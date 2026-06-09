@extends('layouts.admin')

@section('title', 'Booking Request - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Booking Request Pending</h3>
        <p class="text-muted mb-0">
            Daftar booking yang menunggu persetujuan admin.
        </p>
    </div>

    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
        Semua Booking
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Daftar Booking Request</span>
        <span class="badge bg-light text-primary">
            Total Pending: {{ count($bookings) }}
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
                        <th>Harga</th>
                        <th>Catatan</th>
                        <th width="270">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $bookingId = $booking['id'] ?? null;
                            $approveFormId = 'approve-booking-request-' . $bookingId;
                            $rejectModalId = 'rejectBookingRequestModal-' . $bookingId;
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
                                <div>
                                    Rp {{ number_format($booking['total_price'] ?? 0, 0, ',', '.') }}
                                </div>

                                <small class="text-muted">
                                    Rp {{ number_format($booking['price_per_hour'] ?? 0, 0, ',', '.') }} / jam
                                </small>
                            </td>

                            <td>
                                {{ !empty($booking['note']) ? \Illuminate\Support\Str::limit($booking['note'], 60) : '-' }}
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a
                                        href="{{ route('admin.bookings.show', $bookingId) }}"
                                        class="btn btn-sm btn-info text-white"
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
                                                    placeholder="Contoh: Jadwal bentrok, lapangan maintenance, atau alasan lain"
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
                            <td colspan="9" class="text-center text-muted py-5">
                                Tidak ada booking request pending.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection