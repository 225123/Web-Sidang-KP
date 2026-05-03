<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeriodeSessionController extends Controller
{
    public function setPeriode(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:tahun_ajaran,id'
        ]);

        session(['selected_periode_id' => $request->periode_id]);

        return back();
    }
}
