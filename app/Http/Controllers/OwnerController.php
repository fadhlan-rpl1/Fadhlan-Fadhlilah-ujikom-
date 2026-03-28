<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ActivityLog; // <-- SUDAH DIPERBAIKI MENJADI ActivityLog
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Auth; 

class OwnerController extends Controller
{
    public function index()
    {
        $allTransactions = Transaksi::with(['tarif'])
                        ->whereIn('status', ['keluar', 'Keluar', 'Selesai', 'selesai', 'sukses', 'Sukses'])
                        ->get();

        $pendapatanHariIni = 0;
        $pendapatanBulanIni = 0;
        $totalKendaraan = $allTransactions->count();

        $tahunIni = Carbon::now()->year; 
        $bulanSekarang = Carbon::now()->month;
        $pendapatanBulanan = array_fill(1, 12, 0); 

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        foreach ($allTransactions as $tr) {
            $masuk = Carbon::parse($tr->created_at);
            $keluar = Carbon::parse($tr->updated_at);
            $durasi = max(1, ceil($masuk->diffInHours($keluar)));
            $hargaPerJam = $tr->tarif->tarif_per_jam ?? 3000; 
            $totalBiaya = $durasi * $hargaPerJam;

            if ($keluar->isToday()) {
                $pendapatanHariIni += $totalBiaya;
            }
            if ($keluar->isCurrentMonth()) {
                $pendapatanBulanIni += $totalBiaya;
            }
            if ($keluar->year == $tahunIni) {
                $bulanKeluar = $keluar->month; 
                $pendapatanBulanan[$bulanKeluar] += $totalBiaya;
            }
        }

        $transaksis = Transaksi::with(['kendaraan', 'tarif', 'area'])
                        ->whereIn('status', ['keluar', 'Keluar', 'Selesai', 'selesai', 'sukses', 'Sukses'])
                        ->latest('updated_at')
                        ->paginate(5); 

        return view('owner.dashboard', compact(
            'transaksis', 'pendapatanHariIni', 'pendapatanBulanIni', 
            'totalKendaraan', 'pendapatanBulanan', 'namaBulan', 'tahunIni', 'bulanSekarang'
        ));
    }

    public function downloadPDF()
    {
        $transaksis = Transaksi::with(['tarif', 'area', 'kendaraan'])
                        ->whereIn('status', ['keluar', 'Keluar', 'Selesai', 'selesai', 'sukses', 'Sukses'])
                        ->get();

        $totalPendapatan = 0;
        foreach ($transaksis as $tr) {
            $masuk = Carbon::parse($tr->created_at);
            $keluar = Carbon::parse($tr->updated_at);
            $durasi = max(1, ceil($masuk->diffInHours($keluar)));
            $hargaPerJam = $tr->tarif->tarif_per_jam ?? 3000;
            $totalPendapatan += ($durasi * $hargaPerJam);
        }

        // 📝 CATAT LOG
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Download Laporan',
            'description' => 'Owner mengunduh laporan PDF pendapatan lannPark.'
        ]);

        $pdf = Pdf::loadView('owner.laporan_pdf', compact('transaksis', 'totalPendapatan'));
        return $pdf->download('Laporan-Pendapatan-lannPark.pdf');
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $platNomor = $transaksi->kendaraan->plat_nomor ?? $transaksi->plat_nomor ?? '-';
        
        // 📝 CATAT LOG
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Hapus Riwayat',
            'description' => 'Owner menghapus riwayat transaksi secara permanen untuk plat nomor ' . $platNomor
        ]);

        $transaksi->delete();

        return redirect()->back()->with('success', 'Riwayat transaksi berhasil dihapus dan pendapatan telah disesuaikan.');
    }
}