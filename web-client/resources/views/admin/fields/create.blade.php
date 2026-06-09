@extends('layouts.admin')

@section('title', 'Tambah Lapangan - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Tambah Lapangan</h3>
        <p class="text-muted mb-0">
            Tambahkan lapangan baru agar dapat dikelola dan ditampilkan untuk member.
        </p>
    </div>

    <a href="{{ route('admin.fields.index') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        Form Tambah Lapangan
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.fields.store') }}">
            @csrf

            @include('admin.fields.form', ['field' => null])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.fields.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    Simpan Lapangan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection