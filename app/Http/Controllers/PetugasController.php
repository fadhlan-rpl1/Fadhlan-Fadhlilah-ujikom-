<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\AreaParkir;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();
        $areas = AreaParkir::all();
        
        $transaksis = Transaksi::with(['tarif', 'area'])
                        ->where('status', 'masuk')
                        ->latest()
                        ->get();

        return view('petugas.dashboard', compact('tarifs', 'areas', 'transaksis'));
    }
}