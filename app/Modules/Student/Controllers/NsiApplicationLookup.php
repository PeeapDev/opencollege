<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Academic\Models\Program;
use App\Modules\Settings\Models\Institution;
use App\Modules\Student\Models\Admission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public NSI-based admission.
 *
 * The NSI itself is the application: a student enters their NSI,
 * we fetch their identity + academic history from the national
 * registry (SDSL), show the complete record, and they click one
 * button to submit an application — no form to re-fill.
 *
 * Two endpoints:
 *   POST /apply/check-nsi     — fetch + verify + show record
 *   POST /apply/submit-by-nsi — re-verify + create admission row
 *
 * Re-verification on submit prevents tampering: the client could
 * edit the data displayed in the browser, but the server always
 * re-fetches the authoritative record before writing.
 */
class NsiApplicationLookup extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate(['nsi' => 'required|string|max:30']);

        $result = $this->lookup(strtoupper(trim($request->input('nsi'))));

        return response()->json($result, $result['http'] ?? 200)
            ->header('Content-Type', 'application/json');
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'nsi'            => 'required|string|max:30',
            'program_id'     => 'required|integer',
            'institution_id' => 'required|integer',
        ]);

        $institution = Institution::where('id', $request->institution_id)->where('active', true)->first();
        if (!$institution || $institution->id == 1) {
            return response()->json(['ok' => false, 'message' => 'Institution not found'], 404);
        }

        $program = Program::where('id', $request->program_id)->where('institution_id', $institution->id)->first();
        if (!$program) {
            return response()->json(['ok' => false, 'message' => 'Selected program does not belong to this institution'], 422);
        }

        // Re-fetch authoritative NSI record on the server (never trust the browser payload)
        $data = $this->lookup(strtoupper(trim($request->input('nsi'))));
        if (!($data['ok'] ?? false) || empty($data['eligible'])) {
            return response()->json([
                'ok' => false,
                'message' => $data['message'] ?? 'Student is not eligible or NSI is invalid.',
            ], 422);
        }

        $s = $data['student'];

        // Prevent duplicate applications for the same NSI + institution
        $existing = Admission::where('institution_id', $institution->id)
            ->where('nsi_number', $data['nsi'])
            ->whereIn('status', ['pending', 'approved', 'enrolled'])
            ->first();

        if ($existing) {
            return response()->json([
                'ok' => true,
                'duplicate' => true,
                'application_number' => $existing->application_number,
                'status' => $existing->status,
                'message' => "An application for this NSI already exists at {$institution->name} (#{$existing->application_number}, status: {$existing->status}).",
            ]);
        }

        $appNo = 'APP-' . date('Y') . '-' . strtoupper(Str::random(6));

        $admission = Admission::create([
            'institution_id'     => $institution->id,
            'program_id'         => $program->id,
            'application_number' => $appNo,
            'academic_year'      => date('Y') . '/' . (date('Y') + 1),
            'status'             => 'pending',
            'nsi_number'         => $data['nsi'],
            'first_name'         => $s['first_name'] ?: explode(' ', $s['name'])[0] ?? 'Unknown',
            'last_name'          => $s['last_name'] ?: (explode(' ', $s['name'] ?? '')[1] ?? 'Unknown'),
            'email'              => $s['email'] ?: $this->fallbackEmail($data['nsi']),
            'phone'              => $s['phone'],
            'date_of_birth'      => $s['date_of_birth'] ? substr($s['date_of_birth'], 0, 10) : null,
            'gender'             => strtolower($s['gender'] ?? 'other'),
            'nationality'        => 'Sierra Leonean',
            'national_id'        => null,
            'address'            => null,
            'city'               => null,
        ]);

        return response()->json([
            'ok' => true,
            'application_number' => $admission->application_number,
            'message' => "Application submitted! Your application number is {$admission->application_number}. Keep this for tracking.",
            'student' => $s,
        ]);
    }

    // -------------------------------------------------------------------

    /** Fetch + verify + run eligibility. Returns a response payload. */
    private function lookup(string $nsi): array
    {
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
            return ['ok' => false, 'code' => 'unreachable', 'http' => 503,
                    'message' => 'Could not reach the national student registry. Please try again shortly.'];
        }

        if ($response->status() === 404) {
            return ['ok' => false, 'code' => 'not_found', 'nsi' => $nsi,
                    'message' => 'NSI number not found. Double-check the format (SL-YYYY-MM-NNNNN).'];
        }
        if (!$response->successful()) {
            return ['ok' => false, 'code' => 'api_error', 'http' => 502,
                    'message' => 'National registry returned HTTP '.$response->status().'. Please try again later.'];
        }

        $data = $response->json();
        if (($data['status'] ?? null) !== 'verified') {
            return ['ok' => false, 'code' => $data['status'] ?? 'unknown',
                    'message' => $data['message'] ?? 'National registry could not verify this NSI.'];
        }

        $currentClass = (string) ($data['current_class'] ?? '');
        $history      = $data['academic_history'] ?? [];
        $isSeniorNow  = $this->matchesSeniorPattern($currentClass);
        $completedWassce = collect($history)->contains(function ($row) {
            return ($row['exam_class_type'] ?? null) === 'sss3' && !empty($row['has_results']);
        });
        $eligible = $isSeniorNow || $completedWassce;

        return [
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
                'school_code'   => $data['school_code'] ?? null,
                'aggregate'     => $data['aggregate_score'] ?? null,
                'completed_wassce' => $completedWassce,
                'total_classes' => $data['total_classes_attended'] ?? 0,
                'academic_history' => $history,
            ],
            'reason' => $eligible
                ? 'Eligible — this student is in or has completed senior secondary (WASSCE level).'
                : "Not eligible — must complete WASSCE (SSS 3) before applying. Current class: {$currentClass}.",
        ];
    }

    private function matchesSeniorPattern(string $className): bool
    {
        $c = strtolower(trim($className));
        if (preg_match('/(^|\s)s{1,2}s\s*3\b/', $c)) return true;
        if (preg_match('/\bform\s*6\b/', $c)) return true;
        if (str_contains($c, 'senior')) return true;

        return false;
    }

    /** When a student has no email in SDSL, create a placeholder so the admission row validates. */
    private function fallbackEmail(string $nsi): string
    {
        return 'nsi-' . strtolower(str_replace('-', '', $nsi)) . '@pending.opencollege';
    }
}
