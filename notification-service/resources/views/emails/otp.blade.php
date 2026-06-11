<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 30px rgba(15,23,42,.10);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#0f172a,#2563eb);padding:28px 32px;color:#ffffff;">
                            <div style="font-size:13px;letter-spacing:.12em;text-transform:uppercase;opacity:.85;">
                                {{ $brand['tagline'] }}
                            </div>
                            <div style="font-size:30px;font-weight:800;margin-top:8px;">
                                {{ $brand['name'] }}
                            </div>
                            <div style="font-size:14px;opacity:.9;margin-top:6px;">
                                {{ $brand['full_name'] }}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:34px 32px;">
                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;color:#111827;">
                                Verifikasi Akun Anda
                            </h1>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#4b5563;">
                                Halo <strong style="color:#111827;">{{ $name }}</strong>, gunakan kode OTP berikut untuk menyelesaikan proses verifikasi akun member Anda.
                            </p>

                            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:16px;padding:24px;text-align:center;margin:26px 0;">
                                <div style="font-size:13px;color:#2563eb;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px;">
                                    Kode OTP
                                </div>

                                <div style="font-size:38px;line-height:1;font-weight:800;letter-spacing:10px;color:#1d4ed8;">
                                    {{ $otp }}
                                </div>

                                <div style="font-size:13px;color:#4b5563;margin-top:14px;">
                                    Berlaku sampai: <strong>{{ $expiredAt }}</strong>
                                </div>
                            </div>

                            <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;padding:16px 18px;margin:0 0 22px;">
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#9a3412;">
                                    Jangan berikan kode OTP ini kepada siapa pun. Tim {{ $brand['name'] }} tidak akan pernah meminta kode OTP Anda.
                                </p>
                            </div>

                            <p style="margin:0;font-size:14px;line-height:1.7;color:#6b7280;">
                                Jika Anda tidak melakukan permintaan ini, abaikan email ini.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafb;padding:20px 32px;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7280;">
                                Email ini dikirim otomatis oleh {{ $brand['full_name'] }}.
                                Mohon tidak membalas email ini.
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;color:#9ca3af;">
                                © {{ date('Y') }} {{ $brand['name'] }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>