<?php

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\Payment;
use App\Modules\Student\Models\Student;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('institution_id', auth()->user()->current_institution_id)
            ->with('student.user', 'semester')
            ->latest()
            ->paginate(20);
        return view('finance::invoices.index', compact('invoices'));
    }

    public function create()
    {
        $students = Student::where('institution_id', auth()->user()->current_institution_id)->where('status', 'active')->with('user')->get();
        return view('finance::invoices.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        Invoice::create([
            'institution_id' => auth()->user()->current_institution_id,
            'student_id' => $request->student_id,
            'semester_id' => $request->semester_id ?? 1,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'total_amount' => $request->total_amount,
            'balance' => $request->total_amount,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('student.user', 'items.feeCategory', 'payments');
        return view('finance::invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
