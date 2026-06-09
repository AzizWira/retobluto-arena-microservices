@extends('layouts.admin')

@section('title', 'Log Notifikasi - ARENALO')

@section('content')

@php
    $types = ['email', 'otp', 'booking_created', 'booking_approved', 'booking_rejected', 'register_otp'];
    $statuses = ['pending', 'sent', 'success', 'failed'];

    function notificationBadge($status) {
        return match($status) {
            'sent', 'success' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            default => 'secondary',
        };
    }
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Log Notifikasi</h3>
        <p class="text-muted mb-0">
            Pantau riwayat pengiriman email, OTP, dan notifikasi dari sistem.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.notifications.createEmail') }}" class="btn btn-primary">
            Kirim Email
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari email, subject, pesan..."
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">Tipe Notifikasi</label>
                <select name="type" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>
                            {{ ucwords(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100">
                    Filter
                </button>
            </div>
        </form>

        @if(request()->hasAny(['search', 'type', 'status']))
            <div class="mt-3">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span>Daftar Log Notifikasi</span>
        <span class="badge bg-light text-primary">
            Total: {{ count($logs) }}
        </span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Penerima</th>
                        <th>Tipe</th>
                        <th>Subject</th>
                        <th>Pesan</th>
                        <th>Status</th>
                        <th>Dikirim</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        @php
                            $logId = $log['id'] ?? null;
                            $status = $log['status'] ?? '-';
                            $type = $log['type'] ?? '-';
                            $badge = notificationBadge($status);
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $log['recipient_email'] ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    Log ID: {{ $logId ?? '-' }}
                                </small>
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ ucwords(str_replace('_', ' ', $type)) }}
                                </span>
                            </td>

                            <td>
                                {{ !empty($log['subject']) ? \Illuminate\Support\Str::limit($log['subject'], 45) : '-' }}
                            </td>

                            <td>
                                {{ !empty($log['message']) ? \Illuminate\Support\Str::limit($log['message'], 70) : '-' }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($status) }}
                                </span>

                                @if(!empty($log['error_message']))
                                    <br>
                                    <small class="text-danger">
                                        {{ \Illuminate\Support\Str::limit($log['error_message'], 60) }}
                                    </small>
                                @endif
                            </td>

                            <td>
                                @if(!empty($log['sent_at']))
                                    {{ substr($log['sent_at'], 0, 19) }}
                                @else
                                    <span class="text-muted">Belum terkirim</span>
                                @endif
                            </td>

                            <td>
                                <a
                                    href="{{ route('admin.notifications.show', $logId) }}"
                                    class="btn btn-sm btn-info text-white"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Belum ada log notifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection