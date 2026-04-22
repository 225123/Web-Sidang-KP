<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Support\Facades\Storage;

$filePath = 'berkas_sidang/dummy_berkas.pdf';

// Make sure every Mahasiswa has a PendaftaranKp
$allMahasiswa = Mahasiswa::all();
$defaultDosen = Dosen::first();

foreach ($allMahasiswa as $mhs) {
    $kp = PendaftaranKp::where('mahasiswa_id', $mhs->user_id)->orWhereJsonContains('anggota_kelompok_ids', $mhs->user_id)->first();
    
    if (!$kp) {
        $kp = new PendaftaranKp();
        $kp->mahasiswa_id = $mhs->user_id;
        $kp->judul_kp = 'Sistem Informasi Dummy';
        $kp->instansi_nama = 'Dummy Company Untuk Memenuhi 52 Mahasiswa';
        $kp->instansi_alamat = 'Alamat Dummy';
        $kp->status_kp = 'approved';
        $kp->pembimbing_id = $defaultDosen ? $defaultDosen->user_id : null;
        $kp->pengerjaan_kp = 'individu';
        $kp->tahun_ajaran_id = 1;
        $kp->save();
    } else {
        if ($kp->status_kp !== 'approved') {
            $kp->status_kp = 'approved';
            $kp->save();
        }
    }
}

// Now all KPs that are approved
$kps = PendaftaranKp::where('status_kp', 'approved')->get();

$count = 0;
foreach ($kps as $kp) {
    // Collect all mahasiswa_id for this KP
    $mahasiswaIds = [$kp->mahasiswa_id]; // Leader
    
    if ($kp->pengerjaan_kp === 'kelompok' && !empty($kp->anggota_kelompok_ids)) {
        $anggota = is_string($kp->anggota_kelompok_ids) ? json_decode($kp->anggota_kelompok_ids, true) : $kp->anggota_kelompok_ids;
        if (is_array($anggota)) {
            foreach ($anggota as $id) {
                if (!in_array($id, $mahasiswaIds)) {
                    $mahasiswaIds[] = $id;
                }
            }
        }
    }
    
    foreach ($mahasiswaIds as $mhsId) {
        // Create or update PendaftaranSidang for EACH student
        $sidang = PendaftaranSidang::firstOrNew([
            'pendaftaran_kp_id' => $kp->id,
            'mahasiswa_id' => $mhsId
        ]);
        
        $sidang->file_laporan = $filePath;
        $sidang->file_log_bimbingan = $filePath;
        $sidang->file_persetujuan_pembimbing = $filePath;
        $sidang->file_nilai_supervisor = $filePath;
        $sidang->file_berkas_lainnya = $filePath;
        
        $sidang->link_github = 'https://github.com/dummy-project';
        $sidang->link_drive = 'https://drive.google.com/drive/folders/dummy';
        $sidang->link_deploy = 'https://dummy-app.com';
        
        $sidang->status_verifikasi = 'verified'; // ACC by Dosen
        $sidang->status_koordinator = 'verified'; // ACC by Koordinator
        
        $sidang->tahun_ajaran_id = $kp->tahun_ajaran_id ?? 1;
        
        $sidang->save();
        $count++;
    }
}

echo "Successfully populated $count PendaftaranSidang records with dummy data and verified status.\n";
