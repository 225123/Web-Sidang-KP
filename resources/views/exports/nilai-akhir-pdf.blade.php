<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Nilai Akhir Kerja Praktek</title>
    <style>
        @page { margin: 40px; }
        body { font-family: "Times New Roman", serif; font-size: 12px; line-height: 1.5; color: #333; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .mb-4 { margin-bottom: 20px; }
        .header-table { width: 100%; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; }
        .content-table { width: 100%; margin-bottom: 20px; }
        .content-table td { padding: 5px; vertical-align: top; }
        .score-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .score-table th, .score-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .score-table th { bg-color: #f2f2f2; }
        .final-box { border: 2px solid #000; padding: 15px; margin-top: 30px; text-align: center; }
        .footer { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="100"><img src="{{ public_path('images/logo.png') }}" class="logo"></td>
            <td class="text-center">
                <h2 class="uppercase" style="margin:0;">Universitas Kristen Krida Wacana</h2>
                <h3 class="uppercase" style="margin:5px 0;">Fakultas Teknologi Cerdas</h3>
                <h4 class="uppercase" style="margin:0;">Program Studi Informatika</h4>
            </td>
        </tr>
    </table>

    <h3 class="text-center mb-4">LAPORAN HASIL PENILAIAN KERJA PRAKTEK</h3>

    <table class="content-table">
        <tr><td width="150">Nama Mahasiswa</td><td>: {{ strtoupper($sidang->mahasiswa->user->name ?? '-') }}</td></tr>
        <tr><td>NIM</td><td>: {{ $sidang->mahasiswa->nim ?? '-' }}</td></tr>
        <tr><td>Judul Kerja Praktek</td><td>: {{ $sidang->pendaftaranKp->judul_kp ?? '-' }}</td></tr>
        <tr><td>Dosen Pembimbing</td><td>: {{ $sidang->pendaftaranKp->pembimbing->name ?? '-' }}</td></tr>
        <tr><td>Dosen Penguji 1</td><td>: {{ $sidang->penguji1->name ?? '-' }}</td></tr>
        <tr><td>Dosen Penguji 2</td><td>: {{ $sidang->penguji2->name ?? '-' }}</td></tr>
    </table>

    <h4 class="mb-4">Rincian Penilaian</h4>
    <table class="score-table">
        <thead>
            <tr>
                <th>Komponen Penilaian</th>
                <th>Bobot</th>
                <th>Nilai (0-100)</th>
                <th>Nilai Terbobot</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dosen Pembimbing</td>
                <td class="text-center">40%</td>
                <td class="text-center">{{ $sidang->nilai_pembimbing ?? 0 }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_pembimbing ?? 0) * 0.4, 2) }}</td>
            </tr>
            <tr>
                <td>Supervisor Instansi</td>
                <td class="text-center">10%</td>
                <td class="text-center">{{ $sidang->nilai_supervisor ?? 0 }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_supervisor ?? 0) * 0.1, 2) }}</td>
            </tr>
            <tr>
                <td>Dosen Penguji 1</td>
                <td class="text-center">25%</td>
                <td class="text-center">{{ $sidang->nilai_penguji_1 ?? 0 }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_penguji_1 ?? 0) * 0.25, 2) }}</td>
            </tr>
            <tr>
                <td>Dosen Penguji 2</td>
                <td class="text-center">25%</td>
                <td class="text-center">{{ $sidang->nilai_penguji_2 ?? 0 }}</td>
                <td class="text-center">{{ number_format(($sidang->nilai_penguji_2 ?? 0) * 0.25, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="font-bold">
                <td colspan="3">Total Nilai Akhir</td>
                <td class="text-center">{{ number_format($sidang->nilai_akhir_display, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="final-box">
        <div class="font-bold uppercase" style="font-size: 14px;">Grade Akhir</div>
        <div style="font-size: 32px; font-weight: bold; margin: 10px 0;">{{ $sidang->grade_display }}</div>
        <div class="font-bold uppercase" style="font-size: 14px;">Status: {{ strtoupper($sidang->status_kelulusan) }}</div>
        @if($sidang->is_penalized)
            <p style="color: red; font-size: 10px; font-style: italic;">* Grade diturunkan karena keterlambatan/kelengkapan revisi.</p>
        @endif
    </div>

    <div class="footer">
        <p>Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p>Dicetak secara otomatis melalui Sistem Informasi Kerja Praktek</p>
    </div>
</body>
</html>
