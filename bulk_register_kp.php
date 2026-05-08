<?php
use App\Models\User;
use App\Models\PendaftaranKp;
use App\Models\SupervisorInstansi;
use App\Models\TahunAjaran;

// 1. Get active Tahun Ajaran
$tahunAjaran = TahunAjaran::aktif()->id ?? null;

// 2. Get all Mahasiswa without a pending or approved KP
$mahasiswas = User::where('role', 'mahasiswa')->get()->filter(function ($user) {
    $hasKp = PendaftaranKp::where(function ($q) use ($user) {
        $q->where('mahasiswa_id', $user->id)
            ->orWhereJsonContains('anggota_kelompok_ids', (string) $user->id)
            ->orWhereJsonContains('anggota_kelompok_ids', $user->id);
    })->whereIn('status_kp', ['pending', 'approved'])->exists();

    return !$hasKp;
})->values();

// 3. Get all active Dosen
$dosens = User::whereIn('role', ['dosen', 'koordinator_kp'])
    ->whereHas('dosen', function ($query) {
        $query->where('is_aktif', 1);
    })->get()->shuffle(); // Shuffle for randomness

$dosenCount = $dosens->count();

if ($dosenCount > 0) {
    $dosenIndex = 0;

    foreach ($mahasiswas as $mhs) {
        $dosen = $dosens[$dosenIndex];

        // Create PendaftaranKp
        $kp = PendaftaranKp::create([
            'mahasiswa_id' => $mhs->id,
            'tahun_ajaran_id' => $tahunAjaran,
            'judul_kp' => 'Pengembangan Sistem Informasi (Proyek Internal)',
            'jenis_instansi' => 'Internal',
            'tipe_kp' => 'internal',
            'instansi_nama' => 'Universitas Kristen Krida Wacana',
            'supervisor_internal_id' => $dosen->id, // Supervisor is the dosen
            'jenis_proyek' => 'Proyek sistem informasi internal kampus, dikerjakan secara individu di bawah bimbingan ' . $dosen->name . '.',
            'status_kp' => 'pending',
            'pengerjaan_kp' => 'individu',
            'anggota_kelompok_ids' => null,
            // 'dosen_pemberi_projek_id' is not in DB, but supervisor_internal_id is the proxy for it.
        ]);

        // Create SupervisorInstansi
        SupervisorInstansi::create([
            'pendaftaran_kp_id' => $kp->id,
            'nama_supervisor' => $dosen->name,
            'email_supervisor' => null, // Explicitly null for Internal
        ]);

        // Round robin distribution
        $dosenIndex = ($dosenIndex + 1) % $dosenCount;
    }
}

// 4. Remove email_supervisor for ALL Internal KPs in the system
$internalKpIds = PendaftaranKp::where('jenis_instansi', 'Internal')->pluck('id');
SupervisorInstansi::whereIn('pendaftaran_kp_id', $internalKpIds)->update(['email_supervisor' => null]);

echo "Bulk registration complete. Total registered: " . $mahasiswas->count() . ". Emails cleared for all internal KPs.";
