@extends('layouts.member')

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

    $cancelFormId = 'cancel-booking-show-' . $bookingId;

    $formatDateTime = function ($value) {
        if (empty($value)) {
            return '-';
        }

        return str_replace('T', ' ', substr($value, 0, 19));
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Detail Booking</h3>
        <p class="text-muted mb-0">
            Informasi lengkap booking yang kamu ajukan.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('member.bookings.index') }}" class="btn btn-outline-secondary">
            Kembali
        </a>

        @if($status === 'pending')
            <form
                id="{{ $cancelFormId }}"
                method="POST"
                action="{{ route('member.bookings.cancel', $bookingId) }}"
                class="d-none"
            >
                @csrf
            </form>

            <button
                type="button"
                class="btn btn-danger"
                data-confirm
                data-form="{{ $cancelFormId }}"
                data-message="Yakin ingin membatalkan booking ini?"
                data-button-text="Ya, Batalkan"
                data-button-class="btn-danger"
            >
                Batalkan Booking
            </button>
        @endif
    </div>
</div>

@if($status === 'pending')
    <div class="alert alert-warning border-0 shadow-sm">
        Booking ini masih menunggu approval admin.
    </div>
@endif

@if($status === 'approved')
    <div class="alert alert-success border-0 shadow-sm">
        Booking ini sudah disetujui admin dan masuk ke jadwal lapangan.
    </div>
@endif

@if($status === 'rejected')
    <div class="alert alert-danger border-0 shadow-sm">
        Booking ini ditolak admin.
        @if(!empty($booking['rejection_reason']))
            <br>
            <strong>Alasan:</strong> {{ $booking['rejection_reason'] }}
        @endif
    </div>
@endif

@if($status === 'canceled')
    <div class="alert alert-secondary border-0 shadow-sm">
        Booking ini sudah dibatalkan.
    </div>
@endif

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
                        <th>Lapangan</th>
                        <td>
                            <div class="fw-semibold">
                                {{ $booking['field_name'] ?? '-' }}
                            </div>
                            <small class="text-muted">
                                {{ $booking['field_type'] ?? '-' }}
                            </small>
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Booking</th>
                        <td>
                            {{ !empty($booking['booking_date']) ? substr($booking['booking_date'], 0, 10) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jam Booking</th>
                        <td>
                            {{ !empty($booking['start_time']) ? substr($booking['start_time'], 0, 5) : '-' }}
                            -
                            {{ !empty($booking['end_time']) ? substr($booking['end_time'], 0, 5) : '-' }}
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
                        <th>Catatan</th>
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
                        <td>{{ $formatDateTime($booking['created_at'] ?? null) }}</td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>{{ $formatDateTime($booking['updated_at'] ?? null) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
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
                        href="{{ route('member.fields.show', $booking['field_id']) }}"
                        class="btn btn-sm btn-outline-primary w-100 mt-3"
                    >
                        Lihat Lapangan
                    </a>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Aksi
            </div>

            <div class="card-body">
                <a href="{{ route('member.bookings.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                    Semua Booking
                </a>

                <a href="{{ route('member.bookings.create') }}" class="btn btn-primary w-100">
                    Buat Booking Baru
                </a>
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
                    <td>{{ $formatDateTime($booking['created_at'] ?? null) }}</td>
                    <td>{{ $booking['member_name'] ?? 'Member' }}</td>
                </tr>

                <tr>
                    <td>Approved</td>
                    <td>{{ $formatDateTime($booking['approved_at'] ?? null) }}</td>
                    <td>
                        {{ !empty($booking['approved_at']) ? ($booking['approved_by'] ?? '-') : '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Rejected</td>
                    <td>{{ $formatDateTime($booking['rejected_at'] ?? null) }}</td>
                    <td>
                        {{ !empty($booking['rejected_at']) ? ($booking['rejected_by'] ?? '-') : '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Canceled</td>
                    <td>{{ $formatDateTime($booking['canceled_at'] ?? null) }}</td>
                    <td>
                        {{ !empty($booking['canceled_at']) ? ($booking['canceled_by'] ?? '-') : '-' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection