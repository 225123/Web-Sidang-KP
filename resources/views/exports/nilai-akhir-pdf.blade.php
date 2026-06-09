<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Nilai Akhir Kerja Praktek</title>
    <style>
        @page { margin: 60px 70px 100px 70px; }
        body { font-family: "Times New Roman", Times, serif; font-size: 12px; line-height: 1.6; color: #000; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        
        /* Header */
        .header-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 25px; }
        .logo { width: 85px; }
        .header-text h1 { font-size: 16px; margin: 0; letter-spacing: 1px; }
        .header-text h2 { font-size: 14px; margin: 5px 0; }
        .header-text h3 { font-size: 13px; margin: 0; }

        /* Content info */
        .info-table { width: 100%; margin-bottom: 25px; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .label { width: 160px; }
        .separator { width: 20px; text-align: center; }

        /* Score Table */
        .score-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .score-table th, .score-table td { border: 1px solid #000; padding: 10px 8px; }
        .score-table th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
        
        /* Grade Section */
        .grade-container { width: 100%; margin-top: 30px; }
        .grade-box { 
            width: 200px; 
            margin: 0 auto; 
            border: 1px solid #000; 
            padding: 12px;
            text-align: center;
            background-color: #fff;
        }
        .grade-title { font-size: 11px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px; }
        .grade-letter { font-size: 26px; font-weight: bold; margin: 2px 0; }
        .status-text { font-size: 10px; font-weight: bold; color: #333; }

        /* Footer Section */
        .footer {
            position: fixed;
            bottom: -30px;
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

        /* Signature/Date Area (Non-fixed) */
        .signature-area { 
            margin-top: 50px; 
            width: 100%;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="100" style="text-align: center;">
                @if(!empty($logoSrc))
                    <img src="{{ $logoSrc }}" class="logo">
                @endif
            </td>
            <td class="text-center header-text">
                <h1 class="uppercase">Universitas Kristen Krida Wacana</h1>
                <h2 class="uppercase">Fakultas Teknologi Cerdas</h2>
                <h3 class="uppercase">Program Studi Informatika</h3>
            </td>
        </tr>
    </table>

    <h3 class="text-center uppercase underline" style="margin-bottom: 30px;">Laporan Hasil Penilaian Kerja Praktek</h3>

    <table class="info-table">
        <tr>
            <td class="label">Nama Mahasiswa</td>
            <td class="separator">:</td>
            <td class="font-bold">{{ strtoupper($sidang->mahasiswa->user->name ?? '-') }}</td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td class="separator">:</td>
            <td>{{ $sidang->mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Judul Kerja Praktek</td>
            <td class="separator">:</td>
            <td style="font-style: italic;">{{ $sidang->judul_kp_display ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Pembimbing</td>
            <td class="separator">:</td>
            <td>{{ $sidang->pendaftaranKp->pembimbing->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Penguji 1</td>
            <td class="separator">:</td>
            <td>{{ $sidang->penguji1->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Dosen Penguji 2</td>
            <td class="separator">:</td>
            <td>{{ $sidang->penguji2->name ?? '-' }}</td>
        </tr>
    </table>

    <p class="font-bold" style="margin-bottom: 5px;">Rincian Penilaian:</p>
    <table class="score-table">
        <thead>
            <tr>
                <th width="45%">Komponen Penilaian</th>
                <th width="15%">Bobot</th>
                <th width="20%">Nilai (0-100)</th>
                <th width="20%">Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dosen Pembimbing</td>
                <td class="text-center">40%</td>
                <td class="text-center">{{ number_format($sidang->nilai_pembimbing ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_pembimbing ?? 0) * 0.40, 2) }}</td>
            </tr>
            <tr>
                <td>Supervisor</td>
                <td class="text-center">10%</td>
                <td class="text-center">{{ number_format($sidang->nilai_supervisor ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_supervisor ?? 0) * 0.10, 2) }}</td>
            </tr>
            <tr>
                <td>Dosen Penguji 1 (Sidang)</td>
                <td class="text-center">25%</td>
                <td class="text-center">{{ number_format($sidang->nilai_penguji_1 ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_penguji_1 ?? 0) * 0.25, 2) }}</td>
            </tr>
            <tr>
                <td>Dosen Penguji 2 (Sidang)</td>
                <td class="text-center">25%</td>
                <td class="text-center">{{ number_format($sidang->nilai_penguji_2 ?? 0, 2) }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_penguji_2 ?? 0) * 0.25, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="font-bold" style="background-color: #f8f9fa;">
                <td colspan="3" class="text-right">Total Nilai Akhir</td>
                <td class="text-center">{{ number_format($sidang->nilai_akhir_display, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="grade-container">
        <div class="grade-box">
            <div class="grade-title uppercase">Grade Akhir</div>
            <div class="grade-letter">{{ $sidang->grade_display }}</div>
            <div class="status-text uppercase">Status: {{ $sidang->status_kelulusan }}</div>
            @if($sidang->is_penalized)
                <div style="color: #d32f2f; font-size: 8px; font-style: italic; margin-top: 5px;">
                    * Grade disesuaikan berdasarkan ketentuan revisi
                </div>
            @endif
        </div>
    </div>



    <div class="footer">
        <table width="100%">
            <tr>
                <td style="text-align: left;">Laporan Kelulusan KP | Dicetak pada : {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</td>
                <td style="text-align: right;"><span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
