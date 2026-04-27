<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = NotifikasiLog::with('sender')
            ->where(function ($q) use ($user) {
                $q->where('receiver_id', $user->id)
                    ->orWhere('target_role', 'mahasiswa')
                    ->orWhere('target_role', 'semua');
            });

        // Search Filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('sender', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%$search%");
                })
                    ->orWhere('judul', 'like', "%$search%")
                    ->orWhere('pesan', 'like', "%$search%");
            });
        }

        // Sort Filter
        $sort = $request->get('sort', 'desc');
        $query->orderBy('created_at', $sort);

        $notifikasis = $query->paginate(50);

        return view('mahasiswa.notifikasi', [
            'active' => 'notifikasi',
            'notifikasis' => $notifikasis,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $notifikasi = NotifikasiLog::with(['sender', 'receiver'])->findOrFail($id);

        // Mark as read if it belongs to current user or is a global role notification
        if ($notifikasi->receiver_id == auth()->id() || $notifikasi->target_role == 'mahasiswa' || $notifikasi->target_role == 'semua') {
            $notifikasi->update(['is_read' => true]);
        }

        return view('mahasiswa.notifikasi-detail', compact('notifikasi'));
    }
}
