<?php

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Payment;
use App\Modules\Finance\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('institution_id', auth()->user()->current_institution_id)
            ->with('student.user', 'invoice')
            ->latest()
            ->paginate(20);
        return view('finance::payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::where('institution_id', auth()->user()->current_institution_id)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->with('student.user')
            ->get();
        return view('finance::payments.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,bank_transfer,mobile_money,cheque,online,scholarship',
            'payment_date' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        $payment = Payment::create([
            'institution_id' => auth()->user()->current_institution_id,
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'receipt_number' => 'RCP-' . strtoupper(uniqid()),
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'payment_date' => $request->payment_date,
            'received_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        // Update invoice
        $invoice->paid_amount += $payment->amount;
        $invoice->balance = $invoice->total_amount - $invoice->discount - $invoice->paid_amount;
        $invoice->status = $invoice->balance <= 0 ? 'paid' : 'partial';
        $invoice->save();

        return redirect()->route('payments.index')->with('success', 'Payment recorded. Receipt: ' . $payment->receipt_number);
    }
}
