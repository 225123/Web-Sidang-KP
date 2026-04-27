<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\TimelineKegiatan;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function index()
    {
        $timelineMahasiswa = TimelineKegiatan::where('kategori', 'mahasiswa')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        $timelineDosen = TimelineKegiatan::where('kategori', 'dosen')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->get();

        return view('koordinator.timeline', [
            'active' => 'timeline',
            'timelineMahasiswa' => $timelineMahasiswa,
            'timelineDosen' => $timelineDosen,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'kategori' => 'required|in:mahasiswa,dosen',
            'keterangan' => 'nullable|string',
        ]);

        TimelineKegiatan::create($request->all());

        return redirect()->back()->with('success', 'Timeline berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'kategori' => 'required|in:mahasiswa,dosen',
            'keterangan' => 'nullable|string',
        ]);

        $timeline = TimelineKegiatan::findOrFail($id);
        $timeline->update($request->all());

        return redirect()->back()->with('success', 'Timeline berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $timeline = TimelineKegiatan::findOrFail($id);
        $timeline->delete();

        return redirect()->back()->with('success', 'Timeline berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (! empty($ids)) {
            TimelineKegiatan::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'Timeline terpilih berhasil dihapus.']);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada timeline yang dipilih.']);
    }
}
