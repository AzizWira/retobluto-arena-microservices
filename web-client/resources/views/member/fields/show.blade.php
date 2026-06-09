@extends('layouts.member')

@section('title', 'Detail Lapangan - ARENALO')

@section('content')

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
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Detail Lapangan</h3>
        <p class="text-muted mb-0">
            Lihat informasi lapangan dan jadwal booking yang sudah disetujui.
        </p>
    </div>

    <div class="d-flex gap-2">
        @if($status === 'available')
            <a
                href="{{ route('member.bookings.create', ['field_id' => $fieldId]) }}"
                class="btn btn-primary"
            >
                Booking Lapangan
            </a>
        @endif

        <a href="{{ route('member.fields.index') }}" class="btn btn-outline-secondary">
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
                        <th width="210">Nama Lapangan</th>
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
                            <span class="badge bg-{{ $badge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Deskripsi</th>
                        <td>
                            {{ $field['description'] ?? '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                Ringkasan Booking
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Harga/Jam</span>
                    <strong>
                        Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }}
                    </strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Jam Buka</span>
                    <strong>{{ $openTime }}</strong>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span>Jam Tutup</span>
                    <strong>{{ $closeTime }}</strong>
                </div>

                @if($status === 'available')
                    <a
                        href="{{ route('member.bookings.create', ['field_id' => $fieldId]) }}"
                        class="btn btn-primary w-100"
                    >
                        Ajukan Booking
                    </a>
                @else
                    <button class="btn btn-secondary w-100" disabled>
                        Lapangan Tidak Tersedia
                    </button>
                @endif

                <div class="alert alert-info mt-3 mb-0">
                    Booking yang dibuat member akan masuk sebagai <strong>pending</strong>
                    dan menunggu approval admin.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Jadwal Booking Disetujui</span>

            <form method="GET" action="{{ route('member.fields.show', $fieldId) }}" class="d-flex gap-2">
                <input
                    type="date"
                    name="date"
                    value="{{ $selectedDate }}"
                    class="form-control form-control-sm"
                >

                <button class="btn btn-light btn-sm">
                    Lihat
                </button>
            </form>
        </div>
    </div>

    <div class="card-body">
        <p class="text-muted">
            Menampilkan jadwal booking approved untuk tanggal:
            <strong>{{ $selectedDate }}</strong>
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Durasi</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($schedules as $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

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
                                <span class="badge bg-success">
                                    Approved
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                Belum ada booking approved pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="alert alert-warning mt-3 mb-0">
            Pastikan jam booking tidak bertabrakan dengan jadwal approved yang sudah ada.
        </div>
    </div>
</div>

@endsection