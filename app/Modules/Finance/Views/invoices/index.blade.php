@extends('core::layouts.app')
@section('title', 'Invoices')
@section('page_title', 'Invoices')

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div><h3 class="text-lg font-semibold text-gray-900">Invoices</h3><p class="text-sm text-gray-500">Student fee invoices</p></div>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"><i class="fas fa-plus"></i> New Invoice</a>
    </div>
    @if($invoices->count())
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                <tr><th class="px-5 py-3 text-left">Invoice #</th><th class="px-5 py-3 text-left">Student</th><th class="px-5 py-3 text-left">Total</th><th class="px-5 py-3 text-left">Paid</th><th class="px-5 py-3 text-left">Balance</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($invoices as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-gray-600">{{ $inv->invoice_number }}</td>
                    <td class="px-5 py-3 text-gray-900">{{ $inv->student->user->name ?? '—' }}</td>
                    <td class="px-5 py-3 font-medium">Le {{ number_format($inv->total_amount) }}</td>
                    <td class="px-5 py-3 text-green-600">Le {{ number_format($inv->paid_amount) }}</td>
                    <td class="px-5 py-3 text-red-600 font-medium">Le {{ number_format($inv->balance) }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs capitalize {{ $inv->status === 'paid' ? 'bg-green-100 text-green-700' : ($inv->status === 'overdue' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $inv->status }}</span></td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('invoices.show', $inv) }}" class="text-gray-500 hover:text-gray-700 mr-2"><i class="fas fa-eye"></i></a>
                        <form method="POST" action="{{ route('invoices.destroy', $inv) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $invoices->links() }}</div>
    @else
    <div class="py-16 text-center"><i class="fas fa-file-invoice-dollar text-gray-300 text-4xl mb-4"></i><p class="text-gray-500">No invoices yet.</p></div>
    @endif
</div>
@endsection
