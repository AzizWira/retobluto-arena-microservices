@php
    $fieldTypes = ['Futsal','Badminton','Basket','Tenis','Mini Soccer','Voli'];
    $statuses = ['available','maintenance','inactive'];

    $selectedType = old('type', $field['type'] ?? '');
    $selectedStatus = old('status', $field['status'] ?? 'available');

    $openTime = old('open_time', !empty($field['open_time']) ? substr($field['open_time'], 0, 5) : '');
    $closeTime = old('close_time', !empty($field['close_time']) ? substr($field['close_time'], 0, 5) : '');
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            class="form-control"
            value="{{ old('name', $field['name'] ?? '') }}"
            placeholder="Contoh: Lapangan Futsal A"
            required
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">Tipe Lapangan <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="">Pilih Tipe Lapangan</option>
            @foreach($fieldTypes as $type)
                <option value="{{ $type }}" @selected($selectedType === $type)>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Lokasi</label>
        <input
            type="text"
            name="location"
            class="form-control"
            value="{{ old('location', $field['location'] ?? '') }}"
            placeholder="Contoh: Indoor Area 1"
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">Harga per Jam <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input
                type="number"
                name="price_per_hour"
                class="form-control"
                value="{{ old('price_per_hour', $field['price_per_hour'] ?? '') }}"
                min="0"
                step="1000"
                placeholder="Contoh: 100000"
                required
            >
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected($selectedStatus === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">
            Available dapat dipesan oleh member.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Jam Buka</label>
        <input
            type="time"
            name="open_time"
            class="form-control"
            value="{{ $openTime }}"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">Jam Tutup</label>
        <input
            type="time"
            name="close_time"
            class="form-control"
            value="{{ $closeTime }}"
        >
    </div>

    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea
            name="description"
            class="form-control"
            rows="4"
            placeholder="Masukkan deskripsi singkat lapangan"
        >{{ old('description', $field['description'] ?? '') }}</textarea>
    </div>
</div>