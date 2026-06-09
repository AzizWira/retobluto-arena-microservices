@extends('layouts.member')

@section('title', 'Daftar Lapangan - ARENALO')

@section('content')

@php
    $fieldTypes = ['Futsal','Badminton','Basket','Tenis','Mini Soccer','Voli'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Daftar Lapangan</h3>
        <p class="text-muted mb-0">
            Pilih lapangan yang tersedia untuk melakukan booking.
        </p>
    </div>

    <a href="{{ route('member.bookings.create') }}" class="btn btn-primary">
        Buat Booking
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('member.fields.index') }}" class="row g-3">
            <div class="col-md-7">
                <label class="form-label">Pencarian</label>
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari nama lapangan, tipe, atau lokasi..."
                >
            </div>

            <div class="col-md-3">
                <label class="form-label">Tipe Lapangan</label>
                <select name="type" class="form-select">
                    <option value="">Semua Tipe</option>
                    @foreach($fieldTypes as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>
                            {{ $type }}
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

        @if(request()->hasAny(['search', 'type']))
            <div class="mt-3">
                <a href="{{ route('member.fields.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<div class="row g-4">
    @forelse($fields as $field)
        @php
            $fieldId = $field['id'] ?? null;
            $openTime = !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '-';
            $closeTime = !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '-';
        @endphp

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>{{ $field['type'] ?? 'Lapangan' }}</span>
                    <span class="badge bg-light text-primary">
                        Available
                    </span>
                </div>

                <div class="card-body d-flex flex-column">
                    <h5 class="mb-2">{{ $field['name'] ?? '-' }}</h5>

                    <div class="text-muted mb-2">
                        {{ $field['location'] ?? '-' }}
                    </div>

                    <p class="text-muted flex-grow-1">
                        {{ !empty($field['description']) ? \Illuminate\Support\Str::limit($field['description'], 120) : 'Tidak ada deskripsi.' }}
                    </p>

                    <div class="border rounded p-3 bg-light mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga/Jam</span>
                            <strong>
                                Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Jam Operasional</span>
                            <strong>{{ $openTime }} - {{ $closeTime }}</strong>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a
                            href="{{ route('member.fields.show', $fieldId) }}"
                            class="btn btn-outline-primary w-50"
                        >
                            Detail
                        </a>

                        <a
                            href="{{ route('member.bookings.create', ['field_id' => $fieldId]) }}"
                            class="btn btn-primary w-50"
                        >
                            Booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    Belum ada lapangan tersedia.
                </div>
            </div>
        </div>
    @endforelse
</div>

@endsection