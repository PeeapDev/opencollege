@extends('core::layouts.app')
@section('title', 'PeeapPay Transactions')
@section('page_title', 'Online Payment Transactions')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Total</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $stats['successful'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Successful</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
        <p class="text-xs text-gray-500 mt-1">Failed</p>
    </div>
    <div class="bg-white rounded-xl border p-4 text-center">
        <p class="text-lg font-bold text-indigo-600">{{ number_format($stats['total_amount'], 0) }}</p>
        <p class="text-xs text-gray-500 mt-1">Revenue (NLE)</p>
    </div>
</div>

<div class="bg-white rounded-xl border">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">All PeeapPay Transactions</h3>
        <a href="{{ route('payments.index') }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i>All Payments</a>
    </div>
    @if($transactions->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-5 py-2.5 text-left">Reference</th>
                    <th class="px-5 py-2.5 text-left">Invoice</th>
                    <th class="px-5 py-2.5 text-right">Amount</th>
                    <th class="px-5 py-2.5 text-left">Channel</th>
                    <th class="px-5 py-2.5 text-left">Status</th>
                    <th class="px-5 py-2.5 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($transactions as $tx)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs text-blue-600">{{ $tx->reference }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $tx->invoice_id ? '#'.$tx->invoice_id : '—' }}</td>
                    <td class="px-5 py-3 text-right font-mono text-gray-900">{{ number_format($tx->amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $tx->channel ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ in_array($tx->status, ['success','completed','paid']) ? 'bg-green-100 text-green-700' : '' }}
                            {{ $tx->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $tx->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $tx->status === 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $tx->status === 'refunded' ? 'bg-purple-100 text-purple-700' : '' }}
                        ">{{ ucfirst($tx->status) }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ \Carbon\Carbon::parse($tx->created_at)->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $transactions->links() }}</div>
    @else
    <div class="py-16 text-center">
        <i class="fas fa-credit-card text-gray-200 text-4xl mb-3"></i>
        <p class="text-gray-400 text-sm">No online payment transactions yet</p>
    </div>
    @endif
</div>
@endsection
