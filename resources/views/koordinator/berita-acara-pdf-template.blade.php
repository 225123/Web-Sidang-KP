<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Sidang Kerja Praktek</title>
    <style>
        @page {
            margin: 40px 40px 60px 40px; /* top right bottom left */
        }
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 13px; 
            line-height: 1.5; 
            /* Remove body padding since @page margin handles it */
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        .w-full { width: 100%; }
        .text-justify { text-align: justify; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px; vertical-align: top; }
        .w-40 { width: 180px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-4 { margin-top: 16px; }
        .mt-8 { margin-top: 32px; }
        .signature-table { width: 100%; margin-top: 20px; text-align: left; }
        .signature-table td { width: 50%; padding-bottom: 20px; }
        .signature-line { border-bottom: 1px solid black; width: 80%; margin-top: 70px; margin-bottom: 5px; }
        .header-table { width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table td { padding: 0; }
        .logo { width: 90px; height: 90px; }
        .header-text h1 { font-size: 17px; margin: 0 0 5px 0; letter-spacing: 1px; }
        .header-text h2 { font-size: 15px; margin: 0 0 5px 0; }
        
        .content-table { margin-left: 20px; width: 90%; }

        /* Footer Section */
        .footer {
            position: fixed;
            bottom: -30px; /* Places it inside the 60px bottom margin */
            left: 0;
            right: 0;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 9pt;
            color: #888;
        }

        .page-number:after {
            content: "Halaman " counter(page) " dari " counter(pages);
        }
    </style>
</head>
<body>
    @php
        if (!function_exists('getAbsoluteImagePath')) {
            function getAbsoluteImagePath($pathAsset) {
                if(!$pathAsset) return null;
                
                if(str_starts_with($pathAsset, 'data:image')) return $pathAsset;

                if (!extension_loaded('gd') && !str_ends_with(strtolower($pathAsset), '.jpg') && !str_ends_with(strtolower($pathAsset), '.jpeg')) {
                    return null;
                }

                $disk = upload_disk();
                try {
                    if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($pathAsset)) {
                        $type = pathinfo($pathAsset, PATHINFO_EXTENSION);
                        if (!$type) $type = 'png';
                        $data = \Illuminate\Support\Facades\Storage::disk($disk)->get($pathAsset);
                        return 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                } catch (\Exception $e) {
                    // ignore error
                }
                
                return null;
            }
        }

        $base64_penguji1 = getAbsoluteImagePath($sidang->penguji1?->signature_path ?? null) ?? $sidang->penguji1?->signature ?? null;
        $base64_penguji2 = getAbsoluteImagePath($sidang->penguji2?->signature_path ?? null) ?? $sidang->penguji2?->signature ?? null;
        $base64_koordinator = getAbsoluteImagePath($koordinator?->signature_path ?? null) ?? $koordinator?->signature ?? null;
        $base64_pembimbing = getAbsoluteImagePath($sidang->pendaftaranKp->pembimbing?->signature_path ?? null) ?? $sidang->pendaftaranKp->pembimbing?->signature ?? null;
        
        $base64_supervisor = null;
        if ($sidang->pendaftaranKp->jenis_instansi === 'Internal') {
            $base64_supervisor = getAbsoluteImagePath($sidang->pendaftaranKp->supervisorInternal?->signature_path ?? null) ?? $sidang->pendaftaranKp->supervisorInternal?->signature ?? null;
        } else {
            $base64_supervisor = getAbsoluteImagePath($sidang->file_nilai_supervisor ?? null);
        }
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 120px; text-align: center;">
                @if(!empty($logoSrc))
                    <img src="{{ $logoSrc }}" class="logo">
                @endif
            </td>
            <td class="text-center header-text" style="padding-right: 80px;">
                <h1 class="uppercase">Universitas Kristen Krida Wacana</h1>
                <h2 class="uppercase">Fakultas Teknologi Cerdas</h2>
                <h2 class="uppercase">Program Studi Informatika</h2>
            </td>
        </tr>
    </table>

    <div class="text-center font-bold underline mb-6" style="font-size: 15px; margin-top: 30px;">BERITA ACARA SIDANG KERJA PRAKTEK</div>

    <div style="padding: 0 10px;">
        <p class="text-justify" style="line-height: 1.8;">
            Pada hari ini <strong>{{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('dddd') }}</strong>, tanggal <strong>{{ \Carbon\Carbon::parse($sidang->tanggal_sidang)->locale('id')->isoFormat('D MMMM Y') }}</strong>, pukul <strong>{{ \Carbon\Carbon::parse($sidang->waktu_mulai_sidang)->format('H:i') }}</strong> WIB, bertempat di ruangan <strong>{{ $sidang->ruang_sidang ?? '-' }}</strong>, telah dilaksanakan kegiatan <strong>Sidang Kerja Praktek</strong> Program Studi Informatika, Fakultas Teknologi Cerdas, Universitas Kristen Krida Wacana.
        </p>

        <p style="margin-top: 20px;">Sidang tersebut dilaksanakan terhadap mahasiswa dengan data sebagai berikut:</p>

        <table class="content-table">
            <tr><td class="w-40">Nama Mahasiswa</td><td>: <strong>{{ strtoupper($sidang->mahasiswa->user->name ?? '-') }}</strong></td></tr>
            <tr><td>NIM</td><td>: <strong>{{ $sidang->mahasiswa->nim ?? '-' }}</strong></td></tr>
            <tr><td>Judul Kerja Praktek</td><td>: <strong>{{ $sidang->judul_kp_display ?? '-' }}</strong></td></tr>
        </table>

        <p style="margin-top: 25px;">Sidang ini dihadiri dan dinilai oleh tim penguji sebagai berikut:</p>

        <table class="content-table">
            <tr><td class="w-40">Dosen Penguji 1</td><td>: {{ $sidang->penguji1->name ?? '-' }}</td></tr>
            <tr><td>Dosen Penguji 2</td><td>: {{ $sidang->penguji2->name ?? '-' }}</td></tr>
        </table>

        <p class="text-justify" style="margin-top: 30px;">
            Demikian berita acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="mt-8">
            <p>Mengetahui,</p>
            <table class="signature-table">
                <!-- Baris 1: Pembimbing & Supervisor -->
                <tr>
                    <td>
                        <p class="font-bold">Dosen Pembimbing</p>
                        @if($base64_pembimbing)
                            <img src="{{ $base64_pembimbing }}" style="width: 150px; height: 80px; margin: 10px 0; object-fit: contain;">
                        @else
                            <div style="height: 80px; color: red; font-style: italic; font-size: 10px; padding-top: 30px;">
                                (Tanda tangan digital belum tersedia)
                            </div>
                        @endif
                        <p class="font-bold underline">{{ $sidang->pendaftaranKp->pembimbing?->name ?? 'Belum Diplot' }}</p>
                        <p>NIDK/NIDN : {{ $sidang->pendaftaranKp->pembimbing?->dosen?->nidn ?? '-' }}</p>
                    </td>
                    <td>
                        <p class="font-bold">Supervisor Instansi</p>
                        @if($base64_supervisor)
                            <img src="{{ $base64_supervisor }}" style="width: 150px; height: 80px; margin: 10px 0; object-fit: contain;">
                        @else
                            <div style="height: 80px; color: red; font-style: italic; font-size: 10px; padding-top: 30px;">
                                (Tanda tangan digital belum tersedia)
                            </div>
                        @endif
                        @if($sidang->pendaftaranKp->jenis_instansi === 'Internal')
                            <p class="font-bold underline">{{ $sidang->pendaftaranKp->supervisorInternal?->name ?? 'Belum Diplot' }}</p>
                            <p>Universitas Kristen Krida Wacana</p>
                        @else
                            <p class="font-bold underline">{{ $sidang->pendaftaranKp->supervisorInstansi?->nama_supervisor ?? 'Supervisor Eksternal' }}</p>
                            <p>{{ $sidang->pendaftaranKp->instansi_nama ?? '-' }}</p>
                        @endif
                    </td>
                </tr>
                <!-- Baris 2: Penguji 1 & Penguji 2 -->
                <tr>
                    <td style="padding-top: 15px;">
                        <p class="font-bold">Dosen Penguji 1</p>
                        @if($base64_penguji1)
                            <img src="{{ $base64_penguji1 }}" style="width: 150px; height: 80px; margin: 10px 0; object-fit: contain;">
                        @else
                            <div style="height: 80px; color: red; font-style: italic; font-size: 10px; padding-top: 30px;">
                                (Tanda tangan digital belum tersedia)
                            </div>
                        @endif
                        <p class="font-bold underline">{{ $sidang->penguji1?->name ?? 'Belum Diplot' }}</p>
                        <p>NIDK/NIDN : {{ $sidang->penguji1?->dosen?->nidn ?? '-' }}</p>
                    </td>
                    <td style="padding-top: 15px;">
                        <p class="font-bold">Dosen Penguji 2</p>
                        @if($base64_penguji2)
                            <img src="{{ $base64_penguji2 }}" style="width: 150px; height: 80px; margin: 10px 0; object-fit: contain;">
                        @else
                            <div style="height: 80px; color: red; font-style: italic; font-size: 10px; padding-top: 30px;">
                                (Tanda tangan digital belum tersedia)
                            </div>
                        @endif
                        <p class="font-bold underline">{{ $sidang->penguji2?->name ?? 'Belum Diplot' }}</p>
                        <p>NIDK/NIDN : {{ $sidang->penguji2?->dosen?->nidn ?? '-' }}</p>
                    </td>
                </tr>
                <!-- Baris 3: Koordinator -->
                <tr>
                    <td style="padding-top: 15px;">
                        <p class="font-bold">Koordinator Kerja Praktek</p>
                        @if($base64_koordinator)
                            <img src="{{ $base64_koordinator }}" style="width: 150px; height: 80px; margin: 10px 0; object-fit: contain;">
                        @else
                            <div style="height: 80px; color: red; font-style: italic; font-size: 10px; padding-top: 30px;">
                                (Tanda tangan digital belum tersedia)
                            </div>
                        @endif
                        <p class="font-bold underline">{{ $koordinator?->name ?? 'Koordinator KP' }}</p>
                        <p>NIDK/NIDN : {{ $koordinator?->dosen?->nidn ?? '-' }}</p>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <table width="100%">
            <tr>
                <td style="text-align: left;">Dokumen Digital Sah | Dicetak pada : {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</td>
                <td style="text-align: right;"><span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
