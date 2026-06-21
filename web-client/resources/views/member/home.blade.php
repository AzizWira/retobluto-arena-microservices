@extends('layouts.member')

@section('title', 'Dashboard Member - ARENALO')

@section('content')

@php
    $memberStatus = $profile['status'] ?? 'inactive';

    $memberBadge = match($memberStatus) {
        'active' => 'success',
        'inactive' => 'secondary',
        'blocked' => 'danger',
        default => 'secondary',
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Dashboard Member</h3>
        <p class="text-muted mb-0">
            Selamat datang, {{ $profile['name'] ?? session('user.name') ?? 'Member' }}.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('member.fields.index') }}" class="btn btn-primary">
            Lihat Lapangan
        </a>

        <a href="{{ route('member.bookings.create') }}" class="btn btn-outline-primary">
            Buat Booking
        </a>
    </div>
</div>

@if($profile)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1">{{ $profile['name'] ?? '-' }}</h5>
                <div class="text-muted">
                    {{ $profile['email'] ?? session('user.email') ?? '-' }}
                </div>
                <div class="text-muted small">
                    {{ $profile['phone'] ?? 'Nomor telepon belum diisi' }}
                </div>
            </div>

            <div class="text-end">
                <div class="mb-2">
                    <span class="badge bg-{{ $memberBadge }}">
                        {{ ucfirst($memberStatus) }}
                    </span>
                </div>

                <a href="{{ route('member.profile.show') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Profil
                </a>
            </div>
        </div>
    </div>
@endif

@if($memberStatus !== 'active')
    <div class="alert alert-warning border-0 shadow-sm">
        <strong>Perhatian:</strong>
        Status member kamu saat ini <strong>{{ ucfirst($memberStatus) }}</strong>.
        Booking hanya dapat dilakukan jika status member sudah <strong>active</strong>.
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Lapangan Tersedia</p>
                <h3 class="mb-0">{{ $stats['available_fields'] ?? 0 }}</h3>
                <small class="text-muted">Lapangan aktif untuk booking</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Total Booking</p>
                <h3 class="mb-0">{{ $stats['total_bookings'] ?? 0 }}</h3>
                <small class="text-muted">Semua riwayat booking</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Pending</p>
                <h3 class="mb-0">{{ $stats['pending_bookings'] ?? 0 }}</h3>
                <small class="text-muted">Menunggu approval admin</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted mb-1">Approved</p>
                <h3 class="mb-0">{{ $stats['approved_bookings'] ?? 0 }}</h3>
                <small class="text-muted">Booking disetujui</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Booking Terbaru</span>

                <a href="{{ route('member.bookings.index') }}" class="btn btn-sm btn-light">
                    Lihat Semua
                </a>
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
                                <th>Status</th>
                                <th width="100">Aksi</th>
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
                                        <span class="badge bg-{{ $bookingBadge }}">
                                            {{ ucfirst($bookingStatus) }}
                                        </span>
                                    </td>

                                    <td>
                                        @if(!empty($booking['id']))
                                            <a
                                                href="{{ route('member.bookings.show', $booking['id']) }}"
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
                                    <td colspan="6" class="text-center text-muted py-5">
                                        Belum ada booking.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="{{ route('member.bookings.create') }}" class="btn btn-primary">
                        Buat Booking Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Rekomendasi Pribadi</span>

                <a href="{{ route('member.bookings.index') }}" class="btn btn-sm btn-light">
                    Riwayat
                </a>
            </div>

            <div class="card-body">
                @forelse($personalRecommendedFields as $field)
                    @php
                        $openTime = !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '-';
                        $closeTime = !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '-';
                    @endphp

                    <div class="border rounded p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h6 class="mb-1">{{ $field['name'] ?? '-' }}</h6>

                                <div class="text-muted small">
                                    {{ $field['type'] ?? '-' }} · {{ $field['location'] ?? '-' }}
                                </div>

                                <div class="text-muted small">
                                    {{ $openTime }} - {{ $closeTime }}
                                </div>

                                <div class="fw-semibold mt-1">
                                    Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }} / jam
                                </div>

                                @if(!empty($field['_recommendation_reason']))
                                    <div class="mt-2">
                                        <span class="badge bg-light text-primary border">
                                            {{ $field['_recommendation_reason'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <a
                                href="{{ route('member.fields.show', $field['id']) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        Belum ada rekomendasi pribadi. Buat booking terlebih dahulu agar sistem dapat membaca preferensi kamu.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Lapangan Terpopuler</span>

                <a href="{{ route('member.fields.index') }}" class="btn btn-sm btn-light">
                    Semua
                </a>
            </div>

            <div class="card-body">
                @forelse($globalPopularFields as $field)
                    @php
                        $openTime = !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '-';
                        $closeTime = !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '-';
                    @endphp

                    <div class="border rounded p-3 mb-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h6 class="mb-1">{{ $field['name'] ?? '-' }}</h6>

                                <div class="text-muted small">
                                    {{ $field['type'] ?? '-' }} · {{ $field['location'] ?? '-' }}
                                </div>

                                <div class="text-muted small">
                                    {{ $openTime }} - {{ $closeTime }}
                                </div>

                                <div class="fw-semibold mt-1">
                                    Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }} / jam
                                </div>

                                @if(!empty($field['_booking_count']))
                                    <div class="mt-2">
                                        <span class="badge bg-light text-primary border">
                                            {{ $field['_booking_count'] }} booking approved
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <a
                                href="{{ route('member.fields.show', $field['id']) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        Belum ada data lapangan terpopuler.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection