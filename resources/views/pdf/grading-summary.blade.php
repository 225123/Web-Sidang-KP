<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Penilaian Sidang KP</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; text-transform: uppercase; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table td.label { width: 150px; }
        .info-table td.colon { width: 20px; text-align: center; }
        .grade-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grade-table th, .grade-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .grade-table th { background-color: #f2f2f2; }
        .footer { margin-top: 50px; }
        .footer-table { width: 100%; }
        .footer-table td { text-align: center; }
        .signature-space { height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Formulir Penilaian Kerja Praktik</h2>
        <p>Program Studi Teknik Informatika</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Mahasiswa</td>
            <td class="colon">:</td>
            <td><strong>{{ $sidang->mahasiswa->user->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td class="colon">:</td>
            <td>{{ $sidang->mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td class="label">Judul KP</td>
            <td class="colon">:</td>
            <td><em>{{ $sidang->pendaftaranKp->judul_kp }}</em></td>
        </tr>
        <tr>
            <td class="label">Peran Penilai</td>
            <td class="colon">:</td>
            <td><strong>{{ strtoupper($role) }}</strong></td>
        </tr>
    </table>

    <h3>Kriteria Penilaian:</h3>
    <table class="grade-table">
        <thead>
            <tr>
                <th width="50%">Komponen Penilaian</th>
                <th width="20%">Bobot</th>
                <th width="15%">Nilai (0-100)</th>
                <th width="15%">Skor</th>
            </tr>
        </thead>
        <tbody>
            @if($role === 'pembimbing')
                <tr>
                    <td>Kualitas Laporan</td>
                    <td>40%</td>
                    <td>{{ $sidang->nb_laporan }}</td>
                    <td>{{ $sidang->nb_laporan * 0.4 }}</td>
                </tr>
                <tr>
                    <td>Kualitas Produk</td>
                    <td>40%</td>
                    <td>{{ $sidang->nb_produk }}</td>
                    <td>{{ $sidang->nb_produk * 0.4 }}</td>
                </tr>
                <tr>
                    <td>Sikap dan Kedisiplinan</td>
                    <td>20%</td>
                    <td>{{ $sidang->nb_sikap }}</td>
                    <td>{{ $sidang->nb_sikap * 0.2 }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Nilai Pembimbing</strong></td>
                    <td><strong>{{ $sidang->nilai_pembimbing }}</strong></td>
                </tr>
            @elseif($role === 'penguji1' || $role === 'penguji2')
                @php 
                    $prefix = ($role === 'penguji1') ? 'n1' : 'n2'; 
                    $laporan = $sidang->{$prefix.'_laporan'};
                    $produk = $sidang->{$prefix.'_produk'};
                    $presentasi = $sidang->{$prefix.'_presentasi'};
                    $total = $sidang->{'nilai_'.str_replace('penguji', 'penguji_', $role)};
                @endphp
                <tr>
                    <td>Kualitas Laporan</td>
                    <td>40%</td>
                    <td>{{ $laporan }}</td>
                    <td>{{ $laporan * 0.4 }}</td>
                </tr>
                <tr>
                    <td>Kualitas Produk</td>
                    <td>40%</td>
                    <td>{{ $produk }}</td>
                    <td>{{ $produk * 0.4 }}</td>
                </tr>
                <tr>
                    <td>Kemampuan Presentasi</td>
                    <td>20%</td>
                    <td>{{ $presentasi }}</td>
                    <td>{{ $presentasi * 0.2 }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Nilai {{ strtoupper($role) }}</strong></td>
                    <td><strong>{{ $total }}</strong></td>
                </tr>
            @elseif($role === 'supervisior')
                <tr>
                    <td>Kemampuan dan Motivasi Kerja</td>
                    <td>25%</td>
                    <td>{{ $sidang->ns_motivasi }}</td>
                    <td>{{ $sidang->ns_motivasi * 0.25 }}</td>
                </tr>
                <tr>
                    <td>Kualitas Kerja</td>
                    <td>25%</td>
                    <td>{{ $sidang->ns_kualitas }}</td>
                    <td>{{ $sidang->ns_kualitas * 0.25 }}</td>
                </tr>
                <tr>
                    <td>Inisiatif dan Kreatifitas</td>
                    <td>25%</td>
                    <td>{{ $sidang->ns_inisiatif }}</td>
                    <td>{{ $sidang->ns_inisiatif * 0.25 }}</td>
                </tr>
                <tr>
                    <td>Sikap dan Kedisiplinan</td>
                    <td>25%</td>
                    <td>{{ $sidang->ns_sikap }}</td>
                    <td>{{ $sidang->ns_sikap * 0.25 }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total Nilai Supervisior</strong></td>
                    <td><strong>{{ ($sidang->ns_motivasi * 0.25) + ($sidang->ns_kualitas * 0.25) + ($sidang->ns_inisiatif * 0.25) + ($sidang->ns_sikap * 0.25) }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td width="50%"></td>
                <td width="50%">
                    Pekanbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Penilai,<br>
                    <div class="signature-space"></div>
                    ( ........................................ )<br>
                    NIP.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
