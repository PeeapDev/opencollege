@php $step = 1; @endphp
@extends('install.layout')
@section('title', 'Welcome')
@section('content')
    <h2>Welcome to OpenCollege</h2>
    <p>This wizard will guide you through installing OpenCollege — a free,
    open-source higher-education management system.</p>

    <p>Before you begin, make sure you have:</p>
    <ul>
        <li>A MySQL 8 / MariaDB 10.6+ database created</li>
        <li>Database username and password</li>
        <li>The domain or URL where this instance will be served</li>
    </ul>

    <p>The installer will take about 2 minutes.</p>

    <a href="{{ route('install.requirements') }}" class="btn">Begin &rarr;</a>
@endsection
