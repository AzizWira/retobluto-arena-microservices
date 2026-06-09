@extends('layouts.member')

@section('title', 'Edit Profil - ARENALO')

@section('content')

@php
    $status = $profile['status'] ?? 'inactive';

    $badge = match($status) {
        'active' => 'success',
        'inactive' => 'secondary',
        'blocked' => 'danger',
        default => 'secondary',
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Edit Profil</h3>
        <p class="text-muted mb-0">
            Perbarui data profil member yang digunakan dalam sistem booking.
        </p>
    </div>

    <a href="{{ route('member.profile.show') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="alert alert-info border-0 shadow-sm">
    <strong>Info:</strong>
    Email dan password dikelola oleh Auth Service, sehingga tidak diedit dari halaman ini.
    Halaman ini hanya memperbarui nama, nomor telepon, dan alamat.
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                Form Edit Profil
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('member.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">
                            Nama Member <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $profile['name'] ?? '') }}"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            value="{{ $profile['email'] ?? session('user.email') ?? '' }}"
                            disabled
                        >
                        <small class="text-muted">
                            Email tidak dapat diubah dari halaman profil.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $profile['phone'] ?? '') }}"
                            placeholder="Contoh: 08123456789"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea
                            name="address"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan alamat"
                        >{{ old('address', $profile['address'] ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('member.profile.show') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Update Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Status Member
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Status saat ini</span>
                    <span class="badge bg-{{ $badge }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                @if($status === 'active')
                    <div class="alert alert-success mb-0">
                        Akun kamu aktif dan dapat melakukan booking.
                    </div>
                @elseif($status === 'inactive')
                    <div class="alert alert-warning mb-0">
                        Akun kamu belum aktif. Selesaikan verifikasi OTP atau hubungi admin.
                    </div>
                @elseif($status === 'blocked')
                    <div class="alert alert-danger mb-0">
                        Akun kamu diblokir. Silakan hubungi admin.
                    </div>
                @else
                    <div class="alert alert-secondary mb-0">
                        Status akun tidak diketahui.
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Aksi Cepat
            </div>

            <div class="card-body">
                <a href="{{ route('member.profile.show') }}" class="btn btn-outline-primary w-100 mb-2">
                    Lihat Profil
                </a>

                <a href="{{ route('member.home') }}" class="btn btn-outline-secondary w-100">
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@endsection