<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\PendaftaranKp;
use App\Models\User;
use Illuminate\Http\Request;

class PeriodeKpController extends Controller
{
    public function index()
    {
        $periodes = TahunAjaran::orderByRaw("CAST(SUBSTR(tahun, 1, INSTR(tahun, '/') - 1) AS INTEGER) DESC")
            ->orderByRaw("CASE semester WHEN 'Genap' THEN 1 ELSE 0 END DESC")
            ->get();

        $last = $periodes->first();
        $nextPeriod = $this->generateNext($last);

        // Count pendaftaran per period
        $stats = [];
        foreach ($periodes as $periode) {
            $stats[$periode->id] = PendaftaranKp::where('tahun_ajaran_id', $periode->id)->count();
        }

        // Active period counts
        $aktif = $periodes->firstWhere('is_active', true);
        $aktifStats = ['mahasiswa' => 0, 'dosen' => 0, 'total' => 0];

        if ($aktif) {
            $aktifStats['mahasiswa'] = PendaftaranKp::where('tahun_ajaran_id', $aktif->id)
                ->distinct('mahasiswa_id')->count('mahasiswa_id');

            $aktifStats['dosen'] = PendaftaranKp::where('tahun_ajaran_id', $aktif->id)
                ->whereNotNull('pembimbing_id')
                ->distinct('pembimbing_id')->count('pembimbing_id');

            $aktifStats['total'] = User::count();
        }

        return view('koordinator.periode-kp', compact('periodes', 'nextPeriod', 'stats', 'aktif', 'aktifStats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required|in:Ganjil,Genap',
            'tahun'    => 'required|regex:/^\d{4}\/\d{4}$/',
        ]);

        $label = $request->semester . ' ' . $request->tahun;

        if (TahunAjaran::where('label_tahun_ajaran', $label)->exists()) {
            return back()->with('error', "Periode \"$label\" sudah ada.");
        }

        // Auto-close current active period
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);

        TahunAjaran::create([
            'semester'           => $request->semester,
            'tahun'              => $request->tahun,
            'label_tahun_ajaran' => $label,
            'is_active'          => true,
        ]);

        return back()->with('success', "Periode KP \"$label\" berhasil dibuka dan kini menjadi periode aktif.");
    }

    public function setActive(Request $request, $id)
    {
        $periode = TahunAjaran::findOrFail($id);
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $periode->update(['is_active' => true]);

        return back()->with('success', "Periode \"{$periode->label_tahun_ajaran}\" sekarang menjadi periode aktif.");
    }

    public function destroy($id)
    {
        $periode = TahunAjaran::findOrFail($id);

        if ($periode->is_active) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sedang aktif.');
        }

        if (PendaftaranKp::where('tahun_ajaran_id', $id)->exists()) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sudah memiliki data pendaftaran.');
        }

        $label = $periode->label_tahun_ajaran;
        $periode->delete();

        return back()->with('success', "Periode \"$label\" berhasil dihapus.");
    }

    /**
     * Ganjil X/Y  → Genap X/Y  (same academic year)
     * Genap X/Y   → Ganjil Y/Y+1 (next academic year)
     */
    private function generateNext(?TahunAjaran $last): array
    {
        if (!$last) {
            $semester  = 'Ganjil';
            $year      = (int) now()->format('Y');
            $tahun     = $year . '/' . ($year + 1);
        } elseif ($last->semester === 'Ganjil') {
            $semester = 'Genap';
            $tahun    = $last->tahun; // e.g. 2025/2026
        } else {
            // Genap → advance year
            $semester = 'Ganjil';
            $parts    = explode('/', $last->tahun);
            $endYear  = (int)($parts[1] ?? $parts[0]);
            $tahun    = $endYear . '/' . ($endYear + 1); // e.g. 2026/2027
        }

        return ['semester' => $semester, 'tahun' => $tahun, 'label' => "$semester $tahun"];
    }
}
