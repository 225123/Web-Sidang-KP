<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PendaftaranKp;
use App\Models\User;

class PendaftaranKpController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranKp::with(['supervisorInstansi', 'user.mahasiswa'])->latest();

        if ($request->has('jenis_kp') && $request->jenis_kp != 'All') {
            $query->where('jenis_instansi', $request->jenis_kp);
        }

        if ($request->has('status_approval') && $request->status_approval != 'All') {
            if ($request->status_approval == 'Disetujui') {
                $query->where('status_kp', 'approved');
            } elseif ($request->status_approval == 'Ditolak') {
                $query->where('status_kp', 'rejected');
            } elseif ($request->status_approval == 'Belum Diperiksa') {
                $query->where('status_kp', 'pending');
            }
        }

        if ($request->has('search') && $request->search != '') {
            // we skip complex search on Mahasiswa Name for now to keep it simple, 
            // or join users table.
            $search = $request->search;
            $query->where('judul_kp', 'like', '%' . $search . '%');
        }

        $pendaftarans = $query->paginate(12);

        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $dapatProjek = PendaftaranKp::where('status_kp', 'approved')->select('mahasiswa_id')->distinct()->count();

        $stats = [
            'disetujui' => PendaftaranKp::where('status_kp', 'approved')->count(),
            'belum_diperiksa' => PendaftaranKp::where('status_kp', 'pending')->count(),
            'ditolak' => PendaftaranKp::where('status_kp', 'rejected')->count(),
            'total' => PendaftaranKp::count(),
            'total_mahasiswa' => $totalMahasiswa,
            'dapat_projek' => $dapatProjek,
            'belum_dapat_projek' => max(0, $totalMahasiswa - $dapatProjek),
        ];

        return view('koordinator.Pendaftaran-KP', compact('pendaftarans', 'stats'));
    }

    public function show($slug)
    {
        $parts = explode('-', $slug);
        $nim = end($parts);
        
        $kp = PendaftaranKp::with(['supervisorInstansi', 'user.mahasiswa'])
            ->whereHas('user.mahasiswa', function($q) use ($nim) {
                $q->where('nim', $nim);
            })->latest()->firstOrFail();

        return view('koordinator.pendaftaran-kp-detail', compact('kp'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string'
        ]);

        $kp = PendaftaranKp::findOrFail($id);
        $kp->status_kp = $request->status;
        
        // Memastikan catatan akan tertimpa menjadi null (kosong) jika form dikirim tanpa text
        $kp->catatan = empty(trim($request->catatan)) ? null : trim($request->catatan);
        
        $kp->save();

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }
}
