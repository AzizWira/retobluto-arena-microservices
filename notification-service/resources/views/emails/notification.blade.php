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
                        <td style="background:linear-gradient(135deg,#111827,#16a34a);padding:28px 32px;color:#ffffff;">
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
                            <div style="display:inline-block;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;border-radius:999px;padding:7px 13px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px;">
                                {{ $typeLabel }}
                            </div>

                            <h1 style="margin:0 0 12px;font-size:24px;line-height:1.3;color:#111827;">
                                {{ $subject }}
                            </h1>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#4b5563;">
                                Halo <strong style="color:#111827;">{{ $recipientName }}</strong>,
                            </p>

                            <div style="font-size:15px;line-height:1.8;color:#374151;margin-bottom:24px;">
                                {!! nl2br(e($message)) !!}
                            </div>

                            @if (!empty($details))
                                <div style="border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin:24px 0;background:#ffffff;">
                                    <div style="background:#f9fafb;padding:14px 18px;font-size:14px;font-weight:700;color:#111827;border-bottom:1px solid #e5e7eb;">
                                        Detail Informasi
                                    </div>

                                    @foreach ($details as $detail)
                                        <div style="display:block;padding:14px 18px;border-bottom:1px solid #f3f4f6;">
                                            <div style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">
                                                {{ $detail['label'] }}
                                            </div>
                                            <div style="font-size:15px;color:#111827;font-weight:600;">
                                                {{ $detail['value'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:14px;padding:16px 18px;margin-top:24px;">
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#475569;">
                                    Terima kasih telah menggunakan layanan {{ $brand['full_name'] }}.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f9fafb;padding:20px 32px;border-top:1px solid #e5e7eb;">
                            <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7280;">
                                Email ini dikirim oleh sistem {{ $brand['full_name'] }}.
                                Jika merasa tidak melakukan aktivitas terkait, silakan abaikan email ini.
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