@extends('core::layouts.app')
@section('title', 'Payments')
@section('page_title', 'Payments')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Payments</h3><p class="text-sm text-gray-500">Payment records</p></div>
        <a href="{{ route('payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> Record Payment</a>
    </div>
    @if($payments->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Receipt #</th><th class="px-5 py-3 text-left">Student</th><th class="px-5 py-3 text-left">Amount</th><th class="px-5 py-3 text-left">Method</th><th class="px-5 py-3 text-left">Date</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $p->receipt_number }}</td>
                    <td class="px-5 py-3 text-gray-900">{{ $p->student->user->name ?? '—' }}</td>
                    <td class="px-5 py-3 font-medium text-green-700">Le {{ number_format($p->amount) }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs capitalize">{{ str_replace('_',' ',$p->method) }}</span></td>
                    <td class="px-5 py-3 text-gray-500">{{ $p->payment_date->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $payments->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-money-bill-wave text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No payments recorded yet.</p></div>
    @endif
</div>
@endsection
