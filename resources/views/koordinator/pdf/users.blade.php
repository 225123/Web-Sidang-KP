<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000000;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-surat h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }
        .kop-surat h2 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .kop-surat p {
            font-size: 12px;
            margin: 0;
        }
        h3.judul {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            font-weight: bold;
            text-align: center;
            font-size: 12px;
            color: #000000;
        }
        td {
            font-size: 12px;
            color: #000000; /* Semua tulisan dipaksa hitam sesuai request */
        }
        .text-center {
            text-align: center;
        }
        .signature {
            float: right;
            width: 250px;
            text-align: left;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature p.date-title {
            margin: 0 0 70px 0;
        }
        .signature p.name {
            margin: 0;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RESMI -->
    <div class="kop-surat">
        <h1>UNIVERSITAS KRISTEN KRIDA WACANA</h1>
        <h2>Fakultas Teknik dan Ilmu Komputer - Program Studi Informatika</h2>
        <p>Jl. Tanjung Duren Raya No. 4, Jakarta Barat 11470<br>
        Telp: (021) 5666952 | Email: info@ukrida.ac.id | Web: www.ukrida.ac.id</p>
    </div>

    <!-- JUDUL DOKUMEN -->
    <h3 class="judul">{{ $title }}</h3>

    <!-- TABEL DATA -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 15%;">ID (NIM/NIDN)</th>
                <th style="width: 25%;">Email</th>
                <th style="width: 15%;">Role</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td class="text-center">{{ $user->identifier_id ?? '-' }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">{{ ucwords(str_replace('_', ' ', $user->role)) }}</td>
                <td class="text-center">
                    @if(in_array($user->role, ['dosen', 'koordinator_kp']))
                        {{ $user->is_aktif !== false ? 'Aktif' : 'Tidak Aktif' }}
                    @elseif($user->role === 'mahasiswa')
                        {{ ucfirst($user->status_mahasiswa ?? 'baru') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data pengguna yang ditemukan pada kategori ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN (TTD) -->
    <div class="signature">
        <p class="date-title">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
        Mengetahui,<br><br>Koordinator Kerja Praktik</p>
        <p class="name">{{ auth()->user()->name ?? 'Koordinator KP' }}</p>
    </div>

</body>
</html>
