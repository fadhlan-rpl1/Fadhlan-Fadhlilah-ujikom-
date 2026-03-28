@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 28px; font-weight: 800; color: var(--primary-dark); margin: 0;">Halo, {{ auth()->user()->nama_lengkap }}! ✨</h1>
            <p style="color: #64748b; font-size: 14px; margin-top: 5px;">Pantau kondisi parkir lannPark hari ini secara real-time.</p>
        </div>
        <div style="text-align: right;">
            <span style="background: #eff6ff; color: #3b82f6; padding: 8px 15px; border-radius: 10px; font-weight: 700; font-size: 13px; border: 1px solid #dbeafe;">
                <i class="far fa-calendar-alt"></i> {{ date('d M Y') }}
            </span>
        </div>
    </div>

    {{-- BARIS 1: STATISTIK RINGKAS --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="width: 50px; height: 50px; background: #dcfce7; color: #166534; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-car-side"></i>
            </div>
            <div>
                <p style="margin: 0; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Parkir Aktif</p>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800; color: #1e293b;">{{ \App\Models\Transaksi::where('status', 'masuk')->count() }} <span style="font-size: 14px; font-weight: 400; color: #94a3b8;">Unit</span></h2>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="width: 50px; height: 50px; background: #eff6ff; color: #1d4ed8; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <p style="margin: 0; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Selesai Hari Ini</p>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800; color: #1e293b;">{{ \App\Models\Transaksi::whereDate('updated_at', today())->whereIn('status', ['keluar', 'selesai'])->count() }} <span style="font-size: 14px; font-weight: 400; color: #94a3b8;">Unit</span></h2>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="width: 50px; height: 50px; background: #fef3c7; color: #b45309; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p style="margin: 0; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Staff</p>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800; color: #1e293b;">{{ \App\Models\User::count() }} <span style="font-size: 14px; font-weight: 400; color: #94a3b8;">User</span></h2>
            </div>
        </div>
    </div>

    {{-- BARIS 2: MONITORING KAPASITAS AREA --}}
    <div style="background: white; border-radius: 15px; padding: 25px; border: 1px solid #e2e8f0; margin-bottom: 30px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
        <h3 style="margin: 0 0 20px 0; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-parking" style="color: #3b82f6;"></i> Monitoring Kapasitas Area
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            @foreach(\App\Models\AreaParkir::all() as $area)
                @php
                    // Logika persentase (Contoh: jika kamu punya sistem counter terisi)
                    // Jika tidak ada kolom 'terisi', kita gunakan manual atau biarkan sebagai slot total
                    $total = $area->kapasitas;
                    $terisi = \App\Models\Transaksi::where('area_parkir_id', $area->id)->where('status', 'masuk')->count();
                    $persen = $total > 0 ? ($terisi / ($terisi + $total)) * 100 : 0;
                    $color = $persen > 80 ? '#ef4444' : ($persen > 50 ? '#f59e0b' : '#10b981');
                @endphp
                <div style="border: 1px solid #f1f5f9; padding: 15px; border-radius: 12px; background: #f8fafc;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-weight: 700; color: #475569;">{{ $area->nama_area }}</span>
                        <span style="font-size: 12px; font-weight: 800; color: {{ $color }};">{{ $total }} Slot Tersisa</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 10px; overflow: hidden;">
                        <div style="width: {{ $persen }}%; height: 100%; background: {{ $color }}; transition: 0.5s;"></div>
                    </div>
                    <p style="margin: 8px 0 0 0; font-size: 11px; color: #94a3b8;">
                        <i class="fas fa-info-circle"></i> Terisi: {{ $terisi }} unit | Status: {{ $total > 0 ? 'Tersedia' : 'Penuh' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- BARIS 3: TOMBOL NAVIGASI CEPAT --}}
    <h3 style="margin: 0 0 20px 0; font-weight: 800; color: #1e293b;">Navigasi Cepat</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <a href="/admin/transaksi" style="text-decoration: none; background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; text-align: center; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#3b82f6'; this.style.transform='translateY(-5px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
            <i class="fas fa-car" style="font-size: 24px; color: #3b82f6; margin-bottom: 10px;"></i>
            <h4 style="margin: 0; color: #1e293b;">Data Parkir</h4>
        </a>

        <a href="/admin/area" style="text-decoration: none; background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; text-align: center; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#10b981'; this.style.transform='translateY(-5px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
            <i class="fas fa-map-marked-alt" style="font-size: 24px; color: #10b981; margin-bottom: 10px;"></i>
            <h4 style="margin: 0; color: #1e293b;">Kelola Area</h4>
        </a>

        <a href="/admin/tarif" style="text-decoration: none; background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; text-align: center; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#f59e0b'; this.style.transform='translateY(-5px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
            <i class="fas fa-money-bill-wave" style="font-size: 24px; color: #f59e0b; margin-bottom: 10px;"></i>
            <h4 style="margin: 0; color: #1e293b;">Set Tarif</h4>
        </a>

        <a href="/admin/users" style="text-decoration: none; background: white; padding: 20px; border-radius: 15px; border: 1px solid #e2e8f0; text-align: center; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onmouseover="this.style.borderColor='#6366f1'; this.style.transform='translateY(-5px)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'">
            <i class="fas fa-users-cog" style="font-size: 24px; color: #6366f1; margin-bottom: 10px;"></i>
            <h4 style="margin: 0; color: #1e293b;">Data User</h4>
        </a>
    </div>
@endsection