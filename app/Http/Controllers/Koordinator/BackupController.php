<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\BackupHistory;
use App\Exports\ArchivedPeriodeExport;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $periodes = TahunAjaran::orderBy('created_at', 'desc')->get();
        
        // Cek kapasitas database (PostgreSQL)
        $dbSize = 'Tidak diketahui';
        try {
            $dbName = env('DB_DATABASE');
            $sizeResult = DB::selectOne("SELECT pg_size_pretty(pg_database_size(?)) as size", [$dbName]);
            if ($sizeResult) {
                $dbSize = $sizeResult->size;
            }
        } catch (\Exception $e) {
            // Abaikan jika query tidak kompatibel
        }
        
        // Hitung estimasi berkas di Cloud
        $cloudFilesCount = PendaftaranSidang::withoutGlobalScope('periode')->whereNotNull('file_laporan')->count() +
                           PendaftaranSidang::withoutGlobalScope('periode')->whereNotNull('file_log_bimbingan')->count() +
                           PendaftaranSidang::withoutGlobalScope('periode')->whereNotNull('file_persetujuan_pembimbing')->count() +
                           PendaftaranSidang::withoutGlobalScope('periode')->whereNotNull('file_nilai_supervisor')->count() +
                           PendaftaranSidang::withoutGlobalScope('periode')->whereNotNull('file_berkas_lainnya')->count() +
                           PendaftaranSidang::withoutGlobalScope('periode')->whereNotNull('file_revisi')->count() +
                           \App\Models\LogBimbingan::whereNotNull('file_progress')->count();
        
        $uploadDisk = upload_disk();
        $cloudStorageName = strtoupper($uploadDisk) . ' Storage';
        if ($uploadDisk == 'google') {
            $cloudStorageName = 'Google Drive';
            $cloudMax = '15 GB';
        } elseif ($uploadDisk == 'storj') {
            $cloudStorageName = 'S3 Storage (Storj)';
            $cloudMax = '25 GB';
        } else {
            $cloudMax = '10 GB';
        }
        $neonMax = '500 MB'; // Neon Free Tier Limit

        $histories = BackupHistory::with('tahunAjaran', 'koordinator')->latest()->get();

        return view('koordinator.backup', compact('periodes', 'dbSize', 'neonMax', 'cloudFilesCount', 'cloudMax', 'cloudStorageName', 'histories'));
    }

    public function downloadZip(Request $request)
    {
        $request->validate(['periode_id' => 'required']);
        $periodeId = $request->periode_id;
        $periode = TahunAjaran::findOrFail($periodeId);

        $periodeNameSafe = str_replace('/', '_', $periode->label_tahun_ajaran);
        $zipFileName = "Backup_KP_Periode_{$periodeNameSafe}.zip";
        
        // Buat folder temporary
        $tmpDir = sys_get_temp_dir() . '/backup_' . uniqid();
        File::makeDirectory($tmpDir, 0755, true);

        try {
            // 1. Generate Excel Data
            $excelFileName = "Data_Lengkap_Periode_{$periodeNameSafe}.xlsx";
            $excelPath = $tmpDir . '/' . $excelFileName;
            
            // Buat Ad-hoc disk untuk Vercel /tmp
            config(['filesystems.disks.backup_tmp' => ['driver' => 'local', 'root' => $tmpDir]]);
            Excel::store(new ArchivedPeriodeExport($periodeId, $periode->label_tahun_ajaran), $excelFileName, 'backup_tmp');

            // 2. Kumpulkan file dari Storj (S3)
            $kps = PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->get();
            $sidangs = PendaftaranSidang::withoutGlobalScope('periode')
                ->whereIn('pendaftaran_kp_id', $kps->pluck('id'))
                ->get();

            $subDirs = ['File_Laporan_KP', 'File_Logbook', 'File_Revisi_Sidang'];
            foreach ($subDirs as $dir) {
                File::makeDirectory($tmpDir . '/' . $dir, 0755, true);
            }

            $diskName = env('FILESYSTEM_DISK', 'public');
            foreach ($sidangs as $sidang) {
                if ($sidang->file_laporan && Storage::disk($diskName)->exists($sidang->file_laporan)) {
                    $content = Storage::disk($diskName)->get($sidang->file_laporan);
                    File::put($tmpDir . '/File_Laporan_KP/' . basename($sidang->file_laporan), $content);
                }
                if ($sidang->file_log_bimbingan && Storage::disk($diskName)->exists($sidang->file_log_bimbingan)) {
                    $content = Storage::disk($diskName)->get($sidang->file_log_bimbingan);
                    File::put($tmpDir . '/File_Logbook/' . basename($sidang->file_log_bimbingan), $content);
                }
                if ($sidang->file_revisi && Storage::disk($diskName)->exists($sidang->file_revisi)) {
                    $content = Storage::disk($diskName)->get($sidang->file_revisi);
                    File::put($tmpDir . '/File_Revisi_Sidang/' . basename($sidang->file_revisi), $content);
                }
            }

            // 3. Buat ZIP
            $zipPath = sys_get_temp_dir() . '/' . $zipFileName;
            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tmpDir));
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($tmpDir) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
                $zip->close();
            } else {
                return back()->with('error', 'Gagal membuat file ZIP.');
            }

            // Catat ke riwayat
            BackupHistory::create([
                'koordinator_id' => auth()->id(),
                'tahun_ajaran_id' => $periodeId,
                'periode_name' => $periode->label_tahun_ajaran,
                'file_name' => $zipFileName,
            ]);

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Bersihkan tmpDir jika error
            if (File::exists($tmpDir)) {
                File::deleteDirectory($tmpDir);
            }
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function purgePeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:tahun_ajaran,id',
            'konfirmasi' => 'required|in:HAPUS'
        ]);

        $activePeriodsCount = TahunAjaran::count();
        if ($activePeriodsCount <= 1) {
            return back()->with('error', 'Pemusnahan dibatalkan: Anda tidak dapat menghapus satu-satunya periode yang tersisa. Sistem mewajibkan setidaknya ada 1 periode agar dapat beroperasi.');
        }

        $periodeId = $request->periode_id;
        $periode = TahunAjaran::findOrFail($periodeId);

        try {
            DB::beginTransaction();

            $kps = PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->get();
            $sidangs = PendaftaranSidang::withoutGlobalScope('periode')
                ->whereIn('pendaftaran_kp_id', $kps->pluck('id'))
                ->get();

            // 1. Hapus File dari Cloud
            $diskName = env('FILESYSTEM_DISK', 'public');
            foreach ($sidangs as $sidang) {
                if ($sidang->file_laporan) Storage::disk($diskName)->delete($sidang->file_laporan);
                if ($sidang->file_log_bimbingan) Storage::disk($diskName)->delete($sidang->file_log_bimbingan);
                if ($sidang->file_persetujuan_pembimbing) Storage::disk($diskName)->delete($sidang->file_persetujuan_pembimbing);
                if ($sidang->file_nilai_supervisor) Storage::disk($diskName)->delete($sidang->file_nilai_supervisor);
                if ($sidang->file_berkas_lainnya) Storage::disk($diskName)->delete($sidang->file_berkas_lainnya);
                if ($sidang->file_revisi) Storage::disk($diskName)->delete($sidang->file_revisi);
            }

            // 2. Hapus Data dari Database
            
            // Rekam statistik secara abadi di wadah periode
            $periode->total_mahasiswa = PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->count();
            $dosenIds = PendaftaranKp::withoutGlobalScope('periode')
                ->where('tahun_ajaran_id', $periodeId)
                ->whereNotNull('pembimbing_id')
                ->pluck('pembimbing_id')
                ->push($periode->koordinator_id)
                ->unique()
                ->filter();
            $periode->total_dosen = $dosenIds->count();
            $periode->save();

            // Karena relasi on cascade delete tidak diset untuk semua tabel, kita harus hapus manual
            // Hapus Pendaftaran Sidang
            PendaftaranSidang::withoutGlobalScope('periode')->whereIn('pendaftaran_kp_id', $kps->pluck('id'))->delete();
            // Hapus Pendaftaran KP
            PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->delete();
            // Hapus Mahasiswa yang terkait periode ini
            Mahasiswa::where('tahun_ajaran_id', $periodeId)->delete();

            // Hapus Tahun Ajaran (Periode) agar tidak muncul lagi di dropdown user lain
            $periode->delete();

            DB::commit();

            return back()->with('success', 'Semua data dan file dari periode ' . $periode->label_tahun_ajaran . ' berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membersihkan data: ' . $e->getMessage());
        }
    }
}
