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
        
        // Hitung estimasi berkas di Storj
        $storjFilesCount = PendaftaranSidang::whereNotNull('file_laporan')->count() +
                           PendaftaranSidang::whereNotNull('file_log_bimbingan')->count() +
                           PendaftaranSidang::whereNotNull('file_revisi')->count();
        $storjMax = '25 GB'; // Storj Free Tier Limit
        $neonMax = '500 MB'; // Neon Free Tier Limit

        return view('koordinator.backup', compact('periodes', 'dbSize', 'neonMax', 'storjFilesCount', 'storjMax'));
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
            Excel::store(new ArchivedPeriodeExport($periodeId, $periode->label_tahun_ajaran), $excelFileName, 'local');
            File::move(storage_path('app/' . $excelFileName), $excelPath);

            // 2. Kumpulkan file dari Storj (S3)
            $kps = PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->get();
            $sidangs = PendaftaranSidang::withoutGlobalScope('periode')
                ->whereIn('pendaftaran_kp_id', $kps->pluck('id'))
                ->get();

            $subDirs = ['File_Laporan_KP', 'File_Logbook', 'File_Revisi_Sidang'];
            foreach ($subDirs as $dir) {
                File::makeDirectory($tmpDir . '/' . $dir, 0755, true);
            }

            foreach ($sidangs as $sidang) {
                if ($sidang->file_laporan && Storage::disk('s3')->exists($sidang->file_laporan)) {
                    $content = Storage::disk('s3')->get($sidang->file_laporan);
                    File::put($tmpDir . '/File_Laporan_KP/' . basename($sidang->file_laporan), $content);
                }
                if ($sidang->file_log_bimbingan && Storage::disk('s3')->exists($sidang->file_log_bimbingan)) {
                    $content = Storage::disk('s3')->get($sidang->file_log_bimbingan);
                    File::put($tmpDir . '/File_Logbook/' . basename($sidang->file_log_bimbingan), $content);
                }
                if ($sidang->file_revisi && Storage::disk('s3')->exists($sidang->file_revisi)) {
                    $content = Storage::disk('s3')->get($sidang->file_revisi);
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
            }

            // Bersihkan tmpDir
            File::deleteDirectory($tmpDir);

            // Return ZIP Download
            return response()->download($zipPath)->deleteFileAfterSend(true);

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
        $request->validate(['periode_id' => 'required', 'konfirmasi' => 'required|in:HAPUS']);
        $periodeId = $request->periode_id;
        $periode = TahunAjaran::findOrFail($periodeId);

        try {
            DB::beginTransaction();

            $kps = PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->get();
            $sidangs = PendaftaranSidang::withoutGlobalScope('periode')
                ->whereIn('pendaftaran_kp_id', $kps->pluck('id'))
                ->get();

            // 1. Hapus File dari S3
            foreach ($sidangs as $sidang) {
                if ($sidang->file_laporan) Storage::disk('s3')->delete($sidang->file_laporan);
                if ($sidang->file_log_bimbingan) Storage::disk('s3')->delete($sidang->file_log_bimbingan);
                if ($sidang->file_persetujuan_pembimbing) Storage::disk('s3')->delete($sidang->file_persetujuan_pembimbing);
                if ($sidang->file_nilai_supervisor) Storage::disk('s3')->delete($sidang->file_nilai_supervisor);
                if ($sidang->file_berkas_lainnya) Storage::disk('s3')->delete($sidang->file_berkas_lainnya);
                if ($sidang->file_revisi) Storage::disk('s3')->delete($sidang->file_revisi);
            }

            // 2. Hapus Data dari Database
            // Hapus Pendaftaran Sidang
            PendaftaranSidang::withoutGlobalScope('periode')->whereIn('pendaftaran_kp_id', $kps->pluck('id'))->delete();
            // Hapus Pendaftaran KP
            PendaftaranKp::withoutGlobalScope('periode')->where('tahun_ajaran_id', $periodeId)->delete();
            // Hapus Mahasiswa
            Mahasiswa::where('tahun_ajaran_id', $periodeId)->delete();

            DB::commit();

            return back()->with('success', 'Semua data dan file dari periode ' . $periode->label_tahun_ajaran . ' berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membersihkan data: ' . $e->getMessage());
        }
    }
}
