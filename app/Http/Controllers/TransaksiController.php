<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\AreaParkir;
use App\Models\ActivityLog;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index() 
    {
        $transaksis = Transaksi::with(['area', 'tarif'])->latest()->paginate(5);
        
        $areas = AreaParkir::all(); 
        $tarifs = Tarif::all(); 
        
        return view('admin.transaksi.index', compact('transaksis', 'areas', 'tarifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'area_parkir_id' => 'required'
        ]);

        $area = AreaParkir::findOrFail($request->area_parkir_id);

        if ($area->kapasitas <= 0) {
            return back()->with('error', 'Maaf, area parkir ini sudah penuh!');
        }

        // Catat kendaraan ke tabel kendaraan jika belum ada
        Kendaraan::firstOrCreate([
            'plat_nomor' => strtoupper($request->plat_nomor)
        ], [
            'jenis_kendaraan' => Tarif::find($request->tarif_id ?? 1)->jenis_kendaraan ?? 'Lainnya',
            'user_id' => Auth::id()
        ]);

        $transaksi = Transaksi::create([
            'plat_nomor'     => strtoupper($request->plat_nomor),
            'area_parkir_id' => $request->area_parkir_id,
            'tarif_id'       => $request->tarif_id ?? 1, 
            'user_id'        => Auth::id(), 
            'waktu_masuk'    => now(),
            'status'         => 'masuk'
        ]);

        $area->decrement('kapasitas');

        ActivityLog::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Kendaraan Masuk',
        ]);

        return back()->with('success', 'Data kendaraan berhasil masuk!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'area_parkir_id' => 'required'
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $areaLama = AreaParkir::find($transaksi->area_parkir_id);
        $areaBaru = AreaParkir::findOrFail($request->area_parkir_id);

        if ($transaksi->area_parkir_id != $request->area_parkir_id) {
            if ($areaLama) $areaLama->increment('kapasitas');
            if ($areaBaru) $areaBaru->decrement('kapasitas');
        }

        $transaksi->update([
            'plat_nomor' => strtoupper($request->plat_nomor),
            'area_parkir_id' => $request->area_parkir_id,
            'tarif_id' => $request->tarif_id ?? $transaksi->tarif_id,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Edit Transaksi',
        ]);

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function bayar($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        
        $waktu_masuk = Carbon::parse($transaksi->waktu_masuk);
        $waktu_keluar = now();
        $selisihJam = $waktu_masuk->diffInHours($waktu_keluar);
        $durasi = max(1, ceil($selisihJam)); 
        
        $hargaPerJam = $transaksi->tarif->tarif_per_jam ?? 3000;
        $totalBiaya = $durasi * $hargaPerJam;

        $transaksi->update([
            'status' => 'keluar',
            'waktu_keluar' => $waktu_keluar,
            'durasi_jam' => $durasi,
            'biaya_total' => $totalBiaya
        ]);

        if ($transaksi->area) {
            $transaksi->area->increment('kapasitas');
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Kendaraan Keluar',
        ]);

        return redirect('/petugas/transaksi/struk/' . $transaksi->id);
    }

    public function destroy($id) 
    {
        $transaksi = Transaksi::findOrFail($id);

        if (strtolower($transaksi->status) == 'masuk') {
            $area = AreaParkir::find($transaksi->area_parkir_id);
            if ($area) {
                $area->increment('kapasitas');
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Transaksi',
        ]);

        $transaksi->delete();

        return back()->with('success', 'Data berhasil dihapus!');
    }

    public function struk($id)
    {
        $transaksi = Transaksi::with(['tarif', 'area'])->findOrFail($id);
        
        $masuk = Carbon::parse($transaksi->waktu_masuk);
        $keluar = Carbon::parse($transaksi->waktu_keluar);
        $durasi = $transaksi->durasi_jam; 
        $hargaPerJam = $transaksi->tarif->tarif_per_jam ?? 3000;
        $totalBiaya = $transaksi->biaya_total;
        $platNomor = $transaksi->plat_nomor;

        return view('petugas.struk', compact('transaksi', 'masuk', 'keluar', 'durasi', 'hargaPerJam', 'totalBiaya', 'platNomor'));
    }
}