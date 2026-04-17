@php $step = 4; @endphp
@extends('install.layout')
@section('title', 'Site')
@section('content')
    <h2>Site Configuration</h2>
    <p class="sub">Basic details about this installation.</p>

    <form method="POST" action="{{ route('install.site.submit') }}">
        @csrf
        <label>Application name</label>
        <input type="text" name="app_name" value="{{ $data['app_name'] ?? 'OpenCollege' }}" required>

        <label>Application URL</label>
        <input type="url" name="app_url" value="{{ $data['app_url'] ?? url('/') }}" required>
        <p class="hint">Example: https://college.example.edu or http://localhost:8000</p>

        <label>Timezone</label>
        <select name="timezone">
            @php
                $current = $data['timezone'] ?? 'UTC';
                $common = [
                    'UTC',
                    'Europe/London','Europe/Paris','Europe/Berlin','Europe/Madrid',
                    'America/New_York','America/Chicago','America/Denver','America/Los_Angeles','America/Toronto','America/Sao_Paulo',
                    'Africa/Lagos','Africa/Nairobi','Africa/Cairo','Africa/Johannesburg','Africa/Accra','Africa/Freetown','Africa/Dakar',
                    'Asia/Kolkata','Asia/Karachi','Asia/Dubai','Asia/Singapore','Asia/Tokyo','Asia/Shanghai','Asia/Manila','Asia/Dhaka',
                    'Australia/Sydney','Pacific/Auckland',
                ];
            @endphp
            @foreach($common as $tz)
                <option value="{{ $tz }}" @selected($current === $tz)>{{ $tz }}</option>
            @endforeach
        </select>
        <p class="hint">Defaults to UTC. Pick the timezone for your primary campus — can be overridden per institution later.</p>

        <label>Mail "from" address (optional)</label>
        <input type="email" name="mail_from" value="{{ $data['mail_from'] ?? '' }}">
        <p class="hint">Where system emails to students/staff will come from.</p>

        <button type="submit">Continue &rarr;</button>
    </form>
@endsection
