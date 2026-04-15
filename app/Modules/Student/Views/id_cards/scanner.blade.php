@extends('core::layouts.app')
@section('title', 'QR Scanner')
@section('page_title', 'QR Code Scanner')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl border p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-qrcode text-blue-600 text-2xl"></i>
            </div>
            <h3 class="font-semibold text-gray-900">Scan Student ID Card</h3>
            <p class="text-sm text-gray-500">Point camera at QR code or paste QR data</p>
        </div>

        <div class="mb-4">
            <video id="qrVideo" class="w-full rounded-lg bg-gray-900 aspect-video hidden"></video>
            <div id="cameraPlaceholder" class="w-full rounded-lg bg-gray-100 aspect-video flex items-center justify-center">
                <button onclick="startCamera()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    <i class="fas fa-camera mr-2"></i>Open Camera
                </button>
            </div>
        </div>

        <div class="text-center text-xs text-gray-400 mb-4">— or paste QR data manually —</div>

        <div class="flex gap-2">
            <input type="text" id="qrInput" placeholder="Paste QR code data..." class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <button onclick="verifyQR()" class="px-4 py-2.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"><i class="fas fa-search"></i></button>
        </div>

        <div id="result" class="mt-6 hidden"></div>
    </div>
</div>

@push('scripts')
<script>
function verifyQR() {
    const data = document.getElementById('qrInput').value.trim();
    if (!data) return;
    const resultDiv = document.getElementById('result');
    resultDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-500"></i></div>';
    resultDiv.classList.remove('hidden');

    fetch('{{ route("qr.verify") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ qr_data: data })
    })
    .then(r => r.json())
    .then(d => {
        if (d.valid) {
            resultDiv.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-3"><i class="fas fa-check-circle text-green-600"></i><span class="font-semibold text-green-800">Valid ID Card</span></div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><span class="text-gray-400">Name</span><p class="font-medium text-gray-900">${d.student.name}</p></div>
                        <div><span class="text-gray-400">Matric</span><p class="font-mono text-gray-900">${d.student.matric}</p></div>
                        <div><span class="text-gray-400">Program</span><p class="text-gray-900">${d.student.program}</p></div>
                        <div><span class="text-gray-400">Year</span><p class="text-gray-900">${d.student.year}</p></div>
                        <div><span class="text-gray-400">Card #</span><p class="font-mono text-gray-900">${d.student.card_number}</p></div>
                        <div><span class="text-gray-400">Expires</span><p class="text-gray-900">${d.student.expiry}</p></div>
                    </div>
                </div>`;
        } else {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center">
                    <i class="fas fa-times-circle text-red-500 text-2xl mb-2"></i>
                    <p class="font-semibold text-red-800">${d.message}</p>
                </div>`;
        }
    })
    .catch(() => {
        resultDiv.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center text-red-700"><i class="fas fa-exclamation-triangle mr-1"></i> Error verifying QR code</div>';
    });
}

function startCamera() {
    const video = document.getElementById('qrVideo');
    const placeholder = document.getElementById('cameraPlaceholder');
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
    .then(stream => {
        video.srcObject = stream;
        video.play();
        video.classList.remove('hidden');
        placeholder.classList.add('hidden');
    })
    .catch(() => {
        placeholder.innerHTML = '<p class="text-sm text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i>Camera access denied</p>';
    });
}
</script>
@endpush
@endsection
