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
        <p class="hint">Example: https://college.your-domain.edu</p>

        <label>Timezone</label>
        <select name="timezone">
            @foreach(['Africa/Freetown','Africa/Lagos','Africa/Nairobi','UTC','Europe/London','America/New_York'] as $tz)
                <option value="{{ $tz }}" @selected(($data['timezone'] ?? 'Africa/Freetown')===$tz)>{{ $tz }}</option>
            @endforeach
        </select>

        <label>Mail "from" address (optional)</label>
        <input type="email" name="mail_from" value="{{ $data['mail_from'] ?? '' }}">
        <p class="hint">Where system emails to students/staff will come from.</p>

        <button type="submit">Continue &rarr;</button>
    </form>
@endsection
