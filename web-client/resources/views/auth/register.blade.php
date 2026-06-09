@extends('layouts.auth')

@section('title', 'Register Member - ARENALO')

@section('content')

<div class="auth-header">
    <h2>Register Member</h2>
    <p>
        Buat akun member baru untuk melakukan booking lapangan.
        Kode OTP akan diproses melalui Notification Service.
    </p>
</div>

<form method="POST" action="{{ route('register.requestOtp') }}">
    @csrf

    <label>Nama Lengkap</label>
    <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        placeholder="Masukkan nama lengkap"
        required
        autofocus
    >

    <label>Email</label>
    <input
        type="email"
        name="email"
        value="{{ old('email') }}"
        placeholder="member@example.com"
        required
    >

    <label>Password</label>
    <input
        type="password"
        name="password"
        placeholder="Minimal 6 karakter"
        required
    >

    <button type="submit" class="btn-auth">
        Request OTP
    </button>
</form>

<div class="auth-links">
    <a href="{{ route('login.member') }}">
        Sudah punya akun? Login Member
    </a>
</div>

@endsection