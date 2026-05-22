<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArchivedPeriodeExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $periodeId;
    protected $tahunAjaranNama;

    public function __construct($periodeId, $tahunAjaranNama)
    {
        $this->periodeId = $periodeId;
        $this->tahunAjaranNama = $tahunAjaranNama;
    }

    public function collection()
    {
        // Ambil semua mahasiswa pada periode yang dipilih beserta relasinya
        return Mahasiswa::with(['user', 'pendaftaranKps.pembimbing', 'pendaftaranKps.pendaftaranSidang'])
            ->where('tahun_ajaran_id', $this->periodeId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NIM',
            'NAMA MAHASISWA',
            'EMAIL',
            'STATUS AKTIF',
            'JUDUL KERJA PRAKTEK',
            'NAMA INSTANSI',
            'DOSEN PEMBIMBING',
            'STATUS KP',
            'TANGGAL SIDANG',
            'NILAI AKHIR',
            'GRADE',
            'STATUS KELULUSAN'
        ];
    }

    public function map($mhs): array
    {
        static $no = 1;

        // Ambil KP terbaru milik mahasiswa ini (tanpa global scope periode agar terambil walau beda filter jika ada anomali)
        $kp = PendaftaranKp::withoutGlobalScope('periode')
            ->where('mahasiswa_id', $mhs->user_id)
            ->where('tahun_ajaran_id', $this->periodeId)
            ->latest()
            ->first();

        // Ambil sidang terbaru dari KP tersebut
        $sidang = null;
        if ($kp) {
            $sidang = PendaftaranSidang::withoutGlobalScope('periode')
                ->where('pendaftaran_kp_id', $kp->id)
                ->latest()
                ->first();
        }

        return [
            $no++,
            $mhs->nim,
            $mhs->user->name ?? '-',
            $mhs->user->email ?? '-',
            $mhs->is_aktif ? 'Aktif' : 'Tidak Aktif',
            $kp->judul_kp ?? '-',
            $kp->instansi_nama ?? '-',
            $kp->pembimbing->name ?? '-',
            $kp->status_kp ?? '-',
            $sidang && $sidang->tanggal_sidang ? \Carbon\Carbon::parse($sidang->tanggal_sidang)->format('d M Y') : '-',
            $sidang->nilai_akhir ?? '-',
            $this->calculateGrade($sidang),
            $sidang->status_kelulusan ?? '-',
        ];
    }

    public function title(): string
    {
        // Batasi panjang judul sheet max 31 karakter
        return substr('Arsip_' . str_replace('/', '_', $this->tahunAjaranNama), 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function calculateGrade($sidang)
    {
        if (!$sidang) return '-';
        if ($sidang->status_kelulusan === 'Lanjut' || $sidang->status_kelulusan === 'Tidak Lulus') return 'E';
        if ($sidang->nilai_akhir <= 0) return '-';

        $nilai = $sidang->nilai_akhir;
        if ($nilai >= 86) return 'A';
        if ($nilai >= 81) return 'A-';
        if ($nilai >= 76) return 'B+';
        if ($nilai >= 71) return 'B';
        if ($nilai >= 66) return 'B-';
        if ($nilai >= 61) return 'C+';
        if ($nilai >= 56) return 'C';
        if ($nilai >= 46) return 'D';
        return 'E';
    }
}
