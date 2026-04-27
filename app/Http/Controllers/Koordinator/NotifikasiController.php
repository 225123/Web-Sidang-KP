<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiLog;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $notifikasis = NotifikasiLog::with(['sender'])
            ->where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->orWhere('target_role', 'koordinator');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('koordinator.notifikasi', compact('notifikasis'));
    }

    public function show($id)
    {
        $userId = Auth::id();

        // Cari notifikasi yang ditujukan untuk koordinator ini atau perannya
        $notifikasi = NotifikasiLog::with(['sender'])
            ->where('id', $id)
            ->where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->orWhere('target_role', 'koordinator');
            })
            ->firstOrFail();

        // Tandai sudah dibaca jika belum
        if (! $notifikasi->is_read) {
            $notifikasi->update(['is_read' => true]);
        }

        return view('koordinator.notifikasi-detail', compact('notifikasi'));
    }
}
