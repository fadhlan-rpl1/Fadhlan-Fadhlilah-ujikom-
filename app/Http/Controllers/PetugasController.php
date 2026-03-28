<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\AreaParkir;
use App\Models\Transaksi;
use App\Models\ActivityLog; // <-- SUDAH DIPERBAIKI MENJADI ActivityLog
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class PetugasController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::all();
        $areas = AreaParkir::all();
        
        $transaksis = Transaksi::with(['kendaraan', 'tarif', 'area'])
                        ->where('status', 'masuk')
                        ->latest()
                        ->get();

        return view('petugas.dashboard', compact('tarifs', 'areas', 'transaksis'));
    }

    // --- FITUR SIMPAN KENDARAAN MASUK ---
    public function store(Request $request)
    {
        $transaksi = Transaksi::create([
            'plat_nomor' => strtoupper($request->plat_nomor),
            'tarif_id' => $request->tarif_id,
            'area_parkir_id' => $request->area_parkir_id,
            'status' => 'masuk'
        ]);

        if ($transaksi->area) {
            $transaksi->area->decrement('kapasitas');
        }

        // 📝 CATAT LOG
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Kendaraan Masuk',
            'description' => 'Petugas mencatat kendaraan masuk dengan plat ' . $transaksi->plat_nomor
        ]);

        return back()->with('success', '🚗 Kendaran berhasil dicatat masuk!');
    }

    // --- FITUR EDIT DATA ---
    public function updateData(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $platLama = $transaksi->plat_nomor ?? ($transaksi->kendaraan->plat_nomor ?? '-');
        
        $transaksi->update([
            'tarif_id' => $request->tarif_id,
            'area_parkir_id' => $request->area_parkir_id,
        ]);

        if ($transaksi->kendaraan) {
            $transaksi->kendaraan->update(['plat_nomor' => strtoupper($request->plat_nomor)]);
        } else {
            $transaksi->update(['plat_nomor' => strtoupper($request->plat_nomor)]);
        }

        // 📝 CATAT LOG
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Edit Data',
            'description' => 'Petugas mengubah data kendaraan dari plat ' . $platLama . ' menjadi ' . strtoupper($request->plat_nomor)
        ]);

        return back()->with('success', '✏️ Data kendaraan berhasil diperbarui!');
    }

    // --- FITUR BAYAR / CHECKOUT ---
    public function bayar($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        
        $transaksi->update(['status' => 'keluar']);

        if ($transaksi->area) {
            $transaksi->area->increment('kapasitas');
        }

        $platNomor = $transaksi->kendaraan->plat_nomor ?? $transaksi->plat_nomor ?? '-';

        // 📝 CATAT LOG
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Kendaraan Keluar',
            'description' => 'Petugas melakukan checkout/pembayaran pada kendaraan plat ' . $platNomor
        ]);

        return redirect('/petugas/transaksi/struk/' . $transaksi->id);
    }

    // --- FITUR HAPUS / BATAL ---
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $platNomor = $transaksi->kendaraan->plat_nomor ?? $transaksi->plat_nomor ?? '-';
        
        if ($transaksi->status == 'masuk' && $transaksi->area) {
            $transaksi->area->increment('kapasitas');
        }

        // 📝 CATAT LOG
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Hapus Batal',
            'description' => 'Petugas membatalkan/menghapus kendaraan masuk dengan plat ' . $platNomor
        ]);

        $transaksi->delete();
        
        return back()->with('success', '🗑️ Data kendaraan berhasil dibatalkan!');
    }

    // --- HALAMAN CETAK STRUK ---
    public function struk($id)
    {
        $transaksi = Transaksi::with(['kendaraan', 'tarif', 'area'])->findOrFail($id);
        
        $masuk = Carbon::parse($transaksi->created_at);
        $keluar = Carbon::parse($transaksi->updated_at);
        $selisihJam = $masuk->diffInHours($keluar);
        $durasi = max(1, ceil($selisihJam)); 
        
        $hargaPerJam = $transaksi->tarif->tarif_per_jam ?? 3000;
        $totalBiaya = $durasi * $hargaPerJam;
        $platNomor = $transaksi->kendaraan->plat_nomor ?? $transaksi->plat_nomor ?? '-';

        return view('petugas.struk', compact('transaksi', 'masuk', 'keluar', 'durasi', 'hargaPerJam', 'totalBiaya', 'platNomor'));
    }
}