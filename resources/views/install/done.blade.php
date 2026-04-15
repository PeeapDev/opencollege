@php $step = 7; @endphp
@extends('install.layout')
@section('title', 'Done')
@section('content')
    <h2 style="color: #10b981;">Installation Complete</h2>
    <p>OpenCollege is ready. You can now log in with the super-admin account
    you created in the previous step.</p>

    <div style="padding: 16px; background: #0f172a; border-radius: 6px; margin: 20px 0;">
        <strong>Important — clean up:</strong>
        <ul>
            <li>A lock file has been written to <code>storage/installed</code> —
                the installer will refuse to run again until you delete it.</li>
            <li>Set <code>APP_DEBUG=false</code> (the installer already did this).</li>
            <li>Schedule <code>php artisan schedule:run</code> in cron for
                background tasks.</li>
            <li>Point your webserver at <code>public/</code> for security.</li>
        </ul>
    </div>

    <a href="{{ $loginUrl }}" class="btn">Go to login &rarr;</a>
@endsection
