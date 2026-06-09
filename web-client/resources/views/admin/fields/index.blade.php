@extends('layouts.admin')

@section('title', 'Data Lapangan - ARENALO')

@section('content')

@php
    $fieldTypes = ['Futsal','Badminton','Basket','Tenis','Mini Soccer','Voli'];
    $statuses = ['available','maintenance','inactive'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Data Lapangan</h3>
        <p class="text-muted mb-0">
            Kelola data lapangan, status, harga, jam operasional, dan detail lapangan.
        </p>
    </div>

    <a href="{{ route('admin.fields.create') }}" class="btn btn-primary">
        + Tambah Lapangan
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.fields.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, tipe, lokasi..."
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">Tipe Lapangan</label>
                <select name="type" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach($fieldTypes as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
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

        @if(request()->hasAny(['search', 'type', 'status']))
            <div class="mt-3">
                <a href="{{ route('admin.fields.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Daftar Lapangan</span>
        <span class="badge bg-light text-primary">
            Total: {{ count($fields) }}
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Lapangan</th>
                        <th>Tipe</th>
                        <th>Lokasi</th>
                        <th>Harga/Jam</th>
                        <th>Jam Operasional</th>
                        <th>Status</th>
                        <th width="280">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($fields as $field)
                        @php
                            $fieldId = $field['id'] ?? null;
                            $status = $field['status'] ?? 'inactive';

                            $badge = match($status) {
                                'available' => 'success',
                                'maintenance' => 'warning',
                                'inactive' => 'secondary',
                                default => 'secondary',
                            };

                            $openTime = !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '-';
                            $closeTime = !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '-';

                            $deleteFormId = 'delete-field-' . $fieldId;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $field['name'] ?? '-' }}
                                </div>

                                @if(!empty($field['description']))
                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($field['description'], 90) }}
                                    </small>
                                @else
                                    <small class="text-muted">
                                        Tidak ada deskripsi.
                                    </small>
                                @endif
                            </td>

                            <td>{{ $field['type'] ?? '-' }}</td>

                            <td>{{ $field['location'] ?? '-' }}</td>

                            <td>
                                Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $openTime }} - {{ $closeTime }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <a
                                        href="{{ route('admin.fields.show', $fieldId) }}"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('admin.fields.edit', $fieldId) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        id="{{ $deleteFormId }}"
                                        method="POST"
                                        action="{{ route('admin.fields.destroy', $fieldId) }}"
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
                                        data-message="Yakin ingin menghapus lapangan {{ $field['name'] ?? '-' }}?"
                                        data-button-text="Ya, Hapus"
                                        data-button-class="btn-danger"
                                    >
                                        Hapus
                                    </button>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('admin.fields.status', $fieldId) }}"
                                >
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
                                Belum ada data lapangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection