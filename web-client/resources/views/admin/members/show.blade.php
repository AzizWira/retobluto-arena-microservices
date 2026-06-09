@extends('layouts.admin')

@section('title', 'Detail Member - ARENALO')

@section('content')

@php
    $memberId = $member['id'] ?? null;
    $status = $member['status'] ?? 'inactive';

    $memberBadge = match($status) {
        'active' => 'success',
        'inactive' => 'secondary',
        'blocked' => 'danger',
        default => 'secondary',
    };

    $deleteFormId = 'delete-member-show-' . $memberId;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Detail Member</h3>
        <p class="text-muted mb-0">
            Informasi lengkap member, status member, dan riwayat booking.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.members.edit', $memberId) }}" class="btn btn-warning">
            Edit
        </a>

        <form
            id="{{ $deleteFormId }}"
            method="POST"
            action="{{ route('admin.members.destroy', $memberId) }}"
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
            data-message="Yakin ingin menghapus member {{ $member['name'] ?? '-' }}? Data login di Auth Service tidak otomatis terhapus."
            data-button-text="Ya, Hapus"
            data-button-class="btn-danger"
        >
            Hapus
        </button>

        <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                Informasi Member
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="210">Member ID</th>
                        <td>{{ $memberId ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>User ID Auth</th>
                        <td>{{ $member['user_id'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nama Member</th>
                        <td>{{ $member['name'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td>{{ $member['email'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $member['phone'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Alamat</th>
                        <td>{{ $member['address'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $memberBadge }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>
                            {{ isset($member['created_at']) ? substr($member['created_at'], 0, 19) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>
                            {{ isset($member['updated_at']) ? substr($member['updated_at'], 0, 19) : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Update Status Member
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.members.status', $memberId) }}">
                    @csrf
                    @method('PATCH')

                    <label class="form-label">Status Member</label>
                    <select name="status" class="form-select mb-3">
                        @foreach(['active','inactive','blocked'] as $statusOption)
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
                    <li><strong>Active</strong>: member dapat melakukan booking.</li>
                    <li><strong>Inactive</strong>: member tidak aktif sementara.</li>
                    <li><strong>Blocked</strong>: member diblokir dari sistem.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Riwayat Booking Member</span>
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
                        <th>Harga/Jam</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bookings as $booking)
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
                                Member ini belum memiliki riwayat booking.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection