@extends('layouts.auth')

@section('title', 'Login Admin - ARENALO')

@section('content')

<div class="auth-header">
    <h2>Login Admin</h2>
    <p>
        Masuk sebagai admin untuk mengelola lapangan, member,
        booking request, booking, dan notifikasi.
    </p>
</div>

<form method="POST" action="{{ route('login.admin.submit') }}">
    @csrf

    <label>Email Admin</label>
    <input
        type="email"
        name="email"
        value="{{ old('email', 'admin@retobluto.test') }}"
        placeholder="admin@retobluto.test"
        required
        autofocus
    >

    <label>Password</label>
    <input
        type="password"
        name="password"
        placeholder="Masukkan password admin"
        required
    >

    <button type="submit" class="btn-auth">
        Login Admin
    </button>
</form>

<div class="auth-links">
    <a href="{{ route('login.member') }}">
        Login sebagai Member
    </a>
</div>

@endsection