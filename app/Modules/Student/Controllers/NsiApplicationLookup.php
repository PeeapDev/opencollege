<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Public NSI lookup for admission applications.
 *
 * Given an NSI number, queries the upstream SDSL
 * (Schools District Sierra Leone) API to verify the student exists
 * and check whether they've completed / are enrolled in the senior
 * secondary exam class (SSS 3). Returns a prefillable dataset or an
 * ineligibility verdict.
 *
 * Eligibility rule: a student is eligible for tertiary admission
 * iff ANY of:
 *   - current_class matches SSS 3 / senior pattern
 *   - academic_history has any row flagged exam_class_type='sss3'
 *
 * If not, we report their current class so the applicant knows
 * they must first sit WASSCE before applying.
 */
class NsiApplicationLookup extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'nsi' => 'required|string|max:30',
        ]);

        $nsi = strtoupper(trim($request->input('nsi')));

        $apiUrl = config('services.sdsl.api_url', env('SDSL_API_URL', 'https://gov.school.edu.sl/api'));
        $apiKey = config('services.sdsl.api_key', env('SDSL_API_KEY', ''));

        try {
            $response = Http::timeout(15)
                ->withHeaders(array_filter([
                    'Accept' => 'application/json',
                    'Authorization' => $apiKey ? 'Bearer '.$apiKey : null,
                ]))
                ->get("{$apiUrl}/student/verify-nsi/{$nsi}");
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'code' => 'unreachable',
                'message' => 'Could not reach the national student registry. Please try again shortly.',
            ], 503);
        }

        if ($response->status() === 404) {
            return response()->json([
                'ok' => false,
                'code' => 'not_found',
                'nsi' => $nsi,
                'message' => 'NSI number not found. Double-check the format (SL-YYYY-MM-NNNNN) and try again.',
            ]);
        }

        if (!$response->successful()) {
            return response()->json([
                'ok' => false,
                'code' => 'api_error',
                'message' => 'National registry returned an error (HTTP '.$response->status().'). Please try again later.',
            ], 502);
        }

        $data = $response->json();
        if (($data['status'] ?? null) !== 'verified') {
            return response()->json([
                'ok' => false,
                'code' => $data['status'] ?? 'unknown',
                'message' => $data['message'] ?? 'National registry could not verify this NSI.',
            ]);
        }

        // Eligibility logic
        $currentClass = (string) ($data['current_class'] ?? '');
        $history = $data['academic_history'] ?? [];

        $isSeniorCurrent = $this->matchesSeniorPattern($currentClass);
        $completedWassce = collect($history)->contains(function ($row) {
            return ($row['exam_class_type'] ?? null) === 'sss3' && !empty($row['has_results']);
        });

        $eligible = $isSeniorCurrent || $completedWassce;

        return response()->json([
            'ok' => true,
            'eligible' => $eligible,
            'nsi' => $nsi,
            'student' => [
                'name'          => $data['student_name'] ?? null,
                'first_name'    => $data['first_name'] ?? null,
                'last_name'     => $data['last_name'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender'        => $data['gender'] ?? null,
                'phone'         => $data['phone'] ?? null,
                'email'         => $data['email'] ?? null,
                'photo_url'     => $data['photo_url'] ?? null,
                'current_class' => $currentClass ?: null,
                'school_name'   => $data['school_name'] ?? null,
                'aggregate'     => $data['aggregate_score'] ?? null,
                'completed_wassce' => $completedWassce,
            ],
            'reason' => $eligible
                ? 'Eligible — this student is in or has completed senior secondary (WASSCE level).'
                : 'Not eligible — must complete WASSCE (SSS 3) before applying for tertiary admission.',
        ]);
    }

    private function matchesSeniorPattern(string $className): bool
    {
        $c = strtolower(trim($className));

        // Explicit SSS 3 / SS 3 / Form 6 — strongest match
        if (preg_match('/(^|\s)s{1,2}s\s*3\b/', $c)) return true;
        if (preg_match('/\bform\s*6\b/', $c)) return true;

        // Generic "Senior Secondary" / "Senior High" — common in SL where
        // schools don't always record the specific SSS grade. Registry
        // marking a student as senior secondary is treated as eligible;
        // the college still requires WASSCE certificates at enrolment.
        if (str_contains($c, 'senior')) return true;

        return false;
    }
}
