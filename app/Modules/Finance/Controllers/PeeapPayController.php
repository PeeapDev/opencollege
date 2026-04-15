<?php

namespace App\Modules\Finance\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\Payment;
use App\Modules\Finance\Services\PeeapPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeeapPayController extends Controller
{
    protected PeeapPayService $peeapPay;

    public function __construct(PeeapPayService $peeapPay)
    {
        $this->peeapPay = $peeapPay;
    }

    /**
     * Show payment page for an invoice
     */
    public function payInvoice($invoiceId)
    {
        $invoice = Invoice::where('id', $invoiceId)
            ->where('institution_id', auth()->user()->current_institution_id)
            ->with('student.user', 'items')
            ->firstOrFail();

        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('info', 'This invoice is already fully paid.');
        }

        $pendingTx = DB::table('peeappay_transactions')
            ->where('invoice_id', $invoice->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        return view('finance::peeappay.pay', compact('invoice', 'pendingTx'));
    }

    /**
     * Initialize PeeapPay transaction
     */
    public function initializePayment(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:1',
            'phone' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $student = $invoice->student;
        $user = $student ? $student->user : null;

        $reference = PeeapPayService::generateReference('OC');

        $result = $this->peeapPay->initializePayment([
            'amount' => $request->amount,
            'currency' => 'NLE',
            'email' => $user ? $user->email : null,
            'phone' => $request->phone,
            'description' => "Invoice #{$invoice->invoice_number} — College Fee Payment",
            'reference' => $reference,
            'callback_url' => route('peeappay.webhook'),
            'return_url' => route('peeappay.callback', ['reference' => $reference]),
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'institution_id' => $invoice->institution_id,
            'payment_type' => 'tuition',
        ]);

        // Store transaction record
        DB::table('peeappay_transactions')->insert([
            'institution_id' => $invoice->institution_id,
            'invoice_id' => $invoice->id,
            'student_id' => $invoice->student_id,
            'reference' => $reference,
            'transaction_id' => $result['transaction_id'] ?? null,
            'amount' => $request->amount,
            'currency' => 'NLE',
            'status' => $result['success'] ? 'pending' : 'failed',
            'channel' => null,
            'metadata' => json_encode($result['raw'] ?? []),
            'initiated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($result['success'] && !empty($result['checkout_url'])) {
            return redirect()->away($result['checkout_url']);
        }

        // If PeeapPay is not configured or fails, show manual payment option
        return back()->with('error', $result['message'] ?? 'Could not initialize online payment. Please try manual payment or contact admin.');
    }

    /**
     * Handle return from PeeapPay checkout
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference', $request->query('ref'));

        if (!$reference) {
            return redirect()->route('payments.index')->with('error', 'Invalid payment callback.');
        }

        $tx = DB::table('peeappay_transactions')->where('reference', $reference)->first();
        if (!$tx) {
            return redirect()->route('payments.index')->with('error', 'Transaction not found.');
        }

        // Verify with PeeapPay
        $verification = $this->peeapPay->verifyTransaction($reference);

        if ($verification['success'] && in_array($verification['status'], ['success', 'completed', 'paid'])) {
            $this->completePayment($tx, $verification);
            return redirect()->route('invoices.show', $tx->invoice_id)
                ->with('success', 'Payment successful! Receipt has been generated.');
        }

        DB::table('peeappay_transactions')->where('id', $tx->id)->update([
            'status' => $verification['status'] ?? 'failed',
            'metadata' => json_encode($verification['raw'] ?? $verification),
            'updated_at' => now(),
        ]);

        return redirect()->route('invoices.show', $tx->invoice_id)
            ->with('error', 'Payment verification failed. Status: ' . ($verification['status'] ?? 'unknown') . '. If you were charged, please contact admin.');
    }

    /**
     * PeeapPay webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-PeeapPay-Signature', '');

        if ($signature && !$this->peeapPay->validateWebhookSignature($payload, $signature)) {
            Log::warning('PeeapPay webhook: invalid signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $data = $request->all();
        $reference = $data['reference'] ?? $data['data']['reference'] ?? null;
        $status = $data['status'] ?? $data['data']['status'] ?? null;

        if (!$reference) {
            return response()->json(['status' => 'error', 'message' => 'Missing reference'], 400);
        }

        $tx = DB::table('peeappay_transactions')->where('reference', $reference)->first();
        if (!$tx) {
            Log::warning('PeeapPay webhook: transaction not found', ['reference' => $reference]);
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        if (in_array($status, ['success', 'completed', 'paid'])) {
            $this->completePayment($tx, $data);
        } else {
            DB::table('peeappay_transactions')->where('id', $tx->id)->update([
                'status' => $status ?? 'failed',
                'metadata' => json_encode($data),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Transaction history
     */
    public function transactions()
    {
        $instId = auth()->user()->current_institution_id;
        $transactions = DB::table('peeappay_transactions')
            ->where('institution_id', $instId)
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => DB::table('peeappay_transactions')->where('institution_id', $instId)->count(),
            'successful' => DB::table('peeappay_transactions')->where('institution_id', $instId)->whereIn('status', ['success', 'completed', 'paid'])->count(),
            'pending' => DB::table('peeappay_transactions')->where('institution_id', $instId)->where('status', 'pending')->count(),
            'failed' => DB::table('peeappay_transactions')->where('institution_id', $instId)->where('status', 'failed')->count(),
            'total_amount' => DB::table('peeappay_transactions')->where('institution_id', $instId)->whereIn('status', ['success', 'completed', 'paid'])->sum('amount'),
        ];

        return view('finance::peeappay.transactions', compact('transactions', 'stats'));
    }

    /**
     * Complete a payment after successful verification
     */
    protected function completePayment($tx, array $verification): void
    {
        // Prevent double-processing
        if (in_array($tx->status, ['success', 'completed', 'paid'])) {
            return;
        }

        DB::transaction(function () use ($tx, $verification) {
            // Update transaction
            DB::table('peeappay_transactions')->where('id', $tx->id)->update([
                'status' => 'success',
                'channel' => $verification['channel'] ?? $verification['data']['channel'] ?? 'peeappay',
                'paid_at' => $verification['paid_at'] ?? now(),
                'metadata' => json_encode($verification['raw'] ?? $verification),
                'updated_at' => now(),
            ]);

            // Create payment record
            $invoice = Invoice::find($tx->invoice_id);
            if ($invoice) {
                $payment = Payment::create([
                    'institution_id' => $tx->institution_id,
                    'invoice_id' => $tx->invoice_id,
                    'student_id' => $tx->student_id,
                    'receipt_number' => 'PP-' . strtoupper(substr($tx->reference, 0, 15)),
                    'amount' => $tx->amount,
                    'method' => 'online',
                    'reference' => $tx->reference,
                    'payment_date' => now(),
                    'notes' => 'PeeapPay online payment — Ref: ' . $tx->reference,
                ]);

                $invoice->paid_amount += $tx->amount;
                $invoice->balance = $invoice->total_amount - $invoice->discount - $invoice->paid_amount;
                $invoice->status = $invoice->balance <= 0 ? 'paid' : 'partial';
                $invoice->save();
            }
        });
    }
}
