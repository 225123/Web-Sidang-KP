<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiLog;
use App\Models\PendaftaranKp;
use App\Models\SupervisorInstansi;
use App\Models\User;
use Illuminate\Http\Request;

class PendaftaranKpController extends Controller
{
    public function create()
    {
        $existingKp = PendaftaranKp::where('mahasiswa_id', auth()->id())
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();

        // Identify all students who are already part of an active or approved KP
        $activeKps = PendaftaranKp::whereIn('status_kp', ['pending', 'approved'])->get();
        $unavailableIds = [];
        foreach ($activeKps as $akp) {
            $unavailableIds[] = (string) $akp->mahasiswa_id;
            if (! empty($akp->anggota_kelompok_ids)) {
                $anggota = is_string($akp->anggota_kelompok_ids) ? json_decode($akp->anggota_kelompok_ids, true) : $akp->anggota_kelompok_ids;
                if (is_array($anggota)) {
                    foreach ($anggota as $aid) {
                        $unavailableIds[] = (string) $aid;
                    }
                }
            }
        }
        $unavailableIds = array_unique($unavailableIds);

        // Fetch all other users with role = mahasiswa, eager load mahasiswa relation to sort by NIM
        $allMahasiswa = User::with('mahasiswa')
            ->where('role', 'mahasiswa')
            ->where('id', '!=', auth()->id())
            ->get()
            ->map(function ($user) use ($unavailableIds) {
                $user->is_unavailable = in_array((string) $user->id, $unavailableIds);

                return $user;
            })
            ->sortBy(function ($user) {
                return $user->mahasiswa->nim ?? (string) $user->id;
            })
            ->values();

        // Fetch all active Dosen
        $allDosen = User::where('role', 'dosen')
            ->whereHas('dosen', function ($query) {
                $query->where('is_aktif', 1);
            })
            ->get(['id', 'name'])
            ->sortBy('name')
            ->values();

        // Check if current user is invited into any active group
        $invitation = PendaftaranKp::where(function ($query) {
            $query->whereJsonContains('anggota_kelompok_ids', (string) auth()->id())
                ->orWhereJsonContains('anggota_kelompok_ids', auth()->id());
        })
            ->whereIn('status_kp', ['pending', 'approved'])
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
            $anggotaIds = array_diff($anggotaIds, [auth()->id(), (string) auth()->id()]);
            // Fetch names for display
            $anggotaTerpilih = User::whereIn('id', $anggotaIds)->get();
        }

        return view('mahasiswa.Pendaftaran-KP', compact('existingKp', 'allMahasiswa', 'allDosen', 'invitation', 'anggotaTerpilih'));
    }

    public function dataKpSaya(Request $request)
    {
        $unrespondedInvitation = PendaftaranKp::where(function ($q) {
            $q->whereJsonContains('anggota_kelompok_ids', (string) auth()->id())
                ->orWhereJsonContains('anggota_kelompok_ids', auth()->id());
        })
            ->whereIn('status_kp', ['pending', 'approved'])
            ->latest()
            ->first();

        if ($unrespondedInvitation) {
            $hasSubmitted = PendaftaranKp::where('mahasiswa_id', auth()->id())
                ->whereIn('status_kp', ['pending', 'approved'])
                ->exists();
            if ($hasSubmitted) {
                $unrespondedInvitation = null;
            }
        }

        $query = PendaftaranKp::where('mahasiswa_id', auth()->id());

        // Search by judul_kp or instansi_nama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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

        return view('mahasiswa.Status-Pendaftaran', compact('riwayatKp', 'unrespondedInvitation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_kp' => 'required|string|max:255',
            'jenis_instansi' => 'required|in:Internal,External',
            'instansi_nama' => 'required_if:jenis_instansi,External|nullable|string|max:255',
            'dosen_pemberi_projek' => 'required_if:jenis_instansi,Internal|nullable|string',
            'nama_supervisor' => 'required|string|max:255',
            'email_supervisor' => 'nullable|email|max:255|required_without:supervisor_internal_id',
            'deskripsi_kp' => 'required|string',
            'pengerjaan_kp' => 'required|in:individu,kelompok',
            'anggota_kelompok_ids' => 'nullable|string',
            'supervisor_internal_id' => 'nullable|exists:users,id',
        ]);

        $existingKp = PendaftaranKp::where('mahasiswa_id', auth()->id())
            ->whereIn('status_kp', ['pending', 'approved'])
            ->first();

        if ($existingKp) {
            return redirect()->route('mahasiswa.pendaftaran-kp.create')->with('error', 'Anda sudah memiliki pendaftaran KP yang sedang diproses atau disetujui.');
        }

        try {
            $anggotaArray = [];
            if ($request->pengerjaan_kp === 'kelompok' && $request->filled('anggota_kelompok_ids')) {
                // Decode the JSON array from frontend
                $anggotaArray = json_decode($request->anggota_kelompok_ids, true);
                if (! is_array($anggotaArray)) {
                    $anggotaArray = [];
                }
            }

            // check if there's an invitation to copy the status (only from active groups)
            $invitation = PendaftaranKp::where(function ($query) {
                $query->whereJsonContains('anggota_kelompok_ids', (string) auth()->id())
                    ->orWhereJsonContains('anggota_kelompok_ids', auth()->id());
            })
                ->whereIn('status_kp', ['pending', 'approved'])
                ->latest()
                ->first();

            $status_kp = 'pending';
            if ($invitation && $request->pengerjaan_kp === 'kelompok') {
                $status_kp = $invitation->status_kp; // Sync status if invited
            }

            $draftKp = PendaftaranKp::where('mahasiswa_id', auth()->id())
                ->whereNull('status_kp')
                ->first();

            $dataKp = [
                'mahasiswa_id' => auth()->id(),
                'judul_kp' => $request->judul_kp,
                'jenis_instansi' => $request->jenis_instansi,
                'tipe_kp' => strtolower($request->jenis_instansi),
                'instansi_nama' => $request->jenis_instansi === 'External' ? $request->instansi_nama : 'Universitas Kristen Krida Wacana',
                'supervisor_internal_id' => $request->supervisor_internal_id, // Allow ID regardless of type
                'jenis_proyek' => $request->deskripsi_kp,
                'status_kp' => $status_kp,
                'pengerjaan_kp' => $request->pengerjaan_kp,
                'anggota_kelompok_ids' => empty($anggotaArray) ? null : $anggotaArray,
            ];

            if ($draftKp) {
                $draftKp->update($dataKp);
                $pendaftaranKp = $draftKp;
            } else {
                $pendaftaranKp = PendaftaranKp::create($dataKp);
            }

            SupervisorInstansi::create([
                'pendaftaran_kp_id' => $pendaftaranKp->id,
                'nama_supervisor' => $request->nama_supervisor,
                'email_supervisor' => $request->email_supervisor ?? null,
            ]);

            // Notifikasi ke Koordinator
            NotifikasiLog::create([
                'sender_id' => null, // Sistem
                'target_role' => 'koordinator',
                'judul' => 'Pendaftaran KP Baru',
                'pesan' => auth()->user()->name.' ('.(auth()->user()->mahasiswa->nim ?? '-').') telah melakukan pendaftaran KP.',
                'target_url' => route('koordinator.pendaftaran-kp.detail', $pendaftaranKp->id),
                'is_read' => false,
            ]);

            return redirect()->route('mahasiswa.pendaftaran-kp.create')->with('success', 'Pendaftaran KP berhasil diajukan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan data: '.$e->getMessage());
        }
    }
}
