<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>Reset Password – Sistem KP Ukrida</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #F5F6F8; font-family: 'Inter', Arial, sans-serif; color: #1a1a2e; }
        .wrapper { max-width: 600px; margin: 40px auto; padding: 0 16px; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 40px 48px; text-align: center; }
        .header-logo { font-size: 22px; font-weight: 900; color: #ffffff; letter-spacing: 3px; text-transform: uppercase; }
        .header-sub { font-size: 12px; color: rgba(255,255,255,0.55); margin-top: 6px; letter-spacing: 1px; }
        .body { padding: 40px 48px; }
        .greeting { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
        .message { font-size: 14px; color: #4a5568; line-height: 1.8; margin-bottom: 32px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #4285F4, #2563EB); color: #ffffff !important; font-size: 15px; font-weight: 700; padding: 16px 40px; border-radius: 10px; text-decoration: none; letter-spacing: 0.5px; }
        .divider { border: none; border-top: 1px solid #E5E7EB; margin: 28px 0; }
        .link-fallback { font-size: 12px; color: #9CA3AF; line-height: 1.6; }
        .link-fallback a { color: #4285F4; word-break: break-all; }
        .note { background: #FFF7ED; border-left: 4px solid #F97316; border-radius: 4px; padding: 14px 18px; font-size: 13px; color: #7C2D12; margin-top: 24px; line-height: 1.6; }
        .footer { background: #F8FAFC; padding: 24px 48px; text-align: center; font-size: 12px; color: #9CA3AF; }
        .footer a { color: #6B7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="header-logo">KERJA PRAKTEK</div>
                <div class="header-sub">Universitas Kristen Krida Wacana</div>
            </div>

            <!-- Body -->
            <div class="body">
                <div class="greeting">Halo, {{ $notifiable->name }}!</div>
                <p class="message">
                    Kami menerima permintaan untuk mereset password akun Anda di Sistem Informasi Kerja Praktek Ukrida.
                    Klik tombol di bawah ini untuk membuat password baru. Tautan ini hanya berlaku selama
                    <strong>{{ $expireMinutes }} menit</strong>.
                </p>

                <div class="btn-wrap" style="text-align: center; margin: 32px 0;">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
                        <tr>
                            <td align="center" style="border-radius: 10px; background-color: #2563EB;">
                                <a href="{{ $resetUrl }}" target="_blank" style="display: block; padding: 16px 40px; font-family: 'Inter', Arial, sans-serif; font-size: 15px; font-weight: bold; color: #ffffff; text-decoration: none; border-radius: 10px; background-color: #2563EB; border: 1px solid #2563EB;">Reset Password Saya</a>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="note">
                    ⚠️ Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah. 
                    Namun jika Anda curiga ada yang mencoba mengakses akun Anda, segera hubungi Koordinator KP.
                </div>

                <hr class="divider">

                <p class="link-fallback" style="font-size: 12px; color: #9CA3AF; line-height: 1.6;">
                    Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:<br>
                    <a href="{{ $resetUrl }}" target="_blank" style="color: #4285F4; word-break: break-all;">{{ $resetUrl }}</a>
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} Sistem KP &bull; Universitas Kristen Krida Wacana</p>
                <p style="margin-top:4px;">Email ini dikirim otomatis oleh sistem, mohon jangan dibalas.</p>
            </div>
        </div>
    </div>
</body>
</html>
