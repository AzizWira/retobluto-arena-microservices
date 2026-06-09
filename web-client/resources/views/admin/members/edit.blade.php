@extends('layouts.admin')

@section('title', 'Edit Member - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Edit Member</h3>
        <p class="text-muted mb-0">
            Perbarui data profil dan status member.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.members.show', $member['id']) }}" class="btn btn-info text-white">
            Detail
        </a>

        <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="alert alert-info border-0 shadow-sm">
    <strong>Info:</strong>
    Password dan autentikasi utama dikelola oleh Auth Service. Halaman ini mengubah data profil
    dan status member di Member Service.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        Form Edit Member
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.members.update', $member['id']) }}">
            @csrf
            @method('PUT')

            @include('admin.members.form', [
                'member' => $member,
                'mode' => 'edit'
            ])

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.members.show', $member['id']) }}" class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    Update Member
                </button>
            </div>
        </form>
    </div>
</div>

@endsection