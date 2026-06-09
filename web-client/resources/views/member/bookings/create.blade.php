@extends('layouts.member')

@section('title', 'Buat Booking - ARENALO')

@section('content')

@php
    $selectedFieldId = old('field_id', $selectedFieldId ?? '');
    $selectedFieldPrice = $selectedField['price_per_hour'] ?? 0;
    $selectedFieldOpen = !empty($selectedField['open_time']) ? substr($selectedField['open_time'], 0, 5) : '';
    $selectedFieldClose = !empty($selectedField['close_time']) ? substr($selectedField['close_time'], 0, 5) : '';
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Buat Booking</h3>
        <p class="text-muted mb-0">
            Ajukan booking lapangan. Booking akan masuk sebagai pending dan menunggu approval admin.
        </p>
    </div>

    <a href="{{ route('member.bookings.index') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                Form Booking Lapangan
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('member.bookings.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">
                            Pilih Lapangan <span class="text-danger">*</span>
                        </label>

                        <select name="field_id" id="field_id" class="form-select" required>
                            <option value="">Pilih lapangan tersedia</option>

                            @foreach($fields as $field)
                                @php
                                    $fieldId = $field['id'] ?? null;
                                    $openTime = !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '';
                                    $closeTime = !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '';
                                @endphp

                                <option
                                    value="{{ $fieldId }}"
                                    data-name="{{ $field['name'] ?? '-' }}"
                                    data-type="{{ $field['type'] ?? '-' }}"
                                    data-location="{{ $field['location'] ?? '-' }}"
                                    data-price="{{ $field['price_per_hour'] ?? 0 }}"
                                    data-open="{{ $openTime }}"
                                    data-close="{{ $closeTime }}"
                                    @selected((string) $selectedFieldId === (string) $fieldId)
                                >
                                    {{ $field['name'] ?? '-' }}
                                    -
                                    {{ $field['type'] ?? '-' }}
                                    -
                                    Rp {{ number_format($field['price_per_hour'] ?? 0, 0, ',', '.') }}/jam
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                Tanggal Booking <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                name="booking_date"
                                class="form-control"
                                value="{{ old('booking_date', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                Jam Mulai <span class="text-danger">*</span>
                            </label>
                            <input
                                type="time"
                                name="start_time"
                                id="start_time"
                                class="form-control"
                                value="{{ old('start_time') }}"
                                required
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                Jam Selesai <span class="text-danger">*</span>
                            </label>
                            <input
                                type="time"
                                name="end_time"
                                id="end_time"
                                class="form-control"
                                value="{{ old('end_time') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Catatan</label>
                        <textarea
                            name="note"
                            class="form-control"
                            rows="4"
                            placeholder="Opsional. Contoh: booking untuk latihan tim"
                        >{{ old('note') }}</textarea>
                    </div>

                    <div class="alert alert-info">
                        <strong>Catatan:</strong>
                        Booking yang kamu buat akan berstatus <strong>pending</strong>.
                        Admin akan melakukan approve atau reject dari dashboard admin.
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('member.bookings.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary" id="submitBookingButton">
                            Ajukan Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                Ringkasan Lapangan
            </div>

            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th>Lapangan</th>
                        <td id="summary_name">
                            {{ $selectedField['name'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Tipe</th>
                        <td id="summary_type">
                            {{ $selectedField['type'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Lokasi</th>
                        <td id="summary_location">
                            {{ $selectedField['location'] ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Harga/Jam</th>
                        <td id="summary_price">
                            Rp {{ number_format($selectedFieldPrice, 0, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jam Buka</th>
                        <td id="summary_open">
                            {{ $selectedFieldOpen ?: '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Jam Tutup</th>
                        <td id="summary_close">
                            {{ $selectedFieldClose ?: '-' }}
                        </td>
                    </tr>
                </table>

                @if(!empty($selectedField['id']))
                    <a
                        href="{{ route('member.fields.show', $selectedField['id']) }}"
                        class="btn btn-sm btn-outline-primary w-100 mt-3"
                    >
                        Lihat Jadwal Lapangan
                    </a>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                Estimasi Biaya
            </div>

            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Durasi</span>
                    <strong id="summary_duration">0 jam</strong>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Harga/Jam</span>
                    <strong id="summary_price_2">
                        Rp {{ number_format($selectedFieldPrice, 0, ',', '.') }}
                    </strong>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <span>Total</span>
                    <strong id="summary_total">
                        Rp 0
                    </strong>
                </div>

                <small class="text-muted d-block mt-3">
                    Perhitungan ini hanya estimasi dari input jam. Validasi akhir tetap dilakukan oleh Booking Service.
                </small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fieldSelect = document.getElementById('field_id');
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const bookingForm = document.querySelector('form[action="{{ route('member.bookings.store') }}"]');
        const submitButton = document.getElementById('submitBookingButton');

        if (bookingForm && submitButton) {
            bookingForm.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitButton.textContent = 'Memproses Booking...';
            });
        }
        
        const rupiah = new Intl.NumberFormat('id-ID');

        function selectedOption() {
            return fieldSelect.options[fieldSelect.selectedIndex];
        }

        function getPrice() {
            const option = selectedOption();
            return Number(option?.dataset?.price || 0);
        }

        function updateFieldSummary() {
            const option = selectedOption();

            document.getElementById('summary_name').textContent = option?.dataset?.name || '-';
            document.getElementById('summary_type').textContent = option?.dataset?.type || '-';
            document.getElementById('summary_location').textContent = option?.dataset?.location || '-';
            document.getElementById('summary_open').textContent = option?.dataset?.open || '-';
            document.getElementById('summary_close').textContent = option?.dataset?.close || '-';

            const price = getPrice();

            document.getElementById('summary_price').textContent = 'Rp ' + rupiah.format(price);
            document.getElementById('summary_price_2').textContent = 'Rp ' + rupiah.format(price);

            updateTotal();
        }

        function timeToMinutes(value) {
            if (!value || !value.includes(':')) {
                return null;
            }

            const [hour, minute] = value.split(':').map(Number);

            return hour * 60 + minute;
        }

        function updateTotal() {
            const price = getPrice();
            const start = timeToMinutes(startInput.value);
            const end = timeToMinutes(endInput.value);

            let duration = 0;

            if (start !== null && end !== null && end > start) {
                duration = (end - start) / 60;
            }

            const total = duration * price;

            document.getElementById('summary_duration').textContent = duration + ' jam';
            document.getElementById('summary_total').textContent = 'Rp ' + rupiah.format(total);
        }

        fieldSelect.addEventListener('change', updateFieldSummary);
        startInput.addEventListener('input', updateTotal);
        endInput.addEventListener('input', updateTotal);

        updateFieldSummary();
    });
</script>
@endpush

@endsection