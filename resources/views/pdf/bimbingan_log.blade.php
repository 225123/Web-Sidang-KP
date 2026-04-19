<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bimbingan Kerja Praktek - {{ $pendaftaran->user->name ?? 'Mahasiswa' }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 20mm 20mm 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .paper {
            width: 100%;
            box-sizing: border-box;
            position: relative;
        }

        /* Header UKRIDA */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            vertical-align: middle;
        }
        .logo {
            width: 85px;
            height: auto;
        }
        .header-text {
            text-align: center;
        }
        .header-text h1 {
            margin: 0;
            font-size: 18pt;
            letter-spacing: 0.5px;
        }
        .header-text h2 {
            margin: 5px 0;
            font-size: 14pt;
            font-weight: normal;
        }
        .header-text h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        /* Judul Laporan */
        .doc-title-container {
            width: 100%;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
        }
        .doc-id {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 9pt;
            font-weight: normal;
        }

        /* Info Mahasiswa */
        .info-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 10pt;
            border: none;
        }
        .info-table td { 
            padding: 2px 0; 
            border: none;
            vertical-align: top;
        }
        .label-col { width: 140px; }
        .sep-col { width: 20px; text-align: center; }

        /* Tabel Bimbingan */
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }
        .log-table th {
            background-color: #d9d9d9;
            border: 1px solid #aaa;
            padding: 10px 5px;
        }
        .log-table td {
            border: 1px solid #ddd;
            padding: 8px 5px;
            text-align: center;
            vertical-align: middle;
        }
        .col-topik { text-align: left !important; padding-left: 10px !important; }
        
        /* Simbol TTD di Tabel */
        .sig-mini {
            height: 35px;
            opacity: 0.9;
        }

        /* Summary Section */
        .summary-section {
            font-size: 10pt;
            margin-bottom: 50px;
        }

        /* Signatures Area - using Table for DomPDF compatibility */
        .sig-area {
            width: 100%;
            border: none;
            margin-top: 40px;
            font-size: 10pt;
        }
        .sig-block {
            width: 250px;
            text-align: left;
        }
        .sig-space {
            height: 70px;
            text-align: center;
        }
        .name-line {
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
            margin-bottom: 5px;
            font-weight: normal;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 9pt;
            color: #888;
        }
        .footer table { width: 100%; border: none; }
        .footer td { border: none; }
    </style>
</head>
<body>

@php
    function getAbsoluteImagePath($pathAsset) {
        if(!$pathAsset) return null;
        // Gunakan storage_path untuk melewati symlink issue di Windows DomPDF
        $path = storage_path('app/public/' . $pathAsset);
        if(file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            return 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($path));
        }
        return null;
    }

    $pembimbing = $pendaftaran->user->mahasiswa->pembimbing ?? $pendaftaran->pembimbing;
    if (!$pembimbing && $pendaftaran->pendaftaran_asal_id) {
        $asal = \App\Models\PendaftaranKp::with('pembimbing.dosen')->find($pendaftaran->pendaftaran_asal_id);
        if ($asal) {
            $pembimbing = $asal->pembimbing;
        }
    }

    $base64_dosen = getAbsoluteImagePath($pembimbing->signature_path ?? null);
    $base64_mhs = getAbsoluteImagePath($pendaftaran->user->signature_path ?? null);
    
    // Logo resolve directly from original public path instead of base64 function
    $logo_path = public_path('images/logo.png');
    $base64_logo = null;
    if(file_exists($logo_path)) {
        $type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $base64_logo = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($logo_path));
    }
@endphp

<div class="paper">
    <div class="header">
        <table>
            <tr>
                <td style="width: 100px;">
                    @if($base64_logo)
                        <img src="{{ $base64_logo }}" alt="Logo UKRIDA" class="logo">
                    @else
                        <!-- Fallback jamin tampil bila base64 logo.png gagal diproses -->
                        <img src="https://upload.wikimedia.org/wikipedia/id/8/80/Logo_UKRIDA.png" alt="Logo UKRIDA" class="logo">
                    @endif
                </td>
                <td>
                    <div class="header-text">
                        <h1>UNIVERSITAS KRISTEN KRIDA WACANA</h1>
                        <h2>FAKULTAS TEKNOLOGI CERDAS</h2>
                        <h3>PROGRAM STUDI INFORMATIKA</h3>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-title-container">
        <div class="doc-title">LAPORAN BIMBINGAN KERJA PRAKTEK</div>
        <div class="doc-id">B-{{ $pendaftaran->user->mahasiswa->nim ?? 'NIM' }}-KP</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Nama</td>
            <td class="sep-col">:</td>
            <td>{{ strtoupper($pendaftaran->user->name ?? 'MAHASISWA') }}</td>
        </tr>
        <tr>
            <td class="label-col">NIM</td>
            <td class="sep-col">:</td>
            <td>{{ $pendaftaran->user->mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Judul KP</td>
            <td class="sep-col">:</td>
            <td>{{ $pendaftaran->judul_kp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Dosen Pembimbing</td>
            <td class="sep-col">:</td>
            <td>
                @if($pembimbing)
                    {{ $pembimbing->name }}
                @else
                    <span style="color:red">[ERROR: Dosen belum di-plot atau tidak ditemukan di Database]</span>
                @endif
            </td>
        </tr>
    </table>

    <table class="log-table">
        <thead>
            <tr>
                <th style="width: 3%; white-space: nowrap;">No</th>
                <th style="width: 10%; white-space: nowrap;">Tanggal</th>
                <th style="white-space: nowrap; padding: 10px 10px;">Waktu dan Tempat</th>
                <th style="width: 100%;">Topik Pembahasan</th>
                <th style="white-space: nowrap; padding: 10px 10px;">Paraf Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
                @php $materi = json_decode($log->materi_bahasan, true); @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($log->tanggal)->format('d/m/Y') }}</td>
                    <td style="white-space: nowrap; padding: 8px 10px;">{{ $materi['waktuMulai'] ?? '00:00' }} - {{ $materi['waktuSelesai'] ?? '00:00' }} ({{ $materi['tempat'] ?? '-' }})</td>
                    <td class="col-topik">{{ $materi['topik'] ?? '-' }}</td>
                    <td style="white-space: nowrap; padding: 8px 10px;">
                        @if($base64_dosen)
                            <img src="{{ $base64_dosen }}" style="max-height: 25px;" alt="sig">
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 20px; font-style: italic;">
                        Belum ada riwayat pelaksanaan bimbingan yang telah disetujui.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-section">
        <table class="info-table" style="width: auto;">
            <tr>
                <td style="width: 170px;">Ringkasan Total Bimbingan</td>
                <td class="sep-col">:</td>
                <td><strong>{{ count($logs) }}</strong></td>
            </tr>
            <tr>
                <td>Status Akhir</td>
                <td class="sep-col">:</td>
                <td><strong>{{ count($logs) >= 12 ? 'LAYAK SIDANG' : 'BELUM LAYAK SIDANG' }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Area Tanda Tangan dengan Layout Kiri & Kanan sejajar -->
    <table class="sig-area" style="width: 100%; border:none;">
        <tr>
            <!-- Signature Kiri (Dosen) -->
            <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                <div class="sig-block" style="width: 250px; text-align: left;">
                    <div style="margin-bottom: 25px;">Dosen Pembimbing</div>
                    <div class="sig-space" style="height: 70px; text-align: left;">
                        @if($base64_dosen)
                            <img src="{{ $base64_dosen }}" style="max-height: 60px; max-width: 150px; margin-top: 10px;">
                        @endif
                    </div>
                    <div class="name-line" style="border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 5px;">
                        {{ $pembimbing->name ?? '[ERROR: Dosen Pembimbing Kosong]' }}
                    </div>
                    <div style="font-size: 9pt;">NIP/NIDN/NIDK : {{ $pembimbing->dosen->nidn ?? '-' }}</div>
                </div>
            </td>
            
            <!-- Signature Kanan (Mahasiswa) -->
            <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                <div class="sig-block" style="width: 250px; text-align: left; margin-left: auto;">
                    <div style="margin-bottom: 5px;">Mengetahui & Menyetujui,</div>
                    <div style="margin-bottom: 5px;">Mahasiswa</div>
                    <div class="sig-space" style="height: 70px; text-align: left;">
                        @if($base64_mhs)
                            <img src="{{ $base64_mhs }}" style="max-height: 60px; max-width: 150px; margin-top: 10px;">
                        @endif
                    </div>
                    <div class="name-line" style="border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 5px;">
                        {{ $pendaftaran->user->name ?? '-' }}
                    </div>
                    <div style="font-size: 9pt;">NIM : {{ $pendaftaran->user->mahasiswa->nim ?? '-' }}</div>
                </div>
            </td>
        </tr>
    </table>

</div>

<div class="footer">
    <table width="100%">
        <tr>
            <td style="text-align: left;">Dokumen ini diterbitkan pada : {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} | Laporan Bimbingan Kerja Praktek</td>
            <td style="text-align: right;">Halaman 1 dari 1</td>
        </tr>
    </table>
</div>

</body>
</html>
