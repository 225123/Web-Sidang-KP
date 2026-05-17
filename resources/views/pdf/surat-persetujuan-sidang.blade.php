<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Persetujuan Sidang KP</title>
    <style>
        body {
            background-color: #fff;
            margin: 0;
            padding: 20px 40px;
            font-family: 'Times New Roman', Times, serif;
        }

        .paper {
            width: 100%;
            display: block;
            position: relative;
        }

        /* Header Section */
        .header {
            border-bottom: 3px solid #333;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .header-text {
            text-align: center;
            color: #1a1a1a;
        }

        .header-text h1 {
            margin: 0;
            font-size: 16pt;
            letter-spacing: 1px;
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

        /* Title Section */
        .document-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .document-title h4 {
            text-decoration: underline;
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            display: inline-block;
        }

        .doc-number {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 10pt;
        }

        /* Content Section */
        .content {
            font-size: 12pt;
            line-height: 1.6;
            color: #1a1a1a;
        }

        .opening-text {
            margin-bottom: 20px;
        }

        .data-table {
            margin-left: 0;
            margin-bottom: 30px;
            width: 100%;
        }

        .data-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .label-col { width: 140px; }
        .sep-col { width: 20px; text-align: center; }

        .status-text {
            margin-top: 30px;
            text-align: justify;
        }

        /* Signature Section */
        .signature-wrapper {
            margin-top: 60px;
            float: right;
            width: 220px;
            text-align: left;
        }

        .sig-date {
            margin-bottom: 5px;
        }

        .sig-role {
            margin-bottom: 10px;
        }

        .sig-image {
            height: 90px;
            max-width: 250px;
            display: block;
            margin: 5px 0 5px -10px;
        }

        .sig-identity {
            display: inline-block;
            margin-top: 5px;
            min-width: 180px; /* Mencegah terlalu sempit */
        }

        .sig-name {
            font-weight: bold;
            border-bottom: 1px solid #888;
            padding-bottom: 2px;
            margin-bottom: 4px;
            padding-right: 15px; /* Memberikan sedikit sisa jarak di ujung kanan */
        }

        /* Footer Section */
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
    </style>
</head>
<body>

    <div class="paper">
        <div class="header">
            <table width="100%">
                <tr>
                    <td width="15%" style="text-align: center;">
                        @php
                            $logoPath = public_path('images/logo.png');
                            $logoData = '';
                            if (file_exists($logoPath)) {
                                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                                $data = file_get_contents($logoPath);
                                $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            } else {
                                // Fallback jika gambar lokal tidak ada
                                $logoData = 'https://upload.wikimedia.org/wikipedia/id/8/80/Logo_UKRIDA.png';
                            }
                        @endphp
                        <img src="{{ $logoData }}" alt="Logo Instansi" class="logo">
                    </td>
                    <td width="85%" class="header-text">
                        <h1>UNIVERSITAS KRISTEN KRIDA WACANA</h1>
                        <h2>FAKULTAS TEKNOLOGI CERDAS</h2>
                        <h3>PROGRAM STUDI INFORMATIKA</h3>
                    </td>
                </tr>
            </table>
        </div>

        <div style="text-align: right; margin-bottom: 20px; font-size: 10pt;">
            B-{{ $persetujuan->mahasiswa->nim }}-KP/GENAP {{ date('Y') }}
        </div>
        
        <div class="document-title" style="margin-top: 20px;">
            <h4>SURAT PERSETUJUAN SIDANG KP</h4>
        </div>

        <div class="content">
            <p class="opening-text">Yang bertanda tangan di bawah ini, Dosen Pembimbing Kerja Praktik, menyatakan bahwa:</p>

            <table class="data-table">
                <tr>
                    <td class="label-col">Nama</td>
                    <td class="sep-col">:</td>
                    <td><strong>{{ strtoupper($persetujuan->mahasiswa?->user?->name ?? 'NAMA MAHASISWA') }}</strong></td>
                </tr>
                <tr>
                    <td class="label-col">NIM</td>
                    <td class="sep-col">:</td>
                    <td>{{ $persetujuan->mahasiswa?->nim ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Judul KP</td>
                    <td class="sep-col">:</td>
                    <td>{{ $persetujuan->pendaftaranKp?->judul_kp ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Instansi KP</td>
                    <td class="sep-col">:</td>
                    <td>{{ $persetujuan->pendaftaranKp?->instansi_nama ?? '-' }}</td>
                </tr>
            </table>

            <p class="status-text">
                Telah menyelesaikan proses bimbingan Kerja Praktik dan <strong>LAPORAN KERJA PRAKTIK</strong> tersebut dinyatakan <strong>LAYAK</strong> untuk diajukan dalam Sidang Kerja Praktik.
            </p>
            
            <p>Demikian surat persetujuan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="signature-wrapper">
            @php
                $approveDate = $persetujuan->status_verifikasi === 'verified' && $persetujuan->updated_at 
                                ? $persetujuan->updated_at 
                                : now();

                $kp = $persetujuan->pendaftaranKp;
                // Handle the normalization of Pembimbing (diambil dari Pendaftaran KP)
                $pembimbing = $persetujuan->pendaftaranKp->pembimbing ?? $kp?->pembimbing;
                
                // Jika tidak ada pembimbing namun dia adalah anggota kelompok (ada pendaftaran_asal_id)
                if (!$pembimbing && $kp?->pendaftaran_asal_id) {
                    $asal = \App\Models\PendaftaranKp::with('pembimbing.dosen')->find($kp->pendaftaran_asal_id);
                    if ($asal) {
                        $pembimbing = $asal->pembimbing;
                    }
                }
            @endphp
            <div class="sig-date">Jakarta, {{ \Carbon\Carbon::parse($approveDate)->locale('id')->isoFormat('D MMMM Y') }}</div>
            <div class="sig-role">Dosen Pembimbing</div>
            
            @if($persetujuan->status_verifikasi === 'verified' && $pembimbing?->signature_path)
                <!-- Load base64 image from storage to bypass DomPDF remote fetching limitations -->
                @php
                    $disk = upload_disk();
                    $sigData = '';
                    try {
                        if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($pembimbing->signature_path)) {
                            $type = pathinfo($pembimbing->signature_path, PATHINFO_EXTENSION);
                            $data = \Illuminate\Support\Facades\Storage::disk($disk)->get($pembimbing->signature_path);
                            $sigData = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        }
                    } catch (\Exception $e) {
                        // ignore error
                    }
                @endphp
                @if($sigData)
                    <img src="{{ $sigData }}" class="sig-image" alt="Tanda Tangan Dosen">
                @else
                    <br><div style="height:50px;">(TTD Tidak Ditemukan)</div>
                @endif
            @else
                <br><div style="height:60px;">(Menunggu Persetujuan)</div><br>
            @endif

            <div class="sig-identity">
                <div class="sig-name">{{ $pembimbing?->name ?? 'NAMA DOSEN' }}</div>
                <div class="sig-nip">NIDN/NIDK : {{ $pembimbing?->dosen?->nidn ?? '-' }}</div>
            </div>
        </div>
        
        <!-- Clear float for neatness -->
        <div style="clear: both;"></div>

        <div class="footer">
            <table width="100%">
                <tr>
                    <td style="text-align: left;">Dokumen ini diterbitkan pada : {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} | Surat Persetujuan Sidang KP</td>
                    <td style="text-align: right;">Halaman 1 dari 1</td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>