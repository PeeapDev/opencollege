@php $step = 5; @endphp
@extends('install.layout')
@section('title', 'Admin')
@section('content')
    <h2>Super Admin Account</h2>
    <p class="sub">This is the first user that can manage the entire platform.</p>

    <form method="POST" action="{{ route('install.admin.submit') }}">
        @csrf
        <label>Full name</label>
        <input type="text" name="name" value="{{ old('name', 'Administrator') }}" required>

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Password (min 8 characters)</label>
        <input type="password" name="password" required>

        <label>Confirm password</label>
        <input type="password" name="password_confirmation" required>

        <label style="display: flex; align-items: center; gap: 8px; margin-top: 20px;">
            <input type="checkbox" name="seed_demo" value="1">
            <span>Seed with demo data (sample college, students, courses) — recommended for first-time evaluation</span>
        </label>

        <button type="submit">Continue &rarr;</button>
    </form>
@endsection
