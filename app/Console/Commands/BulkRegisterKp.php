<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PendaftaranKp;
use App\Models\SupervisorInstansi;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BulkRegisterKp extends Command
{
    protected $signature = 'app:bulk-register-kp';
    protected $description = 'Register KP for all unregistered students according to rules';

    public function handle()
    {
        $this->info("Memulai proses pendaftaran massal...");

        $allMahasiswa = User::where('role', 'mahasiswa')->with('mahasiswa')->get();
        $dosens = User::where('role', 'dosen')->whereHas('dosen', fn($q) => $q->where('is_aktif', 1))->get();
        
        if ($dosens->isEmpty()) {
            $this->error("Tidak ada dosen aktif untuk dijadikan pembimbing/supervisor.");
            return;
        }

        $instansis = ['PT. Gojek Indonesia', 'PT. Tokopedia', 'Traveloka', 'Ruangguru', 'Bukalapak', 'PT. Telkom Indonesia', 'Bank Mandiri'];
        $judulTemplates = [
            'Sistem Informasi Manajemen Aset',
            'Aplikasi E-Learning Berbasis Mobile',
            'Pengembangan Backend API Layanan Publik',
            'Sistem Monitoring IoT Pertanian',
            'Platform E-Commerce Produk Lokal',
            'Sistem Keamanan Jaringan Perusahaan'
        ];

        // 1. Identifikasi siapa yang sudah punya PendaftaranKp (status aktif)
        $existingMhsIds = PendaftaranKp::whereIn('status_kp', ['pending', 'approved', 'verified', 'rejected'])
            ->pluck('mahasiswa_id')
            ->toArray();

        $unregisteredMhs = $allMahasiswa->filter(fn($m) => !in_array($m->id, $existingMhsIds));

        $this->info("Ditemukan " . $unregisteredMhs->count() . " mahasiswa yang belum memiliki rekaman pendaftaran.");

        if ($unregisteredMhs->isEmpty()) {
            $this->info("Semua mahasiswa sudah terdaftar.");
            return;
        }

        DB::beginTransaction();
        try {
            $processedIds = [];

            foreach ($unregisteredMhs as $mhs) {
                if (in_array($mhs->id, $processedIds)) continue;

                // 2. Cek apakah dia diundang di kelompok lain
                $invitation = PendaftaranKp::where(function ($query) use ($mhs) {
                    $query->whereJsonContains('anggota_kelompok_ids', (string) $mhs->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $mhs->id);
                })
                ->whereIn('status_kp', ['pending', 'approved'])
                ->latest()
                ->first();

                if ($invitation) {
                    $this->processInvitation($mhs, $invitation, $processedIds);
                    continue;
                }

                $this->processRandomRegistration($mhs, $unregisteredMhs, $processedIds, $dosens, $instansis, $judulTemplates);
            }

            DB::commit();
            $this->info("Selesai. Berhasil memproses mahasiswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }

    private function processInvitation($mhs, $invitation, &$processedIds)
    {
        $this->line("Mahasiswa {$mhs->name} diundang dalam kelompok: {$invitation->judul_kp}. Mendaftarkan secara otomatis...");
        
        $newKp = PendaftaranKp::create([
            'mahasiswa_id' => $mhs->id,
            'judul_kp' => $invitation->judul_kp,
            'jenis_instansi' => $invitation->jenis_instansi,
            'tipe_kp' => $invitation->tipe_kp,
            'instansi_nama' => $invitation->instansi_nama,
            'supervisor_internal_id' => $invitation->supervisor_internal_id,
            'pembimbing_id' => $invitation->pembimbing_id,
            'jenis_proyek' => $invitation->jenis_proyek,
            'status_kp' => $invitation->status_kp,
            'pengerjaan_kp' => 'kelompok',
            'anggota_kelompok_ids' => $invitation->anggota_kelompok_ids,
        ]);

        if ($invitation->supervisorInstansi) {
            SupervisorInstansi::create([
                'pendaftaran_kp_id' => $newKp->id,
                'nama_supervisor' => $invitation->supervisorInstansi->nama_supervisor,
                'email_supervisor' => $invitation->supervisorInstansi->email_supervisor,
            ]);
        }
        $processedIds[] = $mhs->id;
    }

    private function processRandomRegistration($mhs, $unregisteredMhs, &$processedIds, $dosens, $instansis, $judulTemplates)
    {
        // 3. Jika benar-benar bebas, buat baru (Individu atau Kelompok)
        $mode = (rand(0, 1) === 1) ? 'kelompok' : 'individu';
        
        // Cari rekan jika kelompok
        $rekanIds = [];
        if ($mode === 'kelompok') {
            $potentialRekans = $unregisteredMhs->filter(fn($u) => 
                $u->id !== $mhs->id && 
                !in_array($u->id, $processedIds)
            );
            
            // Cek lagi apakah rekan tersebut tidak diundang di tempat lain
            $validRekans = [];
            foreach ($potentialRekans as $pr) {
                $isInvitedElsewhere = PendaftaranKp::where(function ($q) use ($pr) {
                    $q->whereJsonContains('anggota_kelompok_ids', (string) $pr->id)
                        ->orWhereJsonContains('anggota_kelompok_ids', $pr->id);
                })->whereIn('status_kp', ['pending', 'approved'])->exists();
                
                if (!$isInvitedElsewhere) {
                    $validRekans[] = $pr;
                }
            }

            if (count($validRekans) > 0) {
                $num = rand(1, min(2, count($validRekans)));
                $rekanObjs = array_slice($validRekans, 0, $num);
                $rekanIds = collect($rekanObjs)->pluck('id')->map(fn($id) => (string)$id)->toArray();
            } else {
                $mode = 'individu';
            }
        }

        // Data dasar
        $dosen = $dosens->random();
        $jenis = (rand(0, 1) === 1) ? 'Internal' : 'External';
        $instansi = $jenis === 'Internal' ? 'Universitas Kristen Krida Wacana' : $instansis[array_rand($instansis)];
        $judul = $judulTemplates[array_rand($judulTemplates)] . " " . rand(100, 999);
        
        $membersToRegister = array_merge([$mhs->id], array_map('intval', $rekanIds));
        $allMemberIdsStrings = array_map('strval', $membersToRegister);

        foreach ($membersToRegister as $memberId) {
            // Filter members list to exclude self
            $myRekanIds = array_values(array_filter($allMemberIdsStrings, fn($id) => $id != $memberId));
            
            $newKp = PendaftaranKp::create([
                'mahasiswa_id' => $memberId,
                'judul_kp' => $judul,
                'jenis_instansi' => $jenis,
                'tipe_kp' => strtolower($jenis),
                'instansi_nama' => $instansi,
                'supervisor_internal_id' => $dosen->id,
                'jenis_proyek' => 'Deskripsi proyek otomatis untuk ' . $judul,
                'status_kp' => 'pending',
                'pengerjaan_kp' => $mode,
                'anggota_kelompok_ids' => ($mode === 'kelompok' && !empty($myRekanIds)) ? $myRekanIds : null,
            ]);

            SupervisorInstansi::create([
                'pendaftaran_kp_id' => $newKp->id,
                'nama_supervisor' => 'Supervisor Auto',
                'email_supervisor' => 'supervisor@' . Str::slug($instansi) . '.com',
            ]);

            $processedIds[] = $memberId;
        }

        $this->line("Terdaftar " . ($mode === 'kelompok' ? "Kelompok (" . count($membersToRegister) . " orang)" : "Individu") . ": $judul");
    }
}
