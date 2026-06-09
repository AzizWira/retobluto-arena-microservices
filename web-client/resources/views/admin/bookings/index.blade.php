@extends('layouts.admin')

@section('title', 'Data Booking - ARENALO')

@section('content')

@php
    $statuses = ['pending', 'approved', 'rejected', 'canceled'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Data Booking</h3>
        <p class="text-muted mb-0">
            Kelola dan pantau seluruh data booking lapangan.
        </p>
    </div>

    <a href="{{ route('admin.bookings.requests') }}" class="btn btn-primary">
        Booking Request Pending
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari member, email, lapangan..."
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">Status Booking</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Tanggal Booking</label>
                <input
                    type="date"
                    name="booking_date"
                    class="form-control"
                    value="{{ request('booking_date') }}"
                >
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100">
                    Filter
                </button>
            </div>
        </form>

        @if(request()->hasAny(['search', 'status', 'booking_date', 'field_id', 'member_id']))
            <div class="mt-3">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Daftar Booking</span>
        <span class="badge bg-light text-primary">
            Total: {{ count($bookings) }}
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Member</th>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Durasi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th width="250">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $bookingId = $booking['id'] ?? null;
                            $status = $booking['status'] ?? '-';

                            $badge = match($status) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'canceled' => 'secondary',
                                default => 'secondary',
                            };

                            $approveFormId = 'approve-booking-index-' . $bookingId;
                            $rejectModalId = 'rejectBookingIndexModal-' . $bookingId;
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

                                <br>

                                <small class="text-muted">
                                    Member ID: {{ $booking['member_id'] ?? '-' }}
                                </small>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $booking['field_name'] ?? '-' }}
                                </div>

                                <small class="text-muted">
                                    {{ $booking['field_type'] ?? '-' }}
                                </small>

                                <br>

                                <small class="text-muted">
                                    Field ID: {{ $booking['field_id'] ?? '-' }}
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
                                Rp {{ number_format($booking['total_price'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="{{ route('admin.bookings.show', $bookingId) }}"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Detail
                                    </a>

                                    @if($status === 'pending')
                                        <form
                                            id="{{ $approveFormId }}"
                                            method="POST"
                                            action="{{ route('admin.bookings.approve', $bookingId) }}"
                                            class="d-none"
                                        >
                                            @csrf
                                        </form>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            data-confirm
                                            data-form="{{ $approveFormId }}"
                                            data-message="Approve booking dari {{ $booking['member_name'] ?? '-' }} untuk {{ $booking['field_name'] ?? '-' }}?"
                                            data-button-text="Ya, Approve"
                                            data-button-class="btn-success"
                                        >
                                            Approve
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#{{ $rejectModalId }}"
                                        >
                                            Reject
                                        </button>
                                    @else
                                        <span class="text-muted small">
                                            Tidak ada aksi
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if($status === 'pending')
                            @push('modals')
                                <div class="modal fade" id="{{ $rejectModalId }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" action="{{ route('admin.bookings.reject', $bookingId) }}">
                                                @csrf

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Booking</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <p class="mb-2">
                                                        Reject booking dari
                                                        <strong>{{ $booking['member_name'] ?? '-' }}</strong>
                                                        untuk lapangan
                                                        <strong>{{ $booking['field_name'] ?? '-' }}</strong>?
                                                    </p>

                                                    <label class="form-label">Alasan Reject</label>
                                                    <textarea
                                                        name="rejection_reason"
                                                        class="form-control"
                                                        rows="4"
                                                        placeholder="Contoh: Jadwal tidak tersedia atau lapangan sedang maintenance"
                                                    ></textarea>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                        Batal
                                                    </button>

                                                    <button type="submit" class="btn btn-danger">
                                                        Ya, Reject
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endpush
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                Belum ada data booking.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection