<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use App\Models\User;

class DataRecoveryController extends Controller
{
    public function index()
    {
        // Cari semua Pendaftaran KP yang mahasiswa_id nya tidak ada di tabel users
        $orphanedKps = PendaftaranKp::withoutGlobalScope('periode')
            ->whereNotIn('mahasiswa_id', User::select('id'))
            ->get();
            
        // Cari semua Pendaftaran Sidang yang mahasiswa_id nya tidak ada di tabel users
        $orphanedSidangs = PendaftaranSidang::withoutGlobalScope('periode')
            ->whereNotIn('mahasiswa_id', User::select('id'))
            ->get();
            
        // Ambil semua mahasiswa aktif saat ini (untuk opsi tujuan pemulihan)
        $activeMahasiswas = User::where('role', 'mahasiswa')
            ->join('mahasiswa', 'users.id', '=', 'mahasiswa.user_id')
            ->select('users.id', 'users.name', 'mahasiswa.nim')
            ->orderBy('users.name')
            ->get();

        return view('koordinator.pemulihan-data', compact('orphanedKps', 'orphanedSidangs', 'activeMahasiswas'));
    }

    public function recoverKp(Request $request)
    {
        $request->validate([
            'kp_id' => 'required|exists:pendaftaran_kp,id',
            'new_user_id' => 'required|exists:users,id'
        ]);

        $kp = PendaftaranKp::withoutGlobalScope('periode')->findOrFail($request->kp_id);
        $oldUserId = $kp->mahasiswa_id;
        
        DB::beginTransaction();
        try {
            // Update KP
            $kp->mahasiswa_id = $request->new_user_id;
            $kp->save();
            
            // Update Sidang terkait jika ada
            PendaftaranSidang::withoutGlobalScope('periode')
                ->where('pendaftaran_kp_id', $kp->id)
                ->update(['mahasiswa_id' => $request->new_user_id]);
                
            // Update Array anggota kelompok di KP lain jika dia anggota
            $kpsWithMember = PendaftaranKp::withoutGlobalScope('periode')
                ->whereJsonContains('anggota_kelompok_ids', (string)$oldUserId)
                ->orWhereJsonContains('anggota_kelompok_ids', (int)$oldUserId)
                ->get();
                
            foreach ($kpsWithMember as $memberKp) {
                $anggota = $memberKp->anggota_kelompok_ids ?? [];
                // Replace old ID with new ID
                $anggota = array_map(function($id) use ($oldUserId, $request) {
                    return ((string)$id === (string)$oldUserId) ? (string)$request->new_user_id : (string)$id;
                }, $anggota);
                
                $memberKp->anggota_kelompok_ids = array_values(array_unique($anggota));
                $memberKp->save();
            }

            DB::commit();
            return back()->with('success', 'Data Pendaftaran KP dan Sidang terkait berhasil dipulihkan ke mahasiswa yang dipilih.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memulihkan data: ' . $e->getMessage());
        }
    }
}
