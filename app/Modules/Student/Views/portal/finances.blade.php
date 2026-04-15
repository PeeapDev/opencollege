@extends('core::layouts.app')
@section('title', 'My Finances')
@section('page_title', 'Financial Overview')

@section('content')
<div class="max-w-4xl">
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border p-5 text-center">
            <p class="text-2xl font-bold text-red-600">Le {{ number_format($totalOwed) }}</p>
            <p class="text-xs text-gray-500 mt-1">Outstanding Balance</p>
        </div>
        <div class="bg-white rounded-xl border p-5 text-center">
            <p class="text-2xl font-bold text-green-600">Le {{ number_format($totalPaid) }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Paid</p>
        </div>
        <div class="bg-white rounded-xl border p-5 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $invoices->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Invoices</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Due Date</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $invoice->invoice_number ?? $invoice->id }}</td>
                    <td class="px-4 py-3 text-gray-900">{{ $invoice->description ?? 'Invoice' }}</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900">Le {{ number_format($invoice->total_amount) }}</td>
                    <td class="px-4 py-3 text-center">
                        @php $c = ['paid'=>'green','unpaid'=>'red','partial'=>'amber','overdue'=>'red'][$invoice->status] ?? 'gray'; @endphp
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ ucfirst($invoice->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-gray-500">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">No invoices</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
