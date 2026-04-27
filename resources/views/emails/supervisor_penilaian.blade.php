<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Penilaian Kerja Praktek</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #0056b3;
            color: #ffffff;
            padding: 25px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .content {
            padding: 35px 30px;
        }
        .content p {
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #0056b3;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0 25px 0;
        }
        .btn {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .footer {
            background-color: #f1f1f1;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Permohonan Penilaian Kerja Praktek</h1>
    </div>
    
    <div class="content">
        <p>Yth. <strong>{{ $sidang->pendaftaranKp->supervisorInstansi->nama_supervisor ?? 'Supervisor Eksternal' }}</strong>,</p>
        
        <p>Melalui surat elektronik ini, kami dari Program Studi Teknik Informatika Universitas Kristen Krida Wacana (UKRIDA) mengucapkan terima kasih atas ketersediaan Bapak/Ibu dalam membimbing mahasiswa kami selama pelaksanaan Kerja Praktek (KP).</p>
        
        <p>Sehubungan dengan telah berakhirnya masa Kerja Praktek, kami memohon kesediaan Bapak/Ibu untuk memberikan **Nilai Evaluasi Akhir** kepada mahasiswa berikut:</p>
        
        <div class="info-box">
            <p><strong>Nama Mahasiswa:</strong> {{ $sidang->mahasiswa->user->name ?? '-' }}</p>
            <p><strong>NIM:</strong> {{ $sidang->mahasiswa->nim ?? '-' }}</p>
            <p><strong>Judul Proyek:</strong> {{ $sidang->pendaftaranKp->judul_kp ?? '-' }}</p>
        </div>

        <p>Proses penilaian dilakukan secara digital dan langsung masuk ke dalam sistem akademik kami. Bapak/Ibu **tidak perlu** membuat akun atau mendaftar. Silakan klik tombol di bawah ini untuk mengakses formulir penilaian yang telah diamankan khusus untuk Bapak/Ibu:</p>
        
        <div class="btn-container">
            <a href="{{ $url_penilaian }}" class="btn">Isi Formulir Penilaian</a>
        </div>
        
        <p style="font-size: 13px; color: #555; background: #fff3cd; padding: 10px; border-radius: 5px; border: 1px solid #ffeeba;">
            <em>*Penting: Link di atas bersifat rahasia dan unik hanya untuk mahasiswa tersebut. Setelah Bapak/Ibu mengirimkan nilai, link tersebut akan hangus untuk mencegah modifikasi ganda.</em>
        </p>

        <p>Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>
        
        <p>Hormat kami,<br><strong>Koordinator Kerja Praktek UKRIDA</strong></p>
    </div>
    
    <div class="footer">
        &copy; {{ date('Y') }} Sistem Informasi Kerja Praktek UKRIDA.<br>
        Email ini dikirim secara otomatis oleh sistem, mohon untuk tidak membalas ke alamat email ini.
    </div>
</div>

</body>
</html>
