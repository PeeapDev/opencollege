@extends('core::layouts.app')
@section('title', 'ID Cards')
@section('page_title', 'Student ID Cards')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-gray-500 text-sm">{{ $idCards->total() }} ID cards issued</p>
    <div class="flex items-center gap-3">
        <a href="{{ route('qr.scanner') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
            <i class="fas fa-qrcode"></i> QR Scanner
        </a>
        <form method="POST" action="{{ route('id_cards.bulk_generate') }}">@csrf
            <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-magic"></i> Generate All Missing
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Card #</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expiry</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($idCards as $card)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $card->card_number }}</td>
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ $card->student->user->name ?? '—' }}</div>
                    <div class="text-xs text-gray-400">{{ $card->student->student_id ?? '' }}</div>
                </td>
                <td class="px-4 py-3 text-gray-600 text-sm">{{ $card->student->program->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    @php $c = ['active'=>'green','expired'=>'red','revoked'=>'gray','lost'=>'amber'][$card->status] ?? 'gray'; @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ $c }}-100 text-{{ $c }}-700">{{ ucfirst($card->status) }}</span>
                </td>
                <td class="px-4 py-3 text-right text-xs text-gray-500">{{ $card->expiry_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('id_cards.print', $card) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded" title="Print"><i class="fas fa-print text-xs"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No ID cards issued yet</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $idCards->links() }}</div>
@endsection
