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
        $sidangRows = PendaftaranSidang::withoutGlobalScope('periode')
            ->with(['mahasiswa.user', 'pendaftaranKp.pembimbing'])
            ->whereHas('pendaftaranKp', function($q) {
                $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $this->periodeId);
            })
            ->whereIn('status_kelulusan', ['Lulus', 'Lulus Dengan Revisi', 'Lanjut', 'Tidak Lulus'])
            ->get();

        $mahasiswaDenganSidang = $sidangRows->pluck('mahasiswa_id')->unique()->toArray();

        $mahasiswaTanpaSidang = Mahasiswa::with(['user', 'pendaftaranKps' => function($q) {
            $q->withoutGlobalScope('periode')->where('tahun_ajaran_id', $this->periodeId)->latest();
        }])
            ->where('tahun_ajaran_id', $this->periodeId)
            ->whereNotIn('user_id', $mahasiswaDenganSidang)
            ->get();

        $hasil = collect();

        foreach ($sidangRows as $sidang) {
            $logic = $this->calculateFinalLogic($sidang);
            $statusKelulusan = $sidang->status_kelulusan === 'Tidak Lulus' ? 'Lanjut' : $sidang->status_kelulusan;

            $kp = $sidang->pendaftaranKp;
            
            $ownKp = \App\Models\PendaftaranKp::withoutGlobalScope('periode')
                ->where('mahasiswa_id', $sidang->mahasiswa_id)
                ->whereIn('status_kp', ['pending', 'approved'])
                ->latest()
                ->first();
            $judulKp = $ownKp ? $ownKp->judul_kp : ($kp->judul_kp ?? '-');

            $hasil->push((object)[
                'nim' => $sidang->mahasiswa->nim ?? '-',
                'nama' => $sidang->mahasiswa->user->name ?? '-',
                'email' => $sidang->mahasiswa->user->email ?? '-',
                'is_aktif' => $sidang->mahasiswa->is_aktif ? 'Aktif' : 'Tidak Aktif',
                'judul_kp' => $judulKp,
                'instansi_nama' => $kp->instansi_nama ?? '-',
                'dosen_pembimbing' => $kp->pembimbing->name ?? '-',
                'status_kp' => $kp->status_kp ?? '-',
                'tanggal_sidang' => $sidang->tanggal_sidang ? \Carbon\Carbon::parse($sidang->tanggal_sidang)->format('d M Y') : '-',
                'nilai_akhir' => $logic['nilai'],
                'grade' => $logic['grade'],
                'status_kelulusan' => $statusKelulusan,
            ]);
        }

        foreach ($mahasiswaTanpaSidang as $mhs) {
            $kp = $mhs->pendaftaranKps->first();
            
            $hasil->push((object)[
                'nim' => $mhs->nim ?? '-',
                'nama' => $mhs->user->name ?? '-',
                'email' => $mhs->user->email ?? '-',
                'is_aktif' => $mhs->is_aktif ? 'Aktif' : 'Tidak Aktif',
                'judul_kp' => $kp?->judul_kp ?? '-',
                'instansi_nama' => $kp?->instansi_nama ?? '-',
                'dosen_pembimbing' => $kp?->pembimbing?->name ?? '-',
                'status_kp' => $kp?->status_kp ?? '-',
                'tanggal_sidang' => '-',
                'nilai_akhir' => '0',
                'grade' => 'E',
                'status_kelulusan' => 'Lanjut',
            ]);
        }

        return $hasil->sortBy('nim')->values();
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

    public function map($row): array
    {
        static $no = 1;
        return [
            $no++,
            $row->nim,
            $row->nama,
            $row->email,
            $row->is_aktif,
            $row->judul_kp,
            $row->instansi_nama,
            $row->dosen_pembimbing,
            $row->status_kp,
            $row->tanggal_sidang,
            $row->nilai_akhir,
            $row->grade,
            $row->status_kelulusan,
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

    private function calculateFinalLogic($sidang)
    {
        $status = $sidang->status_kelulusan;

        if ($status === 'Lanjut' || $status === 'Tidak Lulus') {
            return ['nilai' => '0', 'grade' => 'E'];
        }

        $nilaiFinal = (float) $sidang->nilai_akhir;

        if ($nilaiFinal <= 0) {
            $pembimbing = (float) ($sidang->nilai_pembimbing ?? 0) * 0.4;
            $supervisor = (float) ($sidang->nilai_supervisor ?? 0) * 0.1;
            $penguji1 = (float) ($sidang->nilai_penguji_1 ?? 0) * 0.25;
            $penguji2 = (float) ($sidang->nilai_penguji_2 ?? 0) * 0.25;
            $nilaiFinal = $pembimbing + $supervisor + $penguji1 + $penguji2;
        }

        $revisiVerified = ($sidang->status_revisi === 'Disahkan' || $sidang->status_revisi === 'Diterima');
        $originalGrade = $this->getGradeFromScore($nilaiFinal);
        $finalGrade = $originalGrade;

        if ($status === 'Lulus Dengan Revisi' && !$revisiVerified) {
            $finalGrade = $this->getPenalizedGrade($originalGrade);
        }

        return ['nilai' => $nilaiFinal, 'grade' => $finalGrade];
    }

    private function getGradeFromScore($nilai)
    {
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

    private function getPenalizedGrade($grade)
    {
        $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        $index = array_search($grade, $grades);
        return $grades[min($index + 3, count($grades) - 1)];
    }
}
