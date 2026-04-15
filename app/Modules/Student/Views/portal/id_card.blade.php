@extends('core::layouts.app')
@section('title', 'My ID Card')
@section('page_title', 'Student ID Card')

@section('content')
<div class="max-w-2xl mx-auto">
    @if($idCard)
    <div class="bg-white rounded-xl border p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i> Active</span>
            <span class="text-xs text-gray-400">Expires: {{ $idCard->expiry_date->format('M d, Y') }}</span>
        </div>

        <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-xl"></i></div>
                <div>
                    <h3 class="font-bold text-sm">{{ $institution->name ?? 'OpenCollege' }}</h3>
                    <p class="text-blue-300 text-[10px]">Student Identity Card</p>
                </div>
            </div>
            <div class="flex items-center gap-5">
                <div class="w-20 h-24 bg-white/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-3xl text-white/40"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold">{{ $student->user->name ?? '' }}</h2>
                    <p class="text-blue-200 text-sm">{{ $student->program->name ?? '' }}</p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 mt-3 text-xs">
                        <div><span class="text-blue-300">Matric No.</span><p class="font-mono font-medium">{{ $student->student_id }}</p></div>
                        <div><span class="text-blue-300">Year</span><p>{{ $student->current_year }}</p></div>
                        <div><span class="text-blue-300">Card No.</span><p class="font-mono">{{ $idCard->card_number }}</p></div>
                        <div><span class="text-blue-300">Validity</span><p>{{ $idCard->expiry_date->format('M Y') }}</p></div>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between">
                <div class="bg-white p-2 rounded" id="qrCodeContainer"></div>
                <p class="text-[9px] text-blue-300 max-w-[150px] text-right">This card is the property of {{ $institution->name ?? 'the institution' }}. If found, please return.</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border p-12 text-center">
        <i class="fas fa-id-card text-4xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 mb-2">No ID card has been issued yet</p>
        <p class="text-sm text-gray-400">Please contact your college administration to request an ID card.</p>
    </div>
    @endif
</div>

@if($idCard)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('qrCodeContainer');
    const canvas = document.createElement('canvas');
    canvas.width = 80; canvas.height = 80;
    container.appendChild(canvas);
    if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(canvas, atob('{{ $idCard->qr_code }}'), { width: 80, margin: 0 });
    }
});
</script>
@endpush
@endif
@endsection
