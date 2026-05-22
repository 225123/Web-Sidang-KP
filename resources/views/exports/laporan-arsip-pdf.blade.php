<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kelulusan Mahasiswa KP</title>
    <style>
        @page {
            margin: 40px 40px 60px 40px;
        }
        body { 
            font-family: "Times New Roman", Times, serif; 
            font-size: 11px; 
            line-height: 1.4; 
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .underline { text-decoration: underline; }
        .w-full { width: 100%; }
        
        .header-table { width: 100%; border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 70px; height: 70px; }
        .header-text h1 { font-size: 15px; margin: 0 0 5px 0; }
        .header-text h2 { font-size: 13px; margin: 0 0 5px 0; }

        .report-title { font-size: 14px; font-bold: true; text-align: center; margin-bottom: 20px; text-decoration: underline; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid black; padding: 6px; }
        table.data-table th { background-color: #f2f2f2; font-weight: bold; }

        .signature-section { margin-top: 40px; float: right; width: 250px; text-align: left; }
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
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 100px; text-align: center;">
                <img src="{{ public_path('images/logo.png') }}" class="logo">
            </td>
            <td class="text-center header-text" style="padding-right: 50px;">
                <h1 class="uppercase">Universitas Kristen Krida Wacana</h1>
                <h2 class="uppercase">Fakultas Teknologi Cerdas</h2>
                <h2 class="uppercase">Program Studi Informatika</h2>
            </td>
        </tr>
    </table>

    <div class="report-title uppercase">LAPORAN KELULUSAN MAHASISWA KERJA PRAKTEK</div>
    <div class="text-center" style="margin-bottom: 20px;">Periode: Genap 2025/2026</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 100px;">NIM</th>
                <th>Nama Mahasiswa</th>
                <th style="width: 60px;">Nilai</th>
                <th style="width: 60px;">Grade</th>
                <th style="width: 120px;">Status Kelulusan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $index => $mhs)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $mhs->nim }}</td>
                    <td class="uppercase">{{ $mhs->nama }}</td>
                    <td class="text-center font-bold">{{ $mhs->nilai_akhir_display === '-' ? '-' : number_format((float) $mhs->nilai_akhir_display, 2) }}</td>
                    <td class="text-center font-bold">{{ $mhs->grade_display }}</td>
                    <td class="text-center">{{ $mhs->status_kelulusan_display }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data kelulusan yang difinalisasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-section">
        <p>Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</p>
        <p class="font-bold">Koordinator Kerja Praktek,</p>
        <div style="margin: 10px 0;">
            @if($koordinator?->signature_path && file_exists(storage_path('app/public/' . $koordinator->signature_path)))
                <img src="{{ storage_path('app/public/' . $koordinator->signature_path) }}" style="width: 150px; height: 80px;">
            @elseif($koordinator?->signature_path && file_exists(public_path('storage/' . $koordinator->signature_path)))
                <img src="{{ public_path('storage/' . $koordinator->signature_path) }}" style="width: 150px; height: 80px;">
            @elseif($koordinator?->signature && (strpos($koordinator->signature, 'data:image') === 0))
                <img src="{{ $koordinator->signature }}" style="width: 150px; height: 80px;">
            @else
                <div style="height: 80px; color: #ccc; font-style: italic; font-size: 10px; padding-top: 30px;">
                    (Tanda tangan tidak ditemukan)
                </div>
            @endif
        </div>
        <p class="font-bold underline">{{ $koordinator?->name ?? 'Koordinator KP' }}</p>
        <p>NIDK/NIDN : {{ $koordinator?->dosen?->nidn ?? '-' }}</p>
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
