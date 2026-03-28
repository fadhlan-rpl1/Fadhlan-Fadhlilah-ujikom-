<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\ActivityLog; // Pastikan model Log diimport
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();
        return view('admin.tarif.index', compact('tarifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_kendaraan' => 'required|string|max:50',
            'tarif_per_jam'   => 'required|numeric',
        ]);

        Tarif::create([
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'tarif_per_jam'   => $request->tarif_per_jam,
        ]);

        // Catat ke Log Aktivitas
        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Tambah Tarif',
        ]);

        return back()->with('success', 'Tarif berhasil ditambahkan!');
    }

    // --- FUNGSI UPDATE (Untuk Fitur Edit) ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_kendaraan' => 'required|string|max:50',
            'tarif_per_jam'   => 'required|numeric',
        ]);

        $tarif = Tarif::findOrFail($id);
        
        $tarif->update([
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'tarif_per_jam'   => $request->tarif_per_jam,
        ]);

        // Catat ke Log Aktivitas
        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Edit Tarif',
        ]);

        return back()->with('success', 'Tarif berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tarif = Tarif::findOrFail($id);
        $jenis = $tarif->jenis_kendaraan;

        // Catat ke Log Aktivitas sebelum dihapus
        ActivityLog::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Hapus Tarif',
        ]);

        $tarif->delete();

        return back()->with('success', 'Tarif berhasil dihapus!');
    }
} 