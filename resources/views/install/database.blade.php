@php $step = 3; @endphp
@extends('install.layout')
@section('title', 'Database')
@section('content')
    <h2>Database Connection</h2>
    <p class="sub">Enter the credentials for the empty database you created for OpenCollege.</p>

    <form method="POST" action="{{ route('install.db.submit') }}">
        @csrf
        <div class="row">
            <div>
                <label>Host</label>
                <input type="text" name="db_host" value="{{ $data['db_host'] ?? '127.0.0.1' }}" required>
            </div>
            <div>
                <label>Port</label>
                <input type="number" name="db_port" value="{{ $data['db_port'] ?? 3306 }}" required>
            </div>
        </div>
        <label>Database name</label>
        <input type="text" name="db_database" value="{{ $data['db_database'] ?? '' }}" required>
        <label>Username</label>
        <input type="text" name="db_username" value="{{ $data['db_username'] ?? '' }}" required>
        <label>Password</label>
        <input type="password" name="db_password" value="{{ $data['db_password'] ?? '' }}">
        <p class="hint">The installer will test the connection before moving on.</p>
        <button type="submit">Test & Continue &rarr;</button>
    </form>
@endsection
