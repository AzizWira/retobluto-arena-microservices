@extends('layouts.admin')

@section('title', 'Detail Notifikasi - ARENALO')

@section('content')

@php
    $status = $log['status'] ?? '-';
    $type = $log['type'] ?? '-';

    $badge = match($status) {
        'sent', 'success' => 'success',
        'pending' => 'warning',
        'failed' => 'danger',
        default => 'secondary',
    };

    $payload = $log['payload'] ?? null;

    if (is_array($payload)) {
        $payloadText = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } elseif (is_string($payload) && $payload !== '') {
        $decoded = json_decode($payload, true);

        $payloadText = json_last_error() === JSON_ERROR_NONE
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : $payload;
    } else {
        $payloadText = '-';
    }
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Detail Notifikasi</h3>
        <p class="text-muted mb-0">
            Informasi lengkap log pengiriman notifikasi.
        </p>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.notifications.createEmail') }}" class="btn btn-primary">
            Kirim Email
        </a>

        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                Informasi Notifikasi
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="220">Log ID</th>
                        <td>{{ $log['id'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Penerima</th>
                        <td>{{ $log['recipient_email'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Tipe</th>
                        <td>
                            <span class="badge bg-secondary">
                                {{ ucwords(str_replace('_', ' ', $type)) }}
                            </span>
                        </td>
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
                        <th>Subject</th>
                        <td>{{ $log['subject'] ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Pesan</th>
                        <td>
                            {!! !empty($log['message']) ? nl2br(e($log['message'])) : '-' !!}
                        </td>
                    </tr>

                    <tr>
                        <th>Error Message</th>
                        <td>
                            @if(!empty($log['error_message']))
                                <span class="text-danger">
                                    {!! nl2br(e($log['error_message'])) !!}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Dikirim Pada</th>
                        <td>
                            {{ !empty($log['sent_at']) ? substr($log['sent_at'], 0, 19) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Dibuat Pada</th>
                        <td>
                            {{ isset($log['created_at']) ? substr($log['created_at'], 0, 19) : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Terakhir Diupdate</th>
                        <td>
                            {{ isset($log['updated_at']) ? substr($log['updated_at'], 0, 19) : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                Payload
            </div>

            <div class="card-body">
                <pre class="bg-light border rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $payloadText }}</pre>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Ringkasan
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Status</span>
                    <span class="badge bg-{{ $badge }}">{{ ucfirst($status) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Tipe</span>
                    <strong>{{ ucwords(str_replace('_', ' ', $type)) }}</strong>
                </div>

                <div class="d-flex justify-content-between">
                    <span>Penerima</span>
                    <strong class="text-end">
                        {{ $log['recipient_email'] ?? '-' }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Aksi Cepat
            </div>

            <div class="card-body">
                <a href="{{ route('admin.notifications.createEmail') }}" class="btn btn-primary w-100 mb-2">
                    Kirim Email Manual
                </a>
                
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary w-100">
                    Semua Log
                </a>
            </div>
        </div>
    </div>
</div>

@endsection