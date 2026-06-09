<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin - ARENALO')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Mengikuti gaya admin lama: Bootstrap, navbar biru, container, card, table --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
            ARENALO
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}"
                    >
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('admin.bookings.requests') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.requests') }}"
                    >
                        Booking Request
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('admin.bookings.index') || request()->routeIs('admin.bookings.show') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.index') }}"
                    >
                        Booking
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}"
                        href="{{ route('admin.members.index') }}"
                    >
                        Member
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('admin.fields.*') ? 'active' : '' }}"
                        href="{{ route('admin.fields.index') }}"
                    >
                        Lapangan
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
                        href="{{ route('admin.notifications.index') }}"
                    >
                        Notifikasi
                    </a>
                </li>

            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="text-white small text-end">
                    <div class="fw-semibold">
                        {{ session('user.name') ?? 'Admin' }}
                    </div>
                    <div style="font-size: 11px;">
                        {{ session('user.email') ?? '' }}
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#logoutModal"
                >
                    Logout
                </button>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <strong>Terjadi kesalahan validasi.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

</main>

<footer class="text-center text-muted small py-3">
    TUBES IAE &copy; {{ date('Y') }} - KELOMPOK RETOBLUTO
</footer>

{{-- Modal Logout --}}
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">
                    Konfirmasi Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                Apakah Anda yakin ingin keluar dari sistem?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        Ya, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Global --}}
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmActionModalLabel">
                    Konfirmasi Aksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body" id="confirmActionMessage">
                Apakah Anda yakin ingin melakukan aksi ini?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button" class="btn btn-danger" id="confirmActionButton">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

@stack('modals')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedForm = null;

        const confirmModalElement = document.getElementById('confirmActionModal');
        const confirmMessage = document.getElementById('confirmActionMessage');
        const confirmButton = document.getElementById('confirmActionButton');

        if (confirmModalElement && confirmMessage && confirmButton) {
            const confirmModal = new bootstrap.Modal(confirmModalElement);

            document.querySelectorAll('[data-confirm]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const formId = this.getAttribute('data-form');
                    const message = this.getAttribute('data-message') || 'Apakah Anda yakin ingin melakukan aksi ini?';
                    const buttonText = this.getAttribute('data-button-text') || 'Ya, Lanjutkan';
                    const buttonClass = this.getAttribute('data-button-class') || 'btn-danger';

                    selectedForm = document.getElementById(formId);

                    if (!selectedForm) {
                        console.error('Form tidak ditemukan:', formId);
                        return;
                    }

                    confirmMessage.textContent = message;
                    confirmButton.textContent = buttonText;
                    confirmButton.className = 'btn ' + buttonClass;

                    confirmModal.show();
                });
            });

            confirmButton.addEventListener('click', function () {
                if (selectedForm) {
                    selectedForm.submit();
                }
            });
        }
    });
</script>

@stack('scripts')

</body>
</html>