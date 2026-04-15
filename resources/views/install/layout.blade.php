<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenCollege Installer — @yield('title', 'Setup')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #e2e8f0; margin: 0; padding: 40px 20px; }
        .wrap { max-width: 720px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        h1 { color: #f1f5f9; margin: 0 0 8px; font-size: 28px; }
        h2 { color: #e2e8f0; margin: 32px 0 12px; font-size: 20px; }
        .sub { color: #94a3b8; margin-bottom: 24px; }
        .steps { display: flex; gap: 8px; margin-bottom: 32px; flex-wrap: wrap; }
        .step { flex: 1; min-width: 90px; padding: 10px 12px; background: #334155; color: #cbd5e1; border-radius: 6px; text-align: center; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .step.active { background: #3b82f6; color: #fff; }
        .step.done { background: #10b981; color: #fff; }
        label { display: block; margin: 16px 0 6px; color: #cbd5e1; font-size: 14px; font-weight: 500; }
        input[type=text], input[type=email], input[type=url], input[type=password], input[type=number], select { width: 100%; padding: 10px 12px; background: #0f172a; border: 1px solid #334155; color: #f1f5f9; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus { outline: none; border-color: #3b82f6; }
        .row { display: flex; gap: 12px; }
        .row > div { flex: 1; }
        button, .btn { display: inline-block; padding: 12px 24px; background: #3b82f6; color: #fff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; margin-top: 24px; }
        button:hover, .btn:hover { background: #2563eb; }
        .btn-secondary { background: #475569; }
        .btn-secondary:hover { background: #334155; }
        .check-list { list-style: none; padding: 0; }
        .check-list li { padding: 10px 14px; background: #0f172a; border-radius: 6px; margin: 6px 0; display: flex; justify-content: space-between; }
        .ok { color: #10b981; font-weight: 600; }
        .fail { color: #ef4444; font-weight: 600; }
        .errors { background: #7f1d1d; color: #fecaca; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
        .hint { color: #64748b; font-size: 12px; margin-top: 4px; }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid #334155; border-top-color: #3b82f6; border-radius: 50%; animation: spin 0.8s linear infinite; vertical-align: middle; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="wrap">
    <h1>OpenCollege Setup</h1>
    <p class="sub">Complete these steps to get your instance running.</p>

    @php $step = $step ?? 1; @endphp
    <div class="steps">
        <div class="step @if($step==1) active @elseif($step>1) done @endif">1. Welcome</div>
        <div class="step @if($step==2) active @elseif($step>2) done @endif">2. Requirements</div>
        <div class="step @if($step==3) active @elseif($step>3) done @endif">3. Database</div>
        <div class="step @if($step==4) active @elseif($step>4) done @endif">4. Site</div>
        <div class="step @if($step==5) active @elseif($step>5) done @endif">5. Admin</div>
        <div class="step @if($step==6) active @elseif($step>6) done @endif">6. Finalize</div>
    </div>

    @if($errors->any())
        <div class="errors">
            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
        </div>
    @endif

    @if(session('error'))
        <div class="errors">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>
</body>
</html>
