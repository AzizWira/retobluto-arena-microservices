@extends('layouts.admin')

@section('title', 'Detail Booking - ARENALO')

@section('content')

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

    $approveFormId = 'approve-booking-show-' . $bookingId;
    $rejectModalId = 'rejectBookingShowModal-' . $bookingId;

    $formatDateTime = function ($value) {
        if (empty($value)) {
            return '-';
        }

        return str_replace('T', ' ', substr($value, 0, 19));
    };

    $formatProcessor = function ($value, $type = 'admin') {
        if (empty($value)) {
            return '-';
        }

        if ($type === 'member') {
            return 'Member';
        }

        return 'Admin ID: ' . $value;
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Detail Booking</h3>
        <p class="text-muted mb-0">
            Informasi lengkap booking, member, lapangan, status, dan catatan proses.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
            Kembali
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
                class="btn btn-success"
                data-confirm
                data-form="{{ $approveFormId }}"
                data-message="Approve booking ini?"
                data-button-text="Ya, Approve"
                data-button-class="btn-success"
            >
                Approve
            </button>

            <button
                type="button"
                class="btn btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#{{ $rejectModalId }}"
            >
                Reject
            </button>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                Informasi Booking
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="230">Booking ID</th>
                        <td>{{ $bookingId ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $badge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Booking</th>
                        <td>
                            {{ isset($booking['booking_date']) ? substr($booking['booking_date'], 0, 10) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jam Booking</th>
                        <td>
                            {{ isset($booking['start_time']) ? substr($booking['start_time'], 0, 5) : '-' }}
                            -
                            {{ isset($booking['end_time']) ? substr($booking['end_time'], 0, 5) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Durasi</th>
                        <td>{{ $booking['duration_hours'] ?? '-' }} jam</td>
                    </tr>

                    <tr>
                        <th>Harga per Jam</th>
                        <td>
                            Rp {{ number_format($booking['price_per_hour'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Total Harga</th>
                        <td>
                            <strong>
                                Rp {{ number_format($booking['total_price'] ?? 0, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>

                    <tr>
                        <th>Catatan Member</th>
                        <td>
                            {!! !empty($booking['note']) ? nl2br(e($booking['note'])) : '-' !!}
                        </td>
                    </tr>

                    <tr>
                        <th>Alasan Reject</th>
                        <td>
                            {!! !empty($booking['rejection_reason']) ? nl2br(e($booking['rejection_reason'])) : '-' !!}
                        </td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>
                            {{ isset($booking['created_at']) ? substr($booking['created_at'], 0, 19) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>
                            {{ isset($booking['updated_at']) ? substr($booking['updated_at'], 0, 19) : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Data Member
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Member ID</th>
                        <td>{{ $booking['member_id'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>User ID</th>
                        <td>{{ $booking['member_user_id'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nama</th>
                        <td>{{ $booking['member_name'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ $booking['member_email'] ?? '-' }}</td>
                    </tr>
                </table>

                @if(!empty($booking['member_id']))
                    <a
                        href="{{ route('admin.members.show', $booking['member_id']) }}"
                        class="btn btn-sm btn-outline-primary w-100 mt-3"
                    >
                        Lihat Member
                    </a>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Data Lapangan
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Field ID</th>
                        <td>{{ $booking['field_id'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nama</th>
                        <td>{{ $booking['field_name'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Tipe</th>
                        <td>{{ $booking['field_type'] ?? '-' }}</td>
                    </tr>
                </table>

                @if(!empty($booking['field_id']))
                    <a
                        href="{{ route('admin.fields.show', $booking['field_id']) }}"
                        class="btn btn-sm btn-outline-primary w-100 mt-3"
                    >
                        Lihat Lapangan
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        Riwayat Proses Booking
    </div>

    <div class="card-body">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Proses</th>
                    <th>Waktu</th>
                    <th>Diproses Oleh</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Dibuat</td>
                    <td>
                        {{ !empty($booking['created_at']) ? str_replace('T', ' ', substr($booking['created_at'], 0, 19)) : '-' }}
                    </td>
                    <td>
                        {{ $booking['member_name'] ?? 'Member' }}
                    </td>
                </tr>

                <tr>
                    <td>Approved</td>
                    <td>
                        {{ !empty($booking['approved_at']) ? str_replace('T', ' ', substr($booking['approved_at'], 0, 19)) : '-' }}
                    </td>
                    <td>
                        {{ !empty($booking['approved_at']) ? ($booking['approved_by'] ?? '-') : '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Rejected</td>
                    <td>
                        {{ !empty($booking['rejected_at']) ? str_replace('T', ' ', substr($booking['rejected_at'], 0, 19)) : '-' }}
                    </td>
                    <td>
                        {{ !empty($booking['rejected_at']) ? ($booking['rejected_by'] ?? '-') : '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Canceled</td>
                    <td>
                        {{ !empty($booking['canceled_at']) ? str_replace('T', ' ', substr($booking['canceled_at'], 0, 19)) : '-' }}
                    </td>
                    <td>
                        {{ !empty($booking['canceled_at']) ? ($booking['canceled_by'] ?? '-') : '-' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

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
                                placeholder="Masukkan alasan reject"
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

@endsection