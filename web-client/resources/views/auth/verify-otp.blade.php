@extends('layouts.auth')

@section('title', 'Verifikasi OTP - ARENALO')

@section('content')

<div class="auth-header">
    <h2>Verifikasi OTP</h2>
    <p>
        Masukkan kode OTP yang dikirim ke email untuk menyelesaikan verifikasi akun member.
    </p>
</div>

{{-- @if(session('otp_debug'))
    <div class="alert alert-success">
        OTP Debug:
        <strong>{{ session('otp_debug') }}</strong>
        <br>
        <small>
            Ditampilkan karena aplikasi masih berjalan dalam mode debug.
        </small>
    </div>
@endif --}}

<form method="POST" action="{{ route('register.verifyOtp') }}">
    @csrf

    <label>Email</label>
    <input
        type="email"
        name="email"
        value="{{ old('email', session('register_email')) }}"
        placeholder="member@example.com"
        required
    >

    <label>Kode OTP</label>
    <input
        type="text"
        name="otp"
        value="{{ old('otp') }}"
        placeholder="Masukkan 6 digit OTP"
        maxlength="6"
        inputmode="numeric"
        required
        autofocus
    >

    <button type="submit" class="btn-auth">
        Verifikasi OTP
    </button>
</form>

<div style="margin-top: 16px; text-align: center;">
    <small style="display: block; margin-bottom: 8px; color: #6c757d;">
        Tidak menerima kode OTP?
    </small>

    <form method="POST" action="{{ route('register.resendOtp') }}">
        @csrf

        <input
            type="hidden"
            name="email"
            value="{{ old('email', session('register_email')) }}"
        >

        <button
            type="submit"
            style="border: none; background: none; color: #0d6efd; text-decoration: underline; cursor: pointer;"
        >
            Minta Kode OTP Ulang
        </button>
    </form>
</div>

<div class="auth-links">
    <a href="{{ route('register.member') }}">
        Kembali ke Register
    </a>
    <br>
    <a href="{{ route('login.member') }}">
        Kembali ke Login Member
    </a>
</div>

@endsection