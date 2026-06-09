@extends('layouts.auth')

@section('title', 'Login Member - ARENALO')

@section('content')

<div class="auth-header">
    <h2>Login Member</h2>
    <p>
        Masuk sebagai member untuk melihat lapangan, membuat booking,
        dan memantau status booking.
    </p>
</div>

<form method="POST" action="{{ route('login.member.submit') }}">
    @csrf

    <label>Email Member</label>
    <input
        type="email"
        name="email"
        value="{{ old('email') }}"
        placeholder="member@example.com"
        required
        autofocus
    >

    <label>Password</label>
    <input
        type="password"
        name="password"
        placeholder="Masukkan password"
        required
    >

    <button type="submit" class="btn-auth">
        Login Member
    </button>
</form>

<div class="auth-links">
    <a href="{{ route('register.member') }}">
        Belum punya akun? Register Member
    </a>
</div>

@endsection