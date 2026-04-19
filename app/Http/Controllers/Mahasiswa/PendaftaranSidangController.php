<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranSidang;
use App\Models\PendaftaranKp;

class PendaftaranSidangController extends Controller
{
    public function index()
    {
        $mahasiswaId = Auth::user()->id;

        // Cari data pendaftaran KP aktif
        $pendaftaran = PendaftaranKp::where('mahasiswa_id', $mahasiswaId)
            ->where('status_kp', 'approved')
            ->latest()
            ->first();

        // Cari pengajuan sidang / persetujuan
        $pengajuan = null;
        $isVerifiedByDosen = false;

        if ($pendaftaran) {
            $pengajuan = PendaftaranSidang::where('pendaftaran_kp_id', $pendaftaran->id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->first();

            // Pendaftaran Sidang HANYA BISA DIAKSES JIKA status_verifikasi (Dosen) == 'verified'
            if ($pengajuan && $pengajuan->status_verifikasi == 'verified') {
                $isVerifiedByDosen = true;
            }
        }

        return view('mahasiswa.pendaftaran-sidang', compact('pengajuan', 'isVerifiedByDosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_laporan' => 'required|mimes:pdf|max:5120',
            'file_log_bimbingan' => 'required|mimes:pdf|max:5120',
            'file_nilai_supervisor' => 'nullable|mimes:pdf|max:5120',
            'file_berkas_lainnya' => 'nullable|mimes:pdf|max:5120',
            'link_drive' => 'nullable|url',
            'link_github' => 'nullable|url',
            'link_deploy' => 'nullable|url',
        ]);

        $mahasiswaId = Auth::user()->id;
        $pendaftaran = PendaftaranKp::where('mahasiswa_id', $mahasiswaId)->where('status_kp', 'approved')->latest()->first();

        if (!$pendaftaran) abort(403, 'Akses ditolak.');

        $pengajuan = PendaftaranSidang::where('pendaftaran_kp_id', $pendaftaran->id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->firstOrFail();

        // Hindari re-submit jika masih pending atau sudah verified oleh koordinator
        if (in_array($pengajuan->status_koordinator, ['pending', 'verified'])) {
            return back()->with('error', 'Berkas sudah diajukan, menunggu verifikasi Koordinator.');
        }

        // Simpan File
        $dataToUpdate = [
            'status_koordinator' => 'pending',
            'link_drive' => $request->link_drive,
            'link_github' => $request->link_github,
            'link_deploy' => $request->link_deploy,
        ];

        if ($request->hasFile('file_laporan')) {
            $dataToUpdate['file_laporan'] = $request->file('file_laporan')->store('sidang_berkas', 'public');
        }
        if ($request->hasFile('file_log_bimbingan')) {
            $dataToUpdate['file_log_bimbingan'] = $request->file('file_log_bimbingan')->store('sidang_berkas', 'public');
        }
        if ($request->hasFile('file_nilai_supervisor')) {
            $dataToUpdate['file_nilai_supervisor'] = $request->file('file_nilai_supervisor')->store('sidang_berkas', 'public');
        }
        if ($request->hasFile('file_berkas_lainnya')) {
            $dataToUpdate['file_berkas_lainnya'] = $request->file('file_berkas_lainnya')->store('sidang_berkas', 'public');
        }

        $pengajuan->update($dataToUpdate);

        return back()->with('success', 'Berkas pendaftaran sidang berhasil diajukan ke Koordinator KP.');
    }

    public function downloadTemplateSupervisor()
    {
        $mahasiswaId = Auth::user()->id;
        $mhs = \App\Models\Mahasiswa::with('user')->where('user_id', $mahasiswaId)->first();
        $kp = PendaftaranKp::where('mahasiswa_id', $mahasiswaId)->where('status_kp', 'approved')->latest()->first();

        $data = [
            'nama_mahasiswa' => $mhs->user->name ?? 'Mahasiswa',
            'nim' => $mhs->nim ?? '-',
            'nama_projek' => $kp->judul_kp ?? '-',
            'nama_instansi' => $kp->instansi_nama ?? '-'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mahasiswa.template_penilaian_supervisor', compact('data'));
        return $pdf->download('Template_Surat_Penilaian_Supervisor_'.$data['nim'].'.pdf');
    }
}
