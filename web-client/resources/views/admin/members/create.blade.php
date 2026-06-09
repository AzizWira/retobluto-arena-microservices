@extends('layouts.admin')

@section('title', 'Tambah Member - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Tambah Member</h3>
        <p class="text-muted mb-0">
            Tambahkan akun member baru melalui admin panel.
        </p>
    </div>

    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="alert alert-info border-0 shadow-sm">
    <strong>Flow:</strong>
    Admin membuat akun member dengan password. Status awal member otomatis <strong>inactive</strong>.
    Saat member login pertama kali, member akan diarahkan ke halaman OTP. Setelah OTP berhasil,
    status member menjadi <strong>active</strong> dan member bisa melakukan booking.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        Form Tambah Member
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.members.store') }}">
            @csrf

            @include('admin.members.form', [
                'member' => null,
                'mode' => 'create'
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    Simpan Member
                </button>
            </div>
        </form>
    </div>
</div>

@endsection