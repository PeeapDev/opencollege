@php $step = 2; @endphp
@extends('install.layout')
@section('title', 'Requirements')
@section('content')
    <h2>System Requirements</h2>
    <p class="sub">We're checking that your server meets the minimum requirements.</p>

    <ul class="check-list">
        @foreach($checks as $label => $pass)
            <li>
                <span>{{ $label }}</span>
                <span class="{{ $pass ? 'ok' : 'fail' }}">{{ $pass ? '✓ OK' : '✗ MISSING' }}</span>
            </li>
        @endforeach
    </ul>

    @if($ok)
        <a href="{{ route('install.db') }}" class="btn">Continue &rarr;</a>
    @else
        <p class="errors">Please install/enable the missing extensions or fix permissions, then reload this page.</p>
        <a href="{{ route('install.requirements') }}" class="btn btn-secondary">Re-check</a>
    @endif
@endsection
