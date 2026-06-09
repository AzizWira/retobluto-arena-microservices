@extends('layouts.admin')

@section('title', 'Detail Lapangan - ARENALO')

@section('content')

@php
    $fieldId = $field['id'] ?? null;
    $status = $field['status'] ?? 'inactive';

    $fieldBadge = match($status) {
        'available' => 'success',
        'maintenance' => 'warning',
        'inactive' => 'secondary',
        default => 'secondary',
    };

    $openTime = !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '-';
    $closeTime = !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '-';

    $deleteFormId = 'delete-field-show-' . $fieldId;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Detail Lapangan</h3>
        <p class="text-muted mb-0">
            Informasi lengkap lapangan dan jadwal booking pada lapangan ini.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.fields.edit', $fieldId) }}" class="btn btn-warning">
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
            class="btn btn-danger"
            data-confirm
            data-form="{{ $deleteFormId }}"
            data-message="Yakin ingin menghapus lapangan {{ $field['name'] ?? '-' }}?"
            data-button-text="Ya, Hapus"
            data-button-class="btn-danger"
        >
            Hapus
        </button>

        <a href="{{ route('admin.fields.index') }}" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                Informasi Lapangan
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="210">ID Lapangan</th>
                        <td>{{ $fieldId ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nama Lapangan</th>
                        <td>{{ $field['name'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Tipe</th>
                        <td>{{ $field['type'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $field['location'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Harga per Jam</th>
                        <td>
                            Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jam Operasional</th>
                        <td>{{ $openTime }} - {{ $closeTime }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $fieldBadge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $field['description'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>
                            {{ isset($field['created_at']) ? substr($field['created_at'], 0, 19) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>
                            {{ isset($field['updated_at']) ? substr($field['updated_at'], 0, 19) : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Update Status Lapangan
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.fields.status', $fieldId) }}">
                    @csrf
                    @method('PATCH')

                    <label class="form-label">Status</label>
                    <select name="status" class="form-select mb-3">
                        @foreach(['available','maintenance','inactive'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                {{ ucfirst($statusOption) }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn btn-primary w-100">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Keterangan Status
            </div>

            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Available</strong>: lapangan dapat dipesan member.</li>
                    <li><strong>Maintenance</strong>: lapangan sedang perawatan.</li>
                    <li><strong>Inactive</strong>: lapangan tidak aktif.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Jadwal Booking Lapangan</span>

            <form method="GET" action="{{ route('admin.fields.show', $fieldId) }}" class="d-flex flex-wrap gap-2">
                <input
                    type="date"
                    name="date"
                    value="{{ $selectedDate }}"
                    class="form-control form-control-sm"
                >

                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['pending','approved','rejected','canceled'] as $bookingStatusOption)
                        <option value="{{ $bookingStatusOption }}" @selected($selectedStatus === $bookingStatusOption)>
                            {{ ucfirst($bookingStatusOption) }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-light btn-sm">
                    Lihat
                </button>
            </form>
        </div>
    </div>

    <div class="card-body">
        <p class="text-muted">
            Menampilkan jadwal booking untuk tanggal:
            <strong>{{ $selectedDate }}</strong>
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Member</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Durasi</th>
                        <th>Harga/Jam</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($schedules as $booking)
                        @php
                            $bookingStatus = $booking['status'] ?? '-';

                            $bookingBadge = match($bookingStatus) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'canceled' => 'secondary',
                                default => 'secondary',
                            };
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $booking['member_name'] ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $booking['member_email'] ?? '-' }}
                                </small>
                            </td>

                            <td>
                                {{ isset($booking['booking_date']) ? substr($booking['booking_date'], 0, 10) : '-' }}
                            </td>

                            <td>
                                {{ isset($booking['start_time']) ? substr($booking['start_time'], 0, 5) : '-' }}
                                -
                                {{ isset($booking['end_time']) ? substr($booking['end_time'], 0, 5) : '-' }}
                            </td>

                            <td>
                                {{ $booking['duration_hours'] ?? '-' }} jam
                            </td>

                            <td>
                                Rp {{ number_format($booking['price_per_hour'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                Rp {{ number_format($booking['total_price'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $bookingBadge }}">
                                    {{ ucfirst($bookingStatus) }}
                                </span>
                            </td>

                            <td>
                                @if(!empty($booking['id']))
                                    <a
                                        href="{{ route('admin.bookings.show', $booking['id']) }}"
                                        class="btn btn-sm btn-outline-info"
                                    >
                                        Detail
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                Belum ada jadwal booking untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection