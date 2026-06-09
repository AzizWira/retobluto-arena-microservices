@extends('layouts.member')

@section('title', 'Booking Saya - ARENALO')

@section('content')

@php
    $statuses = ['pending', 'approved', 'rejected', 'canceled'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Booking Saya</h3>
        <p class="text-muted mb-0">
            Pantau status booking lapangan yang pernah kamu ajukan.
        </p>
    </div>

    <a href="{{ route('member.bookings.create') }}" class="btn btn-primary">
        + Buat Booking
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('member.bookings.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau tipe lapangan..."
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

        @if(request()->hasAny(['search', 'status', 'booking_date']))
            <div class="mt-3">
                <a href="{{ route('member.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Riwayat Booking</span>
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
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Durasi</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th width="230">Aksi</th>
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

                            $cancelFormId = 'cancel-booking-index-' . $bookingId;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $booking['field_name'] ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $booking['field_type'] ?? '-' }}
                                </small>
                            </td>

                            <td>
                                {{ !empty($booking['booking_date']) ? substr($booking['booking_date'], 0, 10) : '-' }}
                            </td>

                            <td>
                                {{ !empty($booking['start_time']) ? substr($booking['start_time'], 0, 5) : '-' }}
                                -
                                {{ !empty($booking['end_time']) ? substr($booking['end_time'], 0, 5) : '-' }}
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

                                @if($status === 'rejected' && !empty($booking['rejection_reason']))
                                    <br>
                                    <small class="text-danger">
                                        {{ \Illuminate\Support\Str::limit($booking['rejection_reason'], 50) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="{{ route('member.bookings.show', $bookingId) }}"
                                        class="btn btn-sm btn-info text-white"
                                    >
                                        Detail
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
                                            class="btn btn-sm btn-danger"
                                            data-confirm
                                            data-form="{{ $cancelFormId }}"
                                            data-message="Yakin ingin membatalkan booking ini?"
                                            data-button-text="Ya, Batalkan"
                                            data-button-class="btn-danger"
                                        >
                                            Cancel
                                        </button>
                                    @else
                                        <span class="text-muted small">
                                            Tidak ada aksi
                                        </span>
                                    @endif
                                </div>
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