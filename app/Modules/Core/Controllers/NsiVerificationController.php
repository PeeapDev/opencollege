<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NsiVerificationController extends Controller
{
    public function index()
    {
        $institutionId = auth()->user()->current_institution_id;
        $verifications = DB::table('nsi_verifications')
            ->where('nsi_verifications.institution_id', $institutionId)
            ->leftJoin('students', 'nsi_verifications.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->select('nsi_verifications.*', 'users.name as linked_student_name')
            ->latest('nsi_verifications.created_at')
            ->paginate(20);

        return view('core::nsi.index', compact('verifications'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'nsi_number' => 'required|string|max:30',
        ]);

        $nsi = trim($request->nsi_number);
        $institutionId = auth()->user()->current_institution_id;

        // Call SDSL API to get full academic history
        $result = $this->callSdslApi($nsi);

        // Store verification record
        $verificationId = DB::table('nsi_verifications')->insertGetId([
            'institution_id' => $institutionId,
            'nsi_number' => $nsi,
            'student_name' => $result['student_name'] ?? null,
            'high_school_name' => $result['school_name'] ?? null,
            'high_school_code' => $result['school_code'] ?? null,
            'graduation_year' => $result['graduation_year'] ?? null,
            'subjects_results' => isset($result['academic_history']) ? json_encode($result['academic_history']) : (isset($result['subjects']) ? json_encode($result['subjects']) : null),
            'aggregate_score' => $result['aggregate_score'] ?? null,
            'verification_status' => $result['status'],
            'verified_at' => $result['status'] === 'verified' ? now() : null,
            'api_response' => json_encode($result),
            'verified_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => $result['status'] === 'verified',
                'data' => $result,
                'verification_id' => $verificationId,
            ]);
        }

        $msg = $result['status'] === 'verified'
            ? "NSI {$nsi} verified: {$result['student_name']} from {$result['school_name']}"
            : "NSI {$nsi}: verification " . ($result['status'] ?? 'failed');

        return back()->with($result['status'] === 'verified' ? 'success' : 'error', $msg);
    }

    public function show($id)
    {
        $verification = DB::table('nsi_verifications')->where('id', $id)->first();
        if (!$verification) abort(404);

        $apiResponse = json_decode($verification->api_response, true);
        $academicHistory = $apiResponse['academic_history'] ?? [];
        $examClasses = $apiResponse['exam_classes'] ?? [];

        return view('core::nsi.show', compact('verification', 'apiResponse', 'academicHistory', 'examClasses'));
    }

    protected function callSdslApi(string $nsi): array
    {
        $sdslApiUrl = config('services.sdsl.api_url', 'https://gov.school.edu.sl/api');
        $sdslApiKey = config('services.sdsl.api_key', '');

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . $sdslApiKey])
                ->get("{$sdslApiUrl}/student/verify-nsi/{$nsi}");

            if ($response->successful()) {
                $data = $response->json();
                return array_merge($data, ['nsi' => $nsi]);
            }

            if ($response->status() === 404) {
                return ['status' => 'not_found', 'nsi' => $nsi, 'message' => 'NSI number not found in SDSL system'];
            }

            return ['status' => 'failed', 'nsi' => $nsi, 'message' => 'API returned status ' . $response->status()];

        } catch (\Exception $e) {
            return [
                'status' => 'pending',
                'nsi' => $nsi,
                'message' => 'SDSL API not reachable. Queued for manual verification. Error: ' . $e->getMessage(),
            ];
        }
    }
}
