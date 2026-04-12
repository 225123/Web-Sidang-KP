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

        return view('mahasiswa.Pendaftaran-KP', compact('existingKp'));
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
        ]);

        $existingKp = PendaftaranKp::where('mahasiswa_id', auth()->id())
            ->whereIn('status_kp', ['pending', 'approved'])
            ->first();
            
        if ($existingKp) {
            return redirect()->route('mahasiswa.pendaftaran-kp.create')->with('error', 'Anda sudah memiliki pendaftaran KP yang sedang diproses atau disetujui.');
        }

        try {
            $pendaftaranKp = PendaftaranKp::create([
                'mahasiswa_id' => auth()->id(),
                'judul_kp' => $request->judul_kp,
                'jenis_instansi' => $request->jenis_instansi,
                'tipe_kp' => strtolower($request->jenis_instansi),
                'instansi_nama' => $request->jenis_instansi === 'External' ? $request->instansi_nama : 'Universitas Kristen Krida Wacana',
                'supervisor_internal_id' => null, // Just keeping this null to avoid constraint errors since we accept raw text
                'jenis_proyek' => $request->deskripsi_kp, // Mapping Deskripsi to jenis_proyek
                'status_kp' => 'pending',
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
