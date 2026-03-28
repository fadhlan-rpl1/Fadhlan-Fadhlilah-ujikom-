<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Illuminate\Support\Facades\Auth; 

class OwnerController extends Controller
{
    public function index()
    {
        $allTransactions = Transaksi::with(['tarif'])
                        ->where('status', 'keluar')
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
            $keluar = Carbon::parse($tr->waktu_keluar);
            $totalBiaya = $tr->biaya_total;

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

        $transaksis = Transaksi::with(['tarif', 'area'])
                        ->where('status', 'keluar')
                        ->latest('waktu_keluar')
                        ->paginate(5); 

        return view('owner.dashboard', compact(
            'transaksis', 'pendapatanHariIni', 'pendapatanBulanIni', 
            'totalKendaraan', 'pendapatanBulanan', 'namaBulan', 'tahunIni', 'bulanSekarang'
        ));
    }

    public function downloadPDF()
    {
        $transaksis = Transaksi::with(['tarif', 'area'])
                        ->where('status', 'keluar')
                        ->get();

        $totalPendapatan = $transaksis->sum('biaya_total');

        ActivityLog::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Download Laporan',
        ]);

        $pdf = Pdf::loadView('owner.laporan_pdf', compact('transaksis', 'totalPendapatan'));
        return $pdf->download('Laporan-Pendapatan-lannPark.pdf');
    }
}