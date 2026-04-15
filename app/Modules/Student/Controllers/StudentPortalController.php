<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\IdCard;
use App\Modules\Exam\Models\Grade;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Communication\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $student = $user->student;
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'No student record found.');
        }
        $student->load('program');

        $institutionId = $user->current_institution_id;
        $grades = Grade::where('student_id', $student->id)->with('course', 'examType')->latest()->take(10)->get();
        $invoices = Invoice::where('student_id', $student->id)->latest()->take(5)->get();
        $notices = Notice::where('institution_id', $institutionId)
            ->whereIn('audience', ['all', 'students'])
            ->where('publish_date', '<=', now())
            ->where(function ($q) { $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()); })
            ->latest()
            ->take(5)
            ->get();

        $gpa = $this->calculateGPA($student->id);
        $totalCredits = Grade::where('student_id', $student->id)->sum('credit_hours');
        $outstanding = Invoice::where('student_id', $student->id)->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('total_amount');

        return view('student::portal.dashboard', compact('student', 'grades', 'invoices', 'notices', 'gpa', 'totalCredits', 'outstanding'));
    }

    public function profile()
    {
        $student = auth()->user()->student;
        if (!$student) return redirect()->route('dashboard');
        $student->load('program', 'user');
        return view('student::portal.profile', compact('student'));
    }

    public function results()
    {
        $student = auth()->user()->student;
        if (!$student) return redirect()->route('dashboard');

        $grades = Grade::where('student_id', $student->id)
            ->with('course', 'examType')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($g) => ($g->examType->name ?? 'Unknown') . ' - Semester ' . ($g->semester_id ?? ''));

        $gpa = $this->calculateGPA($student->id);
        return view('student::portal.results', compact('student', 'grades', 'gpa'));
    }

    public function finances()
    {
        $student = auth()->user()->student;
        if (!$student) return redirect()->route('dashboard');

        $invoices = Invoice::where('student_id', $student->id)->with('items', 'payments')->latest()->get();
        $totalOwed = $invoices->whereIn('status', ['unpaid', 'partial', 'overdue'])->sum('total_amount');
        $totalPaid = $invoices->where('status', 'paid')->sum('total_amount');

        return view('student::portal.finances', compact('student', 'invoices', 'totalOwed', 'totalPaid'));
    }

    public function idCard()
    {
        $student = auth()->user()->student;
        if (!$student) return redirect()->route('dashboard');
        $student->load('program', 'user');

        $idCard = IdCard::where('student_id', $student->id)->where('status', 'active')->first();
        $institution = auth()->user()->currentInstitution;

        return view('student::portal.id_card', compact('student', 'idCard', 'institution'));
    }

    // Admin: ID Card Management
    public function idCardList()
    {
        $institutionId = auth()->user()->current_institution_id;
        $idCards = IdCard::where('institution_id', $institutionId)
            ->with('student.user', 'student.program')
            ->latest()
            ->paginate(20);
        return view('student::id_cards.index', compact('idCards'));
    }

    public function generateIdCard(Student $student)
    {
        $existing = IdCard::where('student_id', $student->id)->where('status', 'active')->first();
        if ($existing) {
            return back()->with('error', 'Student already has an active ID card.');
        }

        $cardNumber = 'ID-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT);
        $qrData = json_encode([
            'type' => 'student_id',
            'card' => $cardNumber,
            'student_id' => $student->student_id,
            'name' => $student->user->name ?? '',
            'institution' => $student->institution_id,
        ]);

        IdCard::create([
            'institution_id' => $student->institution_id,
            'student_id' => $student->id,
            'card_number' => $cardNumber,
            'qr_code' => base64_encode($qrData),
            'issued_date' => now(),
            'expiry_date' => now()->addYear(),
            'status' => 'active',
            'issued_by' => auth()->id(),
        ]);

        return back()->with('success', "ID card {$cardNumber} generated for {$student->user->name}.");
    }

    public function bulkGenerateIdCards(Request $request)
    {
        $institutionId = auth()->user()->current_institution_id;
        $students = Student::where('institution_id', $institutionId)
            ->where('status', 'active')
            ->whereDoesntHave('idCard', fn($q) => $q->where('status', 'active'))
            ->get();

        $count = 0;
        foreach ($students as $student) {
            $cardNumber = 'ID-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT);
            $qrData = json_encode([
                'type' => 'student_id',
                'card' => $cardNumber,
                'student_id' => $student->student_id,
                'name' => $student->user->name ?? '',
                'institution' => $student->institution_id,
            ]);

            IdCard::create([
                'institution_id' => $student->institution_id,
                'student_id' => $student->id,
                'card_number' => $cardNumber,
                'qr_code' => base64_encode($qrData),
                'issued_date' => now(),
                'expiry_date' => now()->addYear(),
                'status' => 'active',
                'issued_by' => auth()->id(),
            ]);
            $count++;
        }

        return back()->with('success', "{$count} ID cards generated.");
    }

    public function printIdCard(IdCard $idCard)
    {
        $idCard->load('student.user', 'student.program');
        $institution = \App\Modules\Settings\Models\Institution::find($idCard->institution_id);
        return view('student::id_cards.print', compact('idCard', 'institution'));
    }

    // QR Code Scanner
    public function qrScanner()
    {
        return view('student::id_cards.scanner');
    }

    public function qrVerify(Request $request)
    {
        $request->validate(['qr_data' => 'required|string']);

        try {
            $data = json_decode(base64_decode($request->qr_data), true);
            if (!$data || !isset($data['card'])) {
                return response()->json(['valid' => false, 'message' => 'Invalid QR code']);
            }

            $idCard = IdCard::where('card_number', $data['card'])->with('student.user', 'student.program')->first();
            if (!$idCard) {
                return response()->json(['valid' => false, 'message' => 'ID card not found']);
            }

            if ($idCard->status !== 'active') {
                return response()->json(['valid' => false, 'message' => "ID card is {$idCard->status}"]);
            }

            if ($idCard->expiry_date->isPast()) {
                return response()->json(['valid' => false, 'message' => 'ID card has expired']);
            }

            return response()->json([
                'valid' => true,
                'student' => [
                    'name' => $idCard->student->user->name ?? '',
                    'matric' => $idCard->student->student_id,
                    'program' => $idCard->student->program->name ?? '',
                    'year' => $idCard->student->current_year,
                    'status' => $idCard->student->status,
                    'card_number' => $idCard->card_number,
                    'expiry' => $idCard->expiry_date->format('M d, Y'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'message' => 'Error processing QR code']);
        }
    }

    protected function calculateGPA($studentId): float
    {
        $grades = Grade::where('student_id', $studentId)->get();
        if ($grades->isEmpty()) return 0.0;

        $gradePoints = ['A+' => 4.0, 'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
            'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7, 'D+' => 1.3, 'D' => 1.0, 'F' => 0.0];

        $totalPoints = 0;
        $totalCredits = 0;
        foreach ($grades as $grade) {
            $credits = $grade->credit_hours ?: 3;
            $points = $gradePoints[$grade->letter_grade] ?? 0;
            $totalPoints += $points * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }
}
