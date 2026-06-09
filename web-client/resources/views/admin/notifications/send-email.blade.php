@extends('layouts.admin')

@section('title', 'Kirim Email - ARENALO')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Kirim Email Manual</h3>
        <p class="text-muted mb-0">
            Kirim email manual melalui Notification Service dan simpan log pengirimannya.
        </p>
    </div>

    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        Form Kirim Email
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.notifications.sendEmail') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">
                    Email Penerima <span class="text-danger">*</span>
                </label>
                <input
                    type="email"
                    name="recipient_email"
                    class="form-control"
                    value="{{ old('recipient_email') }}"
                    placeholder="member@example.com"
                    required
                    autofocus
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Subject <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="subject"
                    class="form-control"
                    value="{{ old('subject') }}"
                    placeholder="Contoh: Informasi Booking ARENALO"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Pesan Email <span class="text-danger">*</span>
                </label>
                <textarea
                    name="message"
                    class="form-control"
                    rows="7"
                    placeholder="Tulis isi pesan email..."
                    required
                >{{ old('message') }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    Kirim Email
                </button>
            </div>
        </form>
    </div>
</div>

@endsection