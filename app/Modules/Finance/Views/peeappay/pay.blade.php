@extends('core::layouts.app')
@section('title', 'Pay Invoice #' . $invoice->invoice_number)
@section('page_title', 'Online Payment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4"><a href="{{ route('invoices.show', $invoice->id) }}" class="text-sm text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Back to Invoice</a></div>

    <div class="bg-white rounded-xl border p-6 mb-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-credit-card text-green-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">PeeapPay Checkout</h2>
                <p class="text-sm text-gray-500">Secure online payment for Invoice #{{ $invoice->invoice_number }}</p>
            </div>
        </div>

        {{-- Invoice Summary --}}
        <div class="bg-gray-50 rounded-lg p-4 mb-5">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-gray-500">Student</p>
                    <p class="font-medium text-gray-900">{{ $invoice->student->user->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Invoice</p>
                    <p class="font-medium text-gray-900">#{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total Amount</p>
                    <p class="font-medium text-gray-900">{{ number_format($invoice->total_amount, 2) }} NLE</p>
                </div>
                <div>
                    <p class="text-gray-500">Already Paid</p>
                    <p class="font-medium text-green-600">{{ number_format($invoice->paid_amount, 2) }} NLE</p>
                </div>
                <div>
                    <p class="text-gray-500">Discount</p>
                    <p class="font-medium text-gray-900">{{ number_format($invoice->discount, 2) }} NLE</p>
                </div>
                <div>
                    <p class="text-gray-500">Balance Due</p>
                    <p class="font-bold text-red-600 text-lg">{{ number_format($invoice->balance, 2) }} NLE</p>
                </div>
            </div>
        </div>

        {{-- Payment Form --}}
        <form method="POST" action="{{ route('peeappay.initialize') }}" x-data="{ loading: false, amount: {{ $invoice->balance }} }" @submit="loading = true">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Amount (NLE) *</label>
                    <input type="number" name="amount" x-model="amount" step="0.01" min="1" max="{{ $invoice->balance }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-lg font-mono">
                    <p class="text-xs text-gray-400 mt-1">You can make a partial payment (minimum 1.00 NLE)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number (for mobile money)</label>
                    <input type="tel" name="phone" placeholder="e.g. +23276123456"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <button type="submit" :disabled="loading" class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white font-bold rounded-xl transition text-lg">
                    <span x-show="!loading"><i class="fas fa-lock mr-2"></i>Pay <span x-text="parseFloat(amount).toLocaleString('en', {minimumFractionDigits: 2})"></span> NLE</span>
                    <span x-show="loading" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i>Redirecting to PeeapPay...</span>
                </button>
            </div>
        </form>

        <div class="mt-5 flex items-center justify-center gap-4 text-xs text-gray-400">
            <span><i class="fas fa-lock mr-1"></i> SSL Secured</span>
            <span><i class="fas fa-shield-alt mr-1"></i> PeeapPay Protected</span>
            <span><i class="fas fa-mobile-alt mr-1"></i> Mobile Money</span>
        </div>
    </div>

    {{-- Pending Transaction Notice --}}
    @if($pendingTx)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm">
        <p class="font-medium text-amber-800"><i class="fas fa-clock mr-1"></i> Pending Transaction</p>
        <p class="text-amber-700 mt-1">A payment of {{ number_format($pendingTx->amount, 2) }} NLE (Ref: {{ $pendingTx->reference }}) was initiated {{ \Carbon\Carbon::parse($pendingTx->created_at)->diffForHumans() }}. If you already paid, please wait for confirmation before trying again.</p>
    </div>
    @endif
</div>
@endsection
