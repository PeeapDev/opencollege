<?php

namespace App\Modules\Core\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ExportController — Data portability for DPG Criterion 6.
 *
 * Provides CSV and JSON exports of every major entity. Admin endpoints are
 * tenant-scoped via the authenticated user's current_institution_id. The
 * student "Download My Data" endpoint returns only the caller's own records.
 *
 * All endpoints honour `format=csv` (default) or `format=json`.
 */
class ExportController extends Controller
{
    public function students(Request $request)
    {
        $institutionId = $this->tenantId($request);
        $rows = Student::where('institution_id', $institutionId)
            ->with('user', 'program.department.faculty')
            ->get()
            ->map(fn ($s) => [
                'student_id'  => $s->student_id,
                'name'        => $s->user->name ?? '',
                'email'       => $s->user->email ?? '',
                'program'     => $s->program->name ?? '',
                'department'  => $s->program->department->name ?? '',
                'faculty'     => $s->program->department->faculty->name ?? '',
                'gender'      => $s->gender,
                'admission_date' => optional($s->admission_date)->toDateString(),
                'status'      => $s->status,
            ]);

        return $this->respond($rows, 'students', $request);
    }

    public function grades(Request $request)
    {
        $institutionId = $this->tenantId($request);
        $rows = DB::table('grades')
            ->join('students', 'students.id', '=', 'grades.student_id')
            ->join('course_sections', 'course_sections.id', '=', 'grades.course_section_id')
            ->join('courses', 'courses.id', '=', 'course_sections.course_id')
            ->where('students.institution_id', $institutionId)
            ->select(
                'students.student_id',
                'courses.code as course_code',
                'courses.name as course_name',
                'grades.score',
                'grades.letter_grade',
                'grades.grade_point',
                'grades.created_at as recorded_at'
            )
            ->get();

        return $this->respond($rows, 'grades', $request);
    }

    public function attendance(Request $request)
    {
        $institutionId = $this->tenantId($request);
        $rows = DB::table('attendances')
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->where('students.institution_id', $institutionId)
            ->select(
                'students.student_id',
                'attendances.date',
                'attendances.status',
                'attendances.course_section_id',
                'attendances.notes'
            )
            ->get();

        return $this->respond($rows, 'attendance', $request);
    }

    public function finance(Request $request)
    {
        $institutionId = $this->tenantId($request);
        $rows = DB::table('invoices')
            ->leftJoin('payments', 'payments.invoice_id', '=', 'invoices.id')
            ->join('students', 'students.id', '=', 'invoices.student_id')
            ->where('students.institution_id', $institutionId)
            ->select(
                'students.student_id',
                'invoices.id as invoice_id',
                'invoices.total',
                'invoices.balance',
                'invoices.due_date',
                'payments.amount as payment_amount',
                'payments.paid_at'
            )
            ->get();

        return $this->respond($rows, 'finance', $request);
    }

    /**
     * "Download My Data" — student data-subject access request.
     * Returns a JSON bundle of everything the system holds about the caller.
     */
    public function myData(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)
            ->with(['user', 'program', 'enrollments', 'grades', 'invoices', 'cgpaRecords'])
            ->first();

        return response()->json([
            'exported_at' => now()->toIso8601String(),
            'user' => $user->only(['id', 'name', 'email', 'created_at']),
            'student' => $student,
        ]);
    }

    // -----------------------------------------------------------------

    private function tenantId(Request $request): int
    {
        $user = $request->user();
        abort_unless($user && $user->current_institution_id, 403);

        return (int) $user->current_institution_id;
    }

    private function respond($rows, string $name, Request $request)
    {
        $format = strtolower((string) $request->query('format', 'csv'));
        if ($format === 'json') {
            return response()->json([
                'entity' => $name,
                'exported_at' => now()->toIso8601String(),
                'count' => is_countable($rows) ? count($rows) : null,
                'data' => $rows,
            ]);
        }

        return $this->stream($rows, $name);
    }

    private function stream($rows, string $name): StreamedResponse
    {
        $filename = $name.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            $first = true;
            foreach ($rows as $row) {
                $row = is_array($row) ? $row : (array) $row;
                if ($first) {
                    fputcsv($out, array_keys($row));
                    $first = false;
                }
                fputcsv($out, array_values($row));
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
