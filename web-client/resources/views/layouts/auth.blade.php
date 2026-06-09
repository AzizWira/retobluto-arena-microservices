<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ARENALO')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            background:
                linear-gradient(rgba(13, 110, 253, 0.82), rgba(11, 47, 107, 0.9)),
                radial-gradient(circle at top left, #60a5fa, transparent 35%),
                radial-gradient(circle at bottom right, #1e3a8a, transparent 35%);
            color: #111827;
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border-radius: 18px;
            padding: 32px 30px;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.25);
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 22px;
        }

        .auth-brand h1 {
            margin: 0;
            color: #0d6efd;
            font-size: 30px;
            letter-spacing: 1px;
            font-weight: 800;
        }

        .auth-brand span {
            display: block;
            margin-top: 4px;
            color: #6b7280;
            font-size: 13px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-header h2 {
            margin: 0 0 8px;
            font-size: 22px;
            color: #111827;
        }

        .auth-header p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
        }

        input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.14);
        }

        .btn-auth {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px 14px;
            background: #0d6efd;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-auth:hover {
            background: #0b5ed7;
        }

        .auth-links {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
            line-height: 1.7;
        }

        .auth-links a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.4;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .validation-box {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .validation-box ul {
            margin: 8px 0 0;
            padding-left: 18px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 18px;
            color: rgba(255,255,255,.85);
            font-size: 12px;
        }

        @media (max-width: 480px) {
            .auth-page {
                padding: 16px;
            }

            .auth-card {
                padding: 26px 22px;
            }

            .auth-brand h1 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>
    <main class="auth-page">
        <div>
            <section class="auth-card">
                <div class="auth-brand">
                    <h1>ARENALO</h1>
                    <span>Retobluto Arena Booking System</span>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="validation-box">
                        <strong>Terjadi kesalahan validasi.</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </section>

            <div class="auth-footer">
                TUBES IAE &copy; {{ date('Y') }} - KELOMPOK RETOBLUTO
            </div>
        </div>
    </main>
</body>
</html>