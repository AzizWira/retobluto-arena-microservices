@extends('layouts.admin')

@section('title', 'Edit Lapangan - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Edit Lapangan</h3>
        <p class="text-muted mb-0">
            Perbarui data lapangan, harga, status, dan jam operasional.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.fields.show', $field['id']) }}" class="btn btn-info text-white">
            Detail
        </a>

        <a href="{{ route('admin.fields.index') }}" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        Form Edit Lapangan
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.fields.update', $field['id']) }}">
            @csrf
            @method('PUT')

            @include('admin.fields.form', ['field' => $field])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.fields.show', $field['id']) }}" class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    Update Lapangan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection