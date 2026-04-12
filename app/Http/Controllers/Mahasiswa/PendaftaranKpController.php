<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranKp;
use App\Models\SupervisorInstansi;
use App\Models\User;

class PendaftaranKpController extends Controller
{
    public function create()
    {
        $existingKp = PendaftaranKp::where('mahasiswa_id', auth()->id())
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();

        // Fetch all other users with role = mahasiswa, eager load mahasiswa relation to sort by NIM
        $allMahasiswa = User::with('mahasiswa')
            ->where('role', 'mahasiswa')
            ->where('id', '!=', auth()->id())
            ->get()
            ->sortBy(function($user) {
                return $user->mahasiswa->nim ?? (string)$user->id;
            })
            ->values();

        // Check if current user is invited into any group
        $invitation = PendaftaranKp::whereJsonContains('anggota_kelompok_ids', (string)auth()->id())
            ->orWhereJsonContains('anggota_kelompok_ids', auth()->id())
            ->latest()
            ->first();

        // If invited, we need to gather the actual group members for display
        $anggotaTerpilih = [];
        if ($invitation) {
            $anggotaIds = $invitation->anggota_kelompok_ids ?? [];
            // Add the creator of the group to the members list if not the current user
            if ($invitation->mahasiswa_id !== auth()->id()) {
                $anggotaIds[] = $invitation->mahasiswa_id;
            }
            // Remove current user from the display list of members
            $anggotaIds = array_diff($anggotaIds, [auth()->id(), (string)auth()->id()]);
            // Fetch names for display
            $anggotaTerpilih = User::whereIn('id', $anggotaIds)->get();
        }

        return view('mahasiswa.Pendaftaran-KP', compact('existingKp', 'allMahasiswa', 'invitation', 'anggotaTerpilih'));
    }

    public function dataKpSaya(Request $request)
    {
        $query = PendaftaranKp::where('mahasiswa_id', auth()->id());

        // Search by judul_kp or instansi_nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_kp', 'like', "%{$search}%")
                  ->orWhere('instansi_nama', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_kp', $request->status);
        }

        // Filter by jenis kp
        if ($request->filled('jenis')) {
            // Assumes 'internal' or 'external' mapping to jenis_instansi
            $query->where('jenis_instansi', ucfirst($request->jenis));
        }

        // Filter by periode
        if ($request->filled('periode')) {
            if ($request->periode === 'ganjil') {
                $query->whereMonth('created_at', '>', 6);
            } elseif ($request->periode === 'genap') {
                $query->whereMonth('created_at', '<=', 6);
            }
        }

        $riwayatKp = $query->latest()->paginate(10)->withQueryString();
            
        return view('mahasiswa.Status-Pendaftaran', compact('riwayatKp'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'judul_kp' => 'required|string|max:255',
            'jenis_instansi' => 'required|in:Internal,External',
            'instansi_nama' => 'required_if:jenis_instansi,External|nullable|string|max:255',
            'dosen_pemberi_projek' => 'required_if:jenis_instansi,Internal|nullable|string',
            'nama_supervisor' => 'required|string|max:255',
            'deskripsi_kp' => 'required|string',
            'pengerjaan_kp' => 'required|in:sendiri,berkelompok',
            'anggota_kelompok_ids' => 'nullable|string',
        ]);

        $existingKp = PendaftaranKp::where('mahasiswa_id', auth()->id())
            ->whereIn('status_kp', ['pending', 'approved'])
            ->first();
            
        if ($existingKp) {
            return redirect()->route('mahasiswa.pendaftaran-kp.create')->with('error', 'Anda sudah memiliki pendaftaran KP yang sedang diproses atau disetujui.');
        }

        try {
            $anggotaArray = [];
            if ($request->pengerjaan_kp === 'berkelompok' && $request->filled('anggota_kelompok_ids')) {
                // Decode the JSON array from frontend
                $anggotaArray = json_decode($request->anggota_kelompok_ids, true);
                if (!is_array($anggotaArray)) {
                    $anggotaArray = [];
                }
            }

            // check if there's an invitation to copy the status
            $invitation = PendaftaranKp::whereJsonContains('anggota_kelompok_ids', (string)auth()->id())
                ->orWhereJsonContains('anggota_kelompok_ids', auth()->id())
                ->latest()
                ->first();

            $status_kp = 'pending';
            if ($invitation && $request->pengerjaan_kp === 'berkelompok') {
                $status_kp = $invitation->status_kp; // Sync status if invited
            }

            $pendaftaranKp = PendaftaranKp::create([
                'mahasiswa_id' => auth()->id(),
                'judul_kp' => $request->judul_kp,
                'jenis_instansi' => $request->jenis_instansi,
                'tipe_kp' => strtolower($request->jenis_instansi),
                'instansi_nama' => $request->jenis_instansi === 'External' ? $request->instansi_nama : 'Universitas Kristen Krida Wacana',
                'supervisor_internal_id' => null, // Just keeping this null to avoid constraint errors since we accept raw text
                'jenis_proyek' => $request->deskripsi_kp, // Mapping Deskripsi to jenis_proyek
                'status_kp' => $status_kp,
                'pengerjaan_kp' => $request->pengerjaan_kp,
                'anggota_kelompok_ids' => empty($anggotaArray) ? null : $anggotaArray,
            ]);

            SupervisorInstansi::create([
                'pendaftaran_kp_id' => $pendaftaranKp->id,
                'nama_supervisor' => $request->nama_supervisor,
            ]);

            return redirect()->route('mahasiswa.pendaftaran-kp.create')->with('success', 'Pendaftaran KP berhasil diajukan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage());
        }
    }
}
