@extends('layouts.admin')

@section('title', 'Dashboard Admin - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Dashboard Admin</h3>
        <p class="text-muted mb-0">
            Ringkasan data utama sistem booking lapangan ARENALO.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.bookings.requests') }}" class="btn btn-primary">
            Booking Request
        </a>

        <a href="{{ route('admin.fields.create') }}" class="btn btn-outline-primary">
            Tambah Lapangan
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Total Booking</p>
                <h3 class="mb-0">{{ $stats['total_bookings'] }}</h3>
                <small class="text-muted">Semua status booking</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Total Member</p>
                <h3 class="mb-0">{{ $stats['total_members'] }}</h3>
                <small class="text-muted">Member terdaftar</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Total Lapangan</p>
                <h3 class="mb-0">{{ $stats['total_fields'] }}</h3>
                <small class="text-muted">Data lapangan</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Notification Logs</p>
                <h3 class="mb-0">{{ $stats['notification_logs'] }}</h3>
                <small class="text-muted">Riwayat notifikasi</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                Status Lapangan
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Available</span>
                    <span class="badge bg-success">{{ $stats['available_fields'] }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Maintenance</span>
                    <span class="badge bg-warning text-dark">{{ $stats['maintenance_fields'] }}</span>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Inactive</span>
                    <span class="badge bg-secondary">{{ $stats['inactive_fields'] }}</span>
                </div>

                <hr>

                <a href="{{ route('admin.fields.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    Kelola Lapangan
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                Status Member
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Active</span>
                    <span class="badge bg-success">{{ $stats['active_members'] }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Inactive</span>
                    <span class="badge bg-secondary">{{ $stats['inactive_members'] }}</span>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Blocked</span>
                    <span class="badge bg-danger">{{ $stats['blocked_members'] }}</span>
                </div>

                <hr>

                <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    Kelola Member
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                Status Booking
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Pending</span>
                    <span class="badge bg-warning text-dark">{{ $stats['pending_bookings'] }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Approved</span>
                    <span class="badge bg-success">{{ $stats['approved_bookings'] }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Rejected</span>
                    <span class="badge bg-danger">{{ $stats['rejected_bookings'] }}</span>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Canceled</span>
                    <span class="badge bg-secondary">{{ $stats['canceled_bookings'] }}</span>
                </div>

                <hr>

                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary w-100">
                    Kelola Booking
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Booking Request Pending</span>

        <a href="{{ route('admin.bookings.requests') }}" class="btn btn-sm btn-light">
            Lihat Semua
        </a>
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
                        <th>Total Harga</th>
                        <th width="260">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(array_slice($pendingBookings, 0, 10) as $booking)
                        @php
                            $bookingId = $booking['id'] ?? null;
                            $approveFormId = 'approve-booking-dashboard-' . $bookingId;
                            $rejectModalId = 'rejectBookingDashboardModal-' . $bookingId;
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
                                <div class="fw-semibold">
                                    {{ $booking['field_name'] ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $booking['field_type'] ?? '-' }}
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
                                Rp {{ number_format($booking['total_price'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="{{ route('admin.bookings.show', $bookingId) }}"
                                        class="btn btn-sm btn-outline-info"
                                    >
                                        Detail
                                    </a>

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
                                </div>
                            </td>
                        </tr>

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
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Tidak ada booking request pending.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Booking Terbaru</span>

        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light">
            Lihat Semua
        </a>
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
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($latestBookings as $booking)
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
                                <div class="fw-semibold">
                                    {{ $booking['field_name'] ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $booking['field_type'] ?? '-' }}
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
                                Rp {{ number_format($booking['total_price'] ?? 0, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $bookingBadge }}">
                                    {{ ucfirst($bookingStatus) }}
                                </span>
                            </td>

                            <td>
                                <a
                                    href="{{ route('admin.bookings.show', $booking['id']) }}"
                                    class="btn btn-sm btn-outline-info"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
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