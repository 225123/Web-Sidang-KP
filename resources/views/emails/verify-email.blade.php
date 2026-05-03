<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email – Sistem KP Ukrida</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #F5F6F8; font-family: 'Inter', Arial, sans-serif; color: #1a1a2e; }
        .wrapper { max-width: 600px; margin: 40px auto; padding: 0 16px; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 40px 48px; text-align: center; }
        .header-logo { font-size: 22px; font-weight: 900; color: #ffffff; letter-spacing: 3px; text-transform: uppercase; }
        .header-sub { font-size: 12px; color: rgba(255,255,255,0.55); margin-top: 6px; letter-spacing: 1px; }
        .badge { display: inline-block; background: rgba(66,133,244,0.15); border: 1px solid rgba(66,133,244,0.3); color: #93C5FD; font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px; margin-top: 14px; }
        .body { padding: 40px 48px; }
        .greeting { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 12px; }
        .message { font-size: 14px; color: #4a5568; line-height: 1.8; margin-bottom: 32px; }
        .btn-wrap { text-align: center; margin: 32px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #10B981, #059669); color: #ffffff !important; font-size: 15px; font-weight: 700; padding: 16px 40px; border-radius: 10px; text-decoration: none; letter-spacing: 0.5px; }
        .steps { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .steps h3 { font-size: 13px; font-weight: 700; color: #065F46; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .step-item { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px; font-size: 13px; color: #047857; line-height: 1.6; }
        .step-num { background: #10B981; color: white; font-size: 11px; font-weight: 900; width: 20px; height: 20px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
        .divider { border: none; border-top: 1px solid #E5E7EB; margin: 28px 0; }
        .link-fallback { font-size: 12px; color: #9CA3AF; line-height: 1.6; }
        .link-fallback a { color: #10B981; word-break: break-all; }
        .footer { background: #F8FAFC; padding: 24px 48px; text-align: center; font-size: 12px; color: #9CA3AF; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="header-logo">KERJA PRAKTEK</div>
                <div class="header-sub">Universitas Kristen Krida Wacana</div>
                <div class="badge">✉ Verifikasi Email</div>
            </div>

            <!-- Body -->
            <div class="body">
                <div class="greeting">Selamat datang, {{ $notifiable->name }}!</div>
                <p class="message">
                    Akun Anda di Sistem Informasi Kerja Praktek Ukrida telah berhasil dibuat oleh Koordinator KP.
                    Untuk mengaktifkan akun Anda dan mulai mengakses sistem, silakan verifikasi alamat email Anda
                    dengan mengklik tombol di bawah ini.
                </p>

                <div class="btn-wrap">
                    <a href="{{ $verificationUrl }}" class="btn">✓ Verifikasi Email Saya</a>
                </div>

                <div class="steps">
                    <h3>Langkah selanjutnya setelah verifikasi:</h3>
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div>Klik tombol verifikasi di atas untuk mengaktifkan akun Anda.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div>Login menggunakan email dan password yang sudah Anda daftarkan.</div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">3</div>
                        <div>Akses dashboard dan mulai proses pendaftaran Kerja Praktek Anda.</div>
                    </div>
                </div>

                <hr class="divider">

                <p class="link-fallback">
                    Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:<br>
                    <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
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
