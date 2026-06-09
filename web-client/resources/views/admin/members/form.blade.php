@php
    $mode = $mode ?? 'create';
    $isCreate = $mode === 'create';

    $statuses = ['active', 'inactive', 'blocked'];
    $selectedStatus = old('status', $member['status'] ?? 'inactive');
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Member <span class="text-danger">*</span></label>
        <input
            type="text"
            name="name"
            class="form-control"
            value="{{ old('name', $member['name'] ?? '') }}"
            placeholder="Contoh: Budi Santoso"
            required
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">Email Member <span class="text-danger">*</span></label>
        <input
            type="email"
            name="email"
            class="form-control"
            value="{{ old('email', $member['email'] ?? '') }}"
            placeholder="member@example.com"
            required
        >
    </div>

    @if($isCreate)
        <div class="col-md-6">
            <label class="form-label">Password Member <span class="text-danger">*</span></label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Minimal 6 karakter"
                required
            >
            <small class="text-muted">
                Password ini digunakan member untuk login pertama kali sebelum verifikasi OTP.
            </small>
        </div>
    @endif

    @if(!$isCreate)
        <div class="col-md-6">
            <label class="form-label">Status Member</label>
            <select name="status" class="form-select">
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected($selectedStatus === $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">
                Status active dibutuhkan agar member bisa melakukan booking.
            </small>
        </div>
    @endif

    <div class="col-md-6">
        <label class="form-label">Nomor Telepon</label>
        <input
            type="text"
            name="phone"
            class="form-control"
            value="{{ old('phone', $member['phone'] ?? '') }}"
            placeholder="Contoh: 08123456789"
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">Alamat</label>
        <textarea
            name="address"
            class="form-control"
            rows="3"
            placeholder="Alamat member"
        >{{ old('address', $member['address'] ?? '') }}</textarea>
    </div>

    @if($isCreate)
        <div class="col-12">
            <div class="alert alert-warning mb-0">
                Setelah disimpan, member belum langsung active. Member harus login menggunakan email dan password
                yang dibuat admin, lalu menyelesaikan verifikasi OTP.
            </div>
        </div>
    @endif
</div>