<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\PendaftaranKp;
use App\Models\SupervisorInstansi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyKpSeeder extends Seeder
{
    public function run()
    {
        // 1. Create 10 Dosen (including Koordinator)
        $dsnNames = [
            'Prof. Dr. Anton', 'Dr. Budi', 'Dr. Cecep', 'Ir. Doni MTA', 'Edwin, S.Kom, M.Kom',
            'Faisal, P.hD', 'Gina, S.T, M.T', 'Hari MT', 'Irwan, M.Eng', 'Joko, S.Kom, M.MSI',
        ];

        $dosens = [];
        foreach ($dsnNames as $i => $name) {
            $u = User::firstOrCreate(['email' => 'dosen'.($i + 1).'@ukrida.ac.id'], [
                'name' => $name,
                'password' => Hash::make('password'),
                'role' => $i === 0 ? 'koordinator_kp' : 'dosen',
            ]);
            Dosen::firstOrCreate(['user_id' => $u->id], [
                'nidn' => '00000000'.str_pad($i, 2, '0', STR_PAD_LEFT),
                'kuota_bimbingan' => 5,
                'is_aktif' => true,
            ]);
            $dosens[] = $u;
        }

        // 2. Create 50 Mahasiswa
        $mahasiswas = [];
        $counter = 1;
        while (count($mahasiswas) < 50) {
            $nimStr = str_pad($counter, 2, '0', STR_PAD_LEFT);
            if ($nimStr === '24' || $nimStr === '25') {
                $counter++;

                continue;
            }

            $nim = '4120230'.$nimStr;
            $nama = 'Mahasiswa Default '.$counter;

            $u = User::firstOrCreate(['email' => $nim.'@ukrida.ac.id'], [
                'name' => $nama,
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]);

            Mahasiswa::firstOrCreate(['user_id' => $u->id], [
                'nim' => $nim,
                'prodi' => 'Informatika',
            ]);

            $mahasiswas[] = $u;
            $counter++;
        }

        // 3. Create Pendaftaran KP (Approved)
        // Solos: 15, Groups of 2: 12, Groups of 3: 3 --> total 15 + 24 + 9 = 48 mahasiswa users
        $configs = array_merge(
            array_fill(0, 15, 1),
            array_fill(0, 12, 2),
            array_fill(0, 3, 3)
        );

        // Ensure we have a valid Tahun Ajaran via DB builder since model doesn't exist
        $ta = DB::table('tahun_ajaran')->where('tahun', '2025')->first();
        if (! $ta) {
            $taId = DB::table('tahun_ajaran')->insertGetId([
                'tahun' => '2025',
                'label_tahun_ajaran' => '2025/2026',
                'is_active' => true,
                'semester' => 'Genap',
            ]);
            $ta = DB::table('tahun_ajaran')->find($taId);
        }

        $mhsIndex = 0;
        foreach ($configs as $pIndex => $groupSize) {
            if ($mhsIndex >= count($mahasiswas)) {
                break;
            }

            $mainMhs = $mahasiswas[$mhsIndex];
            $groupMembers = [];

            for ($i = 0; $i < $groupSize; $i++) {
                if ($mhsIndex < count($mahasiswas)) {
                    $groupMembers[] = (string) $mahasiswas[$mhsIndex]->id; // Cast to string if needed by JSON
                    $mhsIndex++;
                }
            }

            $kp = clone PendaftaranKp::create([
                'mahasiswa_id' => $mainMhs->id,
                'tahun_ajaran_id' => $ta->id,
                'judul_kp' => 'Aplikasi Manajemen '.rand(100, 999),
                'pengerjaan_kp' => $groupSize > 1 ? 'kelompok' : 'individu',
                'anggota_kelompok_ids' => $groupSize > 1 ? $groupMembers : null,
                'status_kp' => 'approved',
                'instansi_nama' => 'PT Teknologi Bintang '.rand(1, 40),
            ]);

            SupervisorInstansi::create([
                'pendaftaran_kp_id' => $kp->id,
                'nama_supervisor' => 'Bpk. Supervisor '.mt_rand(1, 99),
                'kontak_supervisor' => '0812'.rand(10000000, 99999999),
            ]);
        }
    }
}
