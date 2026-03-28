<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\AreaParkir;
use App\Models\ActivityLog;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index() 
    {
        // 1. Menggunakan paginate(5) agar tombol halaman di view berfungsi
        $transaksis = Transaksi::with(['kendaraan', 'area'])->latest()->paginate(5);
        
        $areas = AreaParkir::all(); 
        $tarifs = Tarif::all(); 
        
        // 👇 JALUR VIEW SUDAH DIPERBAIKI MENJADI admin.transaksi.index 👇
        return view('admin.transaksi.index', compact('transaksis', 'areas', 'tarifs'));
    }

    // --- FUNGSI SIMPAN (KENDARAAN MASUK) ---
    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
            // 'tarif_id' => 'required', // Dimatikan sementara jika form modal belum ada dropdown tarif
            'area_parkir_id' => 'required'
        ]);

        $area = AreaParkir::findOrFail($request->area_parkir_id);

        if ($area->kapasitas <= 0) {
            return back()->with('error', 'Maaf, area parkir ini sudah penuh!');
        }

        $kendaraan = Kendaraan::firstOrCreate([
            'plat_nomor' => strtoupper($request->plat_nomor)
        ]);

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-' . strtoupper(uniqid()),
            'kendaraan_id'   => $kendaraan->id,
            'area_parkir_id' => $request->area_parkir_id,
            'tarif_id'       => $request->tarif_id ?? 1, // Fallback ke tarif ID 1
            'user_id'        => Auth::user()->id_user ?? Auth::id(), 
            'waktu_masuk'    => now(),
            'status'         => 'masuk'
        ]);

        $area->decrement('kapasitas');

        // 📝 CATAT LOG: Simpan Data
        ActivityLog::create([
            'user_id' => Auth::user()->id_user ?? Auth::id(),
            'activity' => 'Kendaraan Masuk',
            'description' => 'Admin mendaftarkan kendaraan masuk dengan plat ' . strtoupper($request->plat_nomor)
        ]);

        return back()->with('success', 'Data kendaraan berhasil masuk!');
    }

    // --- FUNGSI UPDATE UNTUK EDIT ---
    public function update(Request $request, $id)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'area_parkir_id' => 'required'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $areaLama = AreaParkir::find($transaksi->area_parkir_id);
        $areaBaru = AreaParkir::findOrFail($request->area_parkir_id);

        // Update plat nomor di tabel kendaraan
        if ($transaksi->kendaraan) {
            $transaksi->kendaraan->update([
                'plat_nomor' => strtoupper($request->plat_nomor)
            ]);
        }

        // Logika jika Area Parkir diubah
        if ($transaksi->area_parkir_id != $request->area_parkir_id) {
            if ($areaLama) $areaLama->increment('kapasitas');
            if ($areaBaru) $areaBaru->decrement('kapasitas');
        }

        $transaksi->update([
            'area_parkir_id' => $request->area_parkir_id
        ]);

        // 📝 CATAT LOG: Edit Data
        ActivityLog::create([
            'user_id' => Auth::user()->id_user ?? Auth::id(),
            'activity' => 'Edit Transaksi',
            'description' => 'Admin mengubah data parkir kendaraan plat ' . strtoupper($request->plat_nomor)
        ]);

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    // --- FUNGSI HAPUS ---
    public function destroy($id) 
    {
        $transaksi = Transaksi::with('kendaraan')->findOrFail($id);
        $plat = $transaksi->kendaraan->plat_nomor ?? $transaksi->plat_nomor ?? 'Tidak Diketahui';

        // KEMBALIKAN KAPASITAS AREA SEBELUM DIHAPUS (Hanya jika statusnya masih masuk)
        if (strtolower($transaksi->status) == 'masuk') {
            $area = AreaParkir::find($transaksi->area_parkir_id);
            if ($area) {
                $area->increment('kapasitas');
            }
        }

        // 📝 CATAT LOG: Hapus Data
        ActivityLog::create([
            'user_id' => Auth::user()->id_user ?? Auth::id(),
            'activity' => 'Hapus Transaksi',
            'description' => 'Admin menghapus data kendaraan plat: ' . $plat
        ]);

        $transaksi->delete();

        return back()->with('success', 'Data berhasil dihapus dan kapasitas dikembalikan!');
    }
}