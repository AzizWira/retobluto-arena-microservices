@extends('layouts.admin')

@section('title', 'Data Member - ARENALO')

@section('content')

@php
    $statuses = ['active', 'inactive', 'blocked'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Data Member</h3>
        <p class="text-muted mb-0">
            Kelola data profil member, status akun member, dan riwayat booking member.
        </p>
    </div>

    <a href="{{ route('admin.members.create') }}" class="btn btn-primary">
        + Tambah Akun Member
    </a>
</div>

<div class="alert alert-info border-0 shadow-sm">
    <strong>Info:</strong>
    Member yang dibuat admin akan memiliki akun login, tetapi status awalnya inactive.
    Member harus login dan verifikasi OTP terlebih dahulu agar status berubah menjadi active.
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.members.index') }}" class="row g-3">
            <div class="col-md-7">
                <label class="form-label">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, email, nomor telepon..."
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">Status Member</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100">
                    Filter
                </button>
            </div>
        </form>

        @if(request()->hasAny(['search', 'status']))
            <div class="mt-3">
                <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Daftar Member</span>
        <span class="badge bg-light text-primary">
            Total: {{ count($members) }}
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th width="300">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($members as $member)
                        @php
                            $memberId = $member['id'] ?? null;
                            $status = $member['status'] ?? 'inactive';

                            $badge = match($status) {
                                'active' => 'success',
                                'inactive' => 'secondary',
                                'blocked' => 'danger',
                                default => 'secondary',
                            };

                            $deleteFormId = 'delete-member-' . $memberId;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $member['name'] ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    Member ID: {{ $memberId ?? '-' }}
                                </small>
                            </td>

                            <td>{{ $member['email'] ?? '-' }}</td>

                            <td>{{ $member['phone'] ?? '-' }}</td>

                            <td>
                                {{ !empty($member['address']) ? \Illuminate\Support\Str::limit($member['address'], 60) : '-' }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                {{ isset($member['created_at']) ? substr($member['created_at'], 0, 10) : '-' }}
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <a
                                        href="{{ route('admin.members.show', $memberId) }}"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('admin.members.edit', $memberId) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        id="{{ $deleteFormId }}"
                                        method="POST"
                                        action="{{ route('admin.members.destroy', $memberId) }}"
                                        class="d-none"
                                    >
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        data-confirm
                                        data-form="{{ $deleteFormId }}"
                                        data-message="Yakin ingin menghapus member {{ $member['name'] ?? '-' }}? Pastikan data booking terkait sudah dipertimbangkan."
                                        data-button-text="Ya, Hapus"
                                        data-button-class="btn-danger"
                                    >
                                        Hapus
                                    </button>
                                </div>

                                <form method="POST" action="{{ route('admin.members.status', $memberId) }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="input-group input-group-sm">
                                        <select name="status" class="form-select">
                                            @foreach($statuses as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                                    {{ ucfirst($statusOption) }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button class="btn btn-outline-secondary">
                                            Update
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Belum ada data member.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection