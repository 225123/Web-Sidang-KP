<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function index()
    {
        $users = User::with(['mahasiswa', 'dosen'])
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        $logs = NotifikasiLog::with(['receiver'])
            ->where('sender_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('koordinator.pengumuman', compact('users', 'logs'));
    }

    public function show($id)
    {
        $log = NotifikasiLog::with(['receiver.mahasiswa', 'receiver.dosen', 'sender'])
            ->where('id', $id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();

        return view('koordinator.pengumuman-detail', compact('log'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target' => 'required', // Bisa 'semua', 'mahasiswa', 'dosen', atau user_id
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        $target = $request->target;
        $senderId = Auth::id();
        $filePath = null;

        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('announcements', 'public');
        }

        if (in_array($target, ['semua', 'mahasiswa', 'dosen'])) {
            // Jika target adalah role, simpan satu record dengan target_role
            NotifikasiLog::create([
                'sender_id' => $senderId,
                'target_role' => $target,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'file_path' => $filePath,
            ]);
        } else {
            // Jika target adalah user tertentu (user_id)
            NotifikasiLog::create([
                'sender_id' => $senderId,
                'receiver_id' => $target,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
                'file_path' => $filePath,
            ]);
        }

        return redirect()->back()->with('success', 'Pengumuman berhasil dikirim!');
    }

    public function destroy($id)
    {
        $log = NotifikasiLog::where('id', $id)->where('sender_id', Auth::id())->firstOrFail();
        $log->delete();

        return redirect()->back()->with('success', 'Riwayat pengumuman berhasil dihapus.');
    }
}
