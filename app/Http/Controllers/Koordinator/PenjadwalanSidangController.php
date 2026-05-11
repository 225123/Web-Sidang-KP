<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PendaftaranSidang;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PenjadwalanSidangController extends Controller
{
    public function index(Request $request)
    {
        // Safety net: filter periode secara eksplisit (Global Scope juga sudah menangani ini)
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        // Daftar Tunggu: status koordinator verified tapi tanggal sidang masih kosong.
        $daftarTunggu = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp'])
            ->when($periodeId, function($q) use ($periodeId) {
                $q->whereHas('pendaftaranKp', fn($sq) => $sq->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId));
            })
            ->where('status_koordinator', 'verified')
            ->whereNull('tanggal_sidang')
            ->get()
            ->sortBy('mahasiswa.nim')
            ->values()
            ->map(function ($item) {
                return [
                    'id' => data_get($item, 'id'),
                    'name' => data_get($item, 'mahasiswa.user.name') ?? '-',
                    'nim' => data_get($item, 'mahasiswa.nim') ?? '-',
                ];
            });

        // Sudah Terjadwal: status koordinator verified dan tanggal sidang sudah ada.
        $terjadwal = PendaftaranSidang::with(['mahasiswa.user', 'pendaftaranKp', 'penguji1', 'penguji2'])
            ->when($periodeId, function($q) use ($periodeId) {
                $q->whereHas('pendaftaranKp', fn($sq) => $sq->withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId));
            })
            ->where('status_koordinator', 'verified')
            ->whereNotNull('tanggal_sidang')
            ->get()
            ->sortBy('mahasiswa.nim')
            ->values()
            ->map(function ($item) {
                return [
                    'id' => data_get($item, 'id'),
                    'name' => data_get($item, 'mahasiswa.user.name') ?? '-',
                    'nim' => data_get($item, 'mahasiswa.nim') ?? '-',
                    'judul' => data_get($item, 'pendaftaranKp.judul_kp') ?? '-',
                    'tanggal' => data_get($item, 'tanggal_sidang'),
                    'mulai' => data_get($item, 'waktu_mulai_sidang'),
                    'selesai' => data_get($item, 'waktu_selesai_sidang'),
                    'ruang' => data_get($item, 'ruang_sidang') ?? '-',
                    'status' => data_get($item, 'status_jadwal'),
                ];
            });

        if ($request->wantsJson()) {
            return response()->json([
                'daftarTunggu' => $daftarTunggu,
                'terjadwal' => $terjadwal,
            ]);
        }

        $totalMahasiswa = $daftarTunggu->count() + $terjadwal->count();

        return view('koordinator.penjadwalan-sidang', [
            'daftarTunggu' => $daftarTunggu,
            'terjadwal' => $terjadwal,
            'totalMahasiswa' => $totalMahasiswa,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:pendaftaran_sidang,id',
            'tanggal_sidang' => 'required|date',
            'waktu_mulai_sidang' => 'required',
            'waktu_selesai_sidang' => 'required',
            'ruang_sidang' => 'required|string|max:255',
        ]);

        $tanggal = $request->tanggal_sidang;
        $mulai = $request->waktu_mulai_sidang;
        $selesai = $request->waktu_selesai_sidang;
        $sidangId = $request->id;

        // Validasi Kapasitas Maksimal (Maksimum 3 mahasiswa pada jam yang sama)
        // Mengecek apakah ada jadwal pada tanggal yang sama, di mana jam bersinggungan.
        $overlappingCount = PendaftaranSidang::where('tanggal_sidang', $tanggal)
            ->where('id', '!=', $sidangId)
            ->where(function ($query) use ($mulai, $selesai) {
                // Ada overlap jika (MulaiA < SelesaiB) AND (SelesaiA > MulaiB)
                $query->where('waktu_mulai_sidang', '<', $selesai)
                    ->where('waktu_selesai_sidang', '>', $mulai);
            })
            ->count();

        if ($overlappingCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal: Kapasitas maksimal 3 mahasiswa per sesi telah penuh untuk slot waktu tersebut.',
            ], 422);
        }

        $sidang = PendaftaranSidang::findOrFail($sidangId);
        $sidang->update([
            'tanggal_sidang' => $tanggal,
            'waktu_mulai_sidang' => $mulai,
            'waktu_selesai_sidang' => $selesai,
            'ruang_sidang' => $request->ruang_sidang,
            'penguji_1_id' => null,
            'penguji_2_id' => null,
            'status_jadwal' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal sidang berhasil disimpan. Penugasan penguji telah direset.',
        ]);
    }

    public function destroySchedule($id)
    {
        $sidang = PendaftaranSidang::findOrFail($id);
        $sidang->update([
            'tanggal_sidang' => null,
            'waktu_mulai_sidang' => null,
            'waktu_selesai_sidang' => null,
            'ruang_sidang' => null,
            'status_jadwal' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal sidang dihapus & dikembalikan ke Daftar Tunggu.',
        ]);
    }

    public function autoSchedule(Request $request)
    {
        $dates = $request->input('dates', []);

        if (empty($dates)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih setidaknya satu tanggal untuk melakukan plotting otomatis.',
            ], 422);
        }

        sort($dates);

        $daftarTunggu = PendaftaranSidang::where('status_koordinator', 'verified')
            ->whereNull('tanggal_sidang')
            ->get()
            ->sortBy('mahasiswa.nim');

        if ($daftarTunggu->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada mahasiswa di daftar tunggu untuk diplot otomatis.',
            ], 422);
        }

        $assignedCount = 0;
        $studentIndex = 0;
        $totalStudents = $daftarTunggu->count();
        $dateCount = count($dates);

        // Calculate balanced distribution limit
        $limitPerDay = ceil($totalStudents / $dateCount);
        // Cap at 18/day (3 streams * 6 hours, approx)
        if ($limitPerDay > 18) {
            $limitPerDay = 18;
        }

        foreach ($dates as $dateStr) {
            $studentsOnThisDate = 0;
            // Setiap tanggal mulai dari jam 8 pagi
            $currentSlot = Carbon::parse($dateStr)->setTime(8, 0, 0);

            while ($studentIndex < $totalStudents && $studentsOnThisDate < $limitPerDay) {
                // Batas waktu: Jam kerja berakhir jam 4 sore (16:00)
                if ($currentSlot->format('H:i') >= '16:00') {
                    break; // Pindah ke tanggal berikutnya
                }

                $formattedMulai = $currentSlot->format('H:i:s');
                $formattedSelesai = $currentSlot->copy()->addHour()->format('H:i:s');

                // Kapasitas 3 stream per slot (parallel)
                // Buffer jeda antar sidang 30 menit
                $occupiedUntil = $currentSlot->copy()->addMinutes(90)->format('H:i:s');
                $bufferMulai = $currentSlot->copy()->subMinutes(30)->format('H:i:s');

                $overlappingCount = PendaftaranSidang::where('tanggal_sidang', $dateStr)
                    ->where(function ($q) use ($bufferMulai, $occupiedUntil) {
                        $q->where('waktu_mulai_sidang', '<', $occupiedUntil)
                            ->where('waktu_selesai_sidang', '>', $bufferMulai);
                    })
                    ->count();

                if ($overlappingCount < 3) {
                    $sidang = $daftarTunggu->values()[$studentIndex];
                    $sidang->update([
                        'tanggal_sidang' => $dateStr,
                        'waktu_mulai_sidang' => $formattedMulai,
                        'waktu_selesai_sidang' => $formattedSelesai,
                        'ruang_sidang' => '-',
                        'penguji_1_id' => null,
                        'penguji_2_id' => null,
                        'status_jadwal' => 'draft',
                    ]);
                    $studentIndex++;
                    $assignedCount++;
                    $studentsOnThisDate++;

                    // Cek lagi di jam yang sama untuk stream berikutnya (parallel)
                    continue;
                }

                // Jika sudah 3 paralel, geser waktu 30 menit
                $currentSlot->addMinutes(30);
            }

            if ($studentIndex >= $totalStudents) {
                break;
            }
        }

        $message = "Berhasil mem-plot {$assignedCount} mahasiswa secara otomatis.";
        if ($studentIndex < $totalStudents) {
            $remaining = $totalStudents - $studentIndex;
            $message .= " Namun {$remaining} mahasiswa belum ter-plot karena keterbatasan tanggal/waktu yang Anda pilih.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada mahasiswa yang dipilih.']);
        }

        PendaftaranSidang::whereIn('id', $ids)->update([
            'tanggal_sidang' => null,
            'waktu_mulai_sidang' => null,
            'waktu_selesai_sidang' => null,
            'ruang_sidang' => null,
            'status_jadwal' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => count($ids).' jadwal berhasil dibatalkan.',
        ]);
    }
}
