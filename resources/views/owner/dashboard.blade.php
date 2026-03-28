@extends('layouts.admin')

@section('title', 'Dashboard Owner')

@section('content')
    {{-- CSS Khusus untuk mempercantik tombol Pagination --}}
    <style>
        nav .pagination { display: flex; list-style: none; gap: 8px; justify-content: center; align-items: center; margin: 0; padding: 0; }
        nav .pagination .page-item .page-link { 
            display: flex; align-items: center; justify-content: center; 
            min-width: 38px; height: 38px; padding: 0 12px; 
            border-radius: 10px; background: white; color: #64748b; 
            text-decoration: none; font-size: 14px; font-weight: 700; 
            transition: 0.3s; border: 1px solid #e2e8f0; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.02); 
        }
        nav .pagination .page-item.active .page-link { 
            background: #3b82f6; color: white; border-color: #3b82f6; 
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); z-index: 2; 
        }
        nav .pagination .page-item:not(.active):not(.disabled) .page-link:hover { 
            background: #f8fafc; color: #3b82f6; border-color: #cbd5e1; transform: translateY(-2px); 
        }
        nav .pagination .page-item.disabled .page-link { 
            color: #cbd5e1; background: #f8fafc; cursor: not-allowed; box-shadow: none; 
        }
        
        nav p.small.text-muted { display: none !important; }
        .flex.justify-between.flex-1.sm\:hidden { display: none !important; }
    </style>

    <div style="margin-bottom: 30px;">
        <h2 style="font-weight: 800; color: var(--primary-dark);">lann<span style="color: var(--text-main);">Park</span> </h2>
        <p style="color: #64748b; font-size: 14px;">Ringkasan pendapatan dan manajemen riwayat transaksi.</p>
    </div>

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 1. Ringkasan Statistik Utama --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 20px; border-radius: 12px; margin-bottom: 0;">
            <p style="font-size: 14px; margin-bottom: 5px; opacity: 0.9; font-weight: 600;">Pendapatan Hari Ini</p>
            <h2 style="margin: 0; font-size: 28px;">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h2>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; padding: 20px; border-radius: 12px; margin-bottom: 0;">
            <p style="font-size: 14px; margin-bottom: 5px; opacity: 0.9; font-weight: 600;">Pendapatan Bulan Ini</p>
            <h2 style="margin: 0; font-size: 28px;">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h2>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; padding: 20px; border-radius: 12px; margin-bottom: 0;">
            <p style="font-size: 14px; margin-bottom: 5px; opacity: 0.9; font-weight: 600;">Total Kendaraan Keluar</p>
            <h2 style="margin: 0; font-size: 28px;">{{ $totalKendaraan }} Unit</h2>
        </div>
    </div>

    {{-- 2. FITUR BARU: Rekap 12 Bulan --}}
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 25px; margin-bottom: 30px; border: 1px solid #e2e8f0;">
        <h4 style="margin: 0 0 20px 0; font-weight: 700; color: var(--text-main);">
            <i class="fas fa-chart-line" style="color: var(--primary); margin-right: 8px;"></i> 
            Pendapatan Bulanan Tahun {{ $tahunIni }}
        </h4>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px;">
            @foreach($pendapatanBulanan as $bulan => $total)
                @php 
                    // Beri warna khusus jika itu adalah bulan yang sedang berjalan
                    $isCurrentMonth = ($bulan == $bulanSekarang); 
                @endphp
                
                <div style="border: 1px solid {{ $isCurrentMonth ? 'var(--primary)' : '#e2e8f0' }}; border-radius: 10px; padding: 15px; background: {{ $isCurrentMonth ? '#eff6ff' : '#f8fafc' }}; text-align: center; transition: 0.3s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                    
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: {{ $isCurrentMonth ? 'var(--primary-dark)' : '#64748b' }}; text-transform: uppercase; letter-spacing: 1px;">
                        {{ $namaBulan[$bulan] }}
                        @if($isCurrentMonth) <span style="font-size: 10px; color: #ef4444;" title="Bulan Ini">🟢</span> @endif
                    </p>
                    
                    <h3 style="margin: 8px 0 0 0; font-size: 16px; font-weight: 800; color: {{ $total > 0 ? '#059669' : '#94a3b8' }};">
                        {{ $total > 0 ? 'Rp ' . number_format($total, 0, ',', '.') : '-' }}
                    </h3>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 3. Tabel Riwayat Transaksi --}}
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #e2e8f0;">
            <h4 style="margin:0; font-weight: 700;">Riwayat Transaksi (Sukses)</h4>
            <a href="/owner/laporan/download" style="background: #1e293b; color: white; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 13px; transition: 0.3s;" onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='#1e293b'">
                <i class="fas fa-file-pdf"></i> Download Laporan PDF
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b;">PLAT NOMOR</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b;">WAKTU MASUK</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b;">WAKTU KELUAR</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b;">DURASI</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; text-align: right;">PENDAPATAN</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $tr)
                        @php
                            $platNomor = $tr->kendaraan->plat_nomor ?? $tr->plat_nomor ?? '-';
                            $masuk = \Carbon\Carbon::parse($tr->created_at);
                            $keluar = \Carbon\Carbon::parse($tr->updated_at);
                            $durasi = max(1, ceil($masuk->diffInHours($keluar)));
                            $biaya = $durasi * ($tr->tarif->tarif_per_jam ?? 3000);
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 15px 20px; font-weight: 700; color: var(--primary);">{{ $platNomor }}</td>
                            <td style="padding: 15px 20px; color: #475569;">{{ $masuk->format('d/m/Y H:i') }}</td>
                            <td style="padding: 15px 20px; color: #475569;">{{ $keluar->format('d/m/Y H:i') }}</td>
                            <td style="padding: 15px 20px; color: #475569;">{{ $durasi }} Jam</td>
                            <td style="padding: 15px 20px; font-weight: 800; color: #059669; text-align: right;">
                                Rp {{ number_format($biaya, 0, ',', '.') }}
                            </td>
                            <td style="padding: 15px 20px; text-align: center;">
                                <form action="/owner/transaksi/delete/{{ $tr->id }}" method="POST" onsubmit="return confirm('Hapus riwayat ini? Uang pendapatan dari plat nomor ini akan ikut terhapus dari statistik dashboard.');" style="margin: 0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Hapus Riwayat">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 80px; color: #94a3b8;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 10px;">
                                    <i class="fas fa-box-open" style="font-size: 40px; opacity: 0.5;"></i>
                                    <p>Belum ada data transaksi kendaraan yang keluar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Desain Kotak Pagination --}}
        @if($transaksis->hasPages())
            <div style="padding: 20px; display: flex; justify-content: center; background: white; border-top: 1px solid #e2e8f0;">
                <nav>
                    {{ $transaksis->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif

    </div>
@endsection