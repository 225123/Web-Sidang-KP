<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\NotifikasiLog;
use App\Models\PendaftaranKp;
use App\Models\PendaftaranSidang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendaftaranSidangController extends Controller
{
    public function index()
    {
        $mahasiswaId = Auth::user()->id;

        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        // Cari data pendaftaran KP aktif
        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->where(function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $mahasiswaId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $mahasiswaId);
            })
            ->where('status_kp', 'approved');
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }
        
        $pendaftaran = $query->latest()->first();

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

        return view('mahasiswa.pendaftaran-sidang', compact('pengajuan', 'isVerifiedByDosen', 'pendaftaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_laporan' => 'required|mimes:pdf|max:5120',
            'file_log_bimbingan' => 'required|mimes:pdf|max:5120',
            'file_berkas_lainnya' => 'nullable|mimes:pdf|max:5120',
            'link_drive' => 'nullable|url',
            'link_github' => 'nullable|url',
            'link_deploy' => 'nullable|url',
        ]);

        $mahasiswaId = Auth::user()->id;
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->where(function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $mahasiswaId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $mahasiswaId);
            })->where('status_kp', 'approved');
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }
        
        $pendaftaran = $query->latest()->first();

        if (! $pendaftaran) {
            abort(403, 'Akses ditolak.');
        }

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
            $dataToUpdate['file_laporan'] = $request->file('file_laporan')->store('sidang_berkas', upload_disk());
        }
        if ($request->hasFile('file_log_bimbingan')) {
            $dataToUpdate['file_log_bimbingan'] = $request->file('file_log_bimbingan')->store('sidang_berkas', upload_disk());
        }
        if ($request->hasFile('file_berkas_lainnya')) {
            $dataToUpdate['file_berkas_lainnya'] = $request->file('file_berkas_lainnya')->store('sidang_berkas', upload_disk());
        }

        $pengajuan->update($dataToUpdate);

        // Notifikasi ke Koordinator
        NotifikasiLog::create([
            'sender_id' => null, // Sistem
            'target_role' => 'koordinator',
            'judul' => 'Pengajuan Berkas Sidang',
            'pesan' => auth()->user()->name.' ('.(auth()->user()->mahasiswa->nim ?? '-').') telah mengajukan berkas pendaftaran sidang.',
            'target_url' => route('koordinator.verifikasi-berkas'),
            'is_read' => false,
        ]);

        return back()->with('success', 'Berkas pendaftaran sidang berhasil diajukan ke Koordinator KP.');
    }

    public function downloadTemplateSupervisor()
    {
        $mahasiswaId = Auth::user()->id;
        $mhs = Mahasiswa::with('user')->where('user_id', $mahasiswaId)->first();
        $periodeId = session('selected_periode_id') ?? \App\Models\TahunAjaran::aktif()->id ?? null;

        $query = PendaftaranKp::withoutGlobalScope('periode')
            ->where(function ($query) use ($mahasiswaId) {
                $query->where('mahasiswa_id', $mahasiswaId)
                      ->orWhereJsonContains('anggota_kelompok_ids', $mahasiswaId)
                      ->orWhereJsonContains('anggota_kelompok_ids', (string) $mahasiswaId);
            })->where('status_kp', 'approved');
            
        if ($periodeId) {
            $query->where('tahun_ajaran_id', $periodeId);
        }
        
        $kp = $query->latest()->first();

        $data = [
            'nama_mahasiswa' => $mhs->user->name ?? 'Mahasiswa',
            'nim' => $mhs->nim ?? '-',
            'nama_projek' => $kp->judul_kp ?? '-',
            'nama_instansi' => $kp->instansi_nama ?? '-',
        ];

        $pdf = Pdf::loadView('mahasiswa.template_penilaian_supervisor', compact('data'));

        return $pdf->download('Template_Surat_Penilaian_Supervisor_'.$data['nim'].'.pdf');
    }
}
