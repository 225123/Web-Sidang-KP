<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir — Sistem Sidang KP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1d4ed8 100%);
            padding: 1.5rem;
        }
        .card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            max-width: 460px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .icon-wrap {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: rgba(251,191,36,0.15);
            border: 2px solid rgba(251,191,36,0.3);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-wrap svg { width: 40px; height: 40px; color: #fbbf24; }
        h1 { color: #fff; font-size: 1.5rem; font-weight: 800; margin-bottom: 0.75rem; }
        p  { color: rgba(255,255,255,0.65); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; }
        .counter { color: rgba(255,255,255,0.4); font-size: 0.82rem; margin-bottom: 1.5rem; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #2563eb; color: #fff;
            text-decoration: none; border: none; cursor: pointer;
            font-family: inherit; font-size: 0.95rem; font-weight: 700;
            padding: 0.75rem 2rem; border-radius: 10px;
            transition: background 0.2s;
        }
        .btn:hover { background: #1d4ed8; }
        .progress-bar {
            width: 100%; height: 3px; background: rgba(255,255,255,0.1);
            border-radius: 99px; margin-top: 2rem; overflow: hidden;
        }
        .progress-fill {
            height: 100%; background: #2563eb; border-radius: 99px;
            animation: shrink 5s linear forwards;
        }
        @keyframes shrink { from { width: 100%; } to { width: 0%; } }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M12 15v2m0-6v2m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1>Sesi Telah Berakhir</h1>
        <p>Halaman ini sudah kedaluwarsa karena sesi login Anda habis atau tidak aktif dalam waktu lama.<br>Silakan login kembali untuk melanjutkan.</p>
        <div class="counter" id="counter">Mengarahkan dalam <strong>5</strong> detik…</div>
        <a href="{{ route('login') }}" class="btn" id="loginBtn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Login Sekarang
        </a>
        <div class="progress-bar"><div class="progress-fill"></div></div>
    </div>

    <script>
        let s = 5;
        const c = document.getElementById('counter').querySelector('strong');
        const t = setInterval(() => {
            s--;
            c.textContent = s;
            if (s <= 0) { clearInterval(t); window.location.href = '{{ route("login") }}'; }
        }, 1000);
    </script>
</body>
</html>
