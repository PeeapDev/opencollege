@extends('core::layouts.app')
@section('title', 'Invoice Details')
@section('page_title', 'Invoice Details')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h3>
                <p class="text-sm text-gray-500">{{ $invoice->student->user->name ?? 'Unknown' }} — {{ $invoice->student->student_id ?? '' }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm capitalize {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $invoice->status }}</span>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
            <div class="bg-gray-50 rounded-lg p-4"><p class="text-gray-500">Total</p><p class="text-lg font-bold">Le {{ number_format($invoice->total_amount) }}</p></div>
            <div class="bg-green-50 rounded-lg p-4"><p class="text-gray-500">Paid</p><p class="text-lg font-bold text-green-700">Le {{ number_format($invoice->paid_amount) }}</p></div>
            <div class="bg-red-50 rounded-lg p-4"><p class="text-gray-500">Balance</p><p class="text-lg font-bold text-red-700">Le {{ number_format($invoice->balance) }}</p></div>
        </div>
        @if($invoice->payments->count())
        <h4 class="font-semibold text-gray-900 mb-2">Payments</h4>
        <div class="space-y-2 mb-4">
            @foreach($invoice->payments as $p)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg text-sm">
                <div><span class="font-mono text-gray-600">{{ $p->receipt_number }}</span> — {{ ucfirst($p->method) }}</div>
                <div><span class="font-medium text-green-700">Le {{ number_format($p->amount) }}</span> <span class="text-gray-400 text-xs">{{ $p->payment_date->format('d M Y') }}</span></div>
            </div>
            @endforeach
        </div>
        @endif
        <div class="flex gap-3">
            @if($invoice->balance > 0)
            <a href="{{ route('peeappay.pay', $invoice->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg"><i class="fas fa-credit-card mr-1"></i>Pay Online</a>
            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg"><i class="fas fa-money-bill mr-1"></i>Record Payment</a>
            @endif
            <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg">Back</a>
        </div>
    </div>
</div>
@endsection
