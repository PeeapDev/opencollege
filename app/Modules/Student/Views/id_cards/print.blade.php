<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card — {{ $idCard->student->user->name ?? '' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <style>
        @media print { body { margin: 0; } .no-print { display: none !important; } }
        .card { width: 340px; height: 215px; }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen gap-4">
<div class="no-print mb-4">
    <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"><i class="fas fa-print mr-1"></i> Print</button>
    <a href="{{ route('id_cards.index') }}" class="px-4 py-2 text-gray-600 text-sm ml-2">Back</a>
</div>

{{-- Front --}}
<div class="card bg-gradient-to-br from-blue-700 to-blue-900 rounded-xl p-4 text-white relative overflow-hidden shadow-xl">
    <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-12 -mt-12"></div>
    <div class="flex items-center gap-2 mb-3">
        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-sm"></i></div>
        <div>
            <h3 class="font-bold text-xs">{{ $institution->name ?? 'OpenCollege' }}</h3>
            <p class="text-blue-300 text-[8px]">Student Identity Card</p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="w-16 h-20 bg-white/10 rounded flex items-center justify-center flex-shrink-0">
            <i class="fas fa-user text-2xl text-white/40"></i>
        </div>
        <div class="flex-1 min-w-0">
            <h2 class="text-sm font-bold truncate">{{ $idCard->student->user->name ?? '' }}</h2>
            <p class="text-blue-200 text-[10px] truncate">{{ $idCard->student->program->name ?? '' }}</p>
            <div class="grid grid-cols-2 gap-x-3 gap-y-0.5 mt-2 text-[9px]">
                <div><span class="text-blue-300">Matric</span><p class="font-mono font-medium">{{ $idCard->student->student_id }}</p></div>
                <div><span class="text-blue-300">Year</span><p>{{ $idCard->student->current_year }}</p></div>
                <div><span class="text-blue-300">Card #</span><p class="font-mono">{{ $idCard->card_number }}</p></div>
                <div><span class="text-blue-300">Valid Till</span><p>{{ $idCard->expiry_date->format('M Y') }}</p></div>
            </div>
        </div>
    </div>
    <div class="mt-2 pt-2 border-t border-white/10 flex items-center justify-between">
        <canvas id="qr" width="50" height="50"></canvas>
        <p class="text-[7px] text-blue-300 max-w-[120px] text-right">Property of {{ $institution->name ?? 'the institution' }}. If found, please return.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof QRCode !== 'undefined') {
        QRCode.toCanvas(document.getElementById('qr'), atob('{{ $idCard->qr_code }}'), { width: 50, margin: 0 });
    }
});
</script>
</body>
</html>
