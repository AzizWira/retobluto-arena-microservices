@extends('layouts.member')

@section('title', 'Profil Saya - ARENALO')

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
        <h3 class="mb-1">Profil Saya</h3>
        <p class="text-muted mb-0">
            Informasi profil member yang digunakan untuk booking lapangan.
        </p>
    </div>

    <a href="{{ route('member.profile.edit') }}" class="btn btn-primary">
        Edit Profil
    </a>
</div>

@if($status !== 'active')
    <div class="alert alert-warning border-0 shadow-sm">
        <strong>Perhatian:</strong>
        Status member kamu saat ini <strong>{{ ucfirst($status) }}</strong>.
        Booking hanya dapat dilakukan jika status sudah <strong>active</strong>.
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                Informasi Profil
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="220">Nama Member</th>
                        <td>{{ $profile['name'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ $profile['email'] ?? session('user.email') ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $profile['phone'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Alamat</th>
                        <td>{{ $profile['address'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Status Member</th>
                        <td>
                            <span class="badge bg-{{ $badge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>
                            {{ !empty($profile['created_at']) ? str_replace('T', ' ', substr($profile['created_at'], 0, 19)) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>
                            {{ !empty($profile['updated_at']) ? str_replace('T', ' ', substr($profile['updated_at'], 0, 19)) : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Ringkasan Akun
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Status</span>
                    <span class="badge bg-{{ $badge }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Email</span>
                    <strong class="text-end">
                        {{ $profile['email'] ?? session('user.email') ?? '-' }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Role</span>
                    <strong>Member</strong>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Aksi Cepat
            </div>

            <div class="card-body">
                <a href="{{ route('member.profile.edit') }}" class="btn btn-primary w-100 mb-2">
                    Edit Profil
                </a>

                <a href="{{ route('member.bookings.index') }}" class="btn btn-outline-primary w-100 mb-2">
                    Booking Saya
                </a>

                <a href="{{ route('member.fields.index') }}" class="btn btn-outline-secondary w-100">
                    Lihat Lapangan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection