@php $step = 6; @endphp
@extends('install.layout')
@section('title', 'Finalizing')
@section('content')
    <h2>Ready to Install</h2>
    <p class="sub">Click the button below to write the configuration, run migrations,
    and create your super-admin account. This may take up to 60 seconds.</p>

    <div id="status" style="display:none; padding: 20px; background: #0f172a; border-radius: 6px; margin: 20px 0;">
        <span class="spinner"></span>
        <span id="status-text" style="margin-left: 12px;">Installing…</span>
    </div>
    <div id="error-box" class="errors" style="display:none;"></div>

    <button type="button" id="run-btn" class="btn">Install OpenCollege &rarr;</button>

    <script>
        document.getElementById('run-btn').addEventListener('click', async function () {
            this.disabled = true;
            this.style.display = 'none';
            document.getElementById('status').style.display = 'block';

            try {
                const res = await fetch('{{ route('install.run') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                if (data.ok) {
                    document.getElementById('status-text').textContent = 'Done! Redirecting…';
                    setTimeout(() => window.location.href = '{{ route('install.done') }}', 800);
                } else {
                    throw new Error(data.msg || 'Unknown error');
                }
            } catch (e) {
                document.getElementById('status').style.display = 'none';
                const box = document.getElementById('error-box');
                box.textContent = e.message;
                box.style.display = 'block';
                document.getElementById('run-btn').disabled = false;
                document.getElementById('run-btn').style.display = 'inline-block';
            }
        });
    </script>
@endsection
