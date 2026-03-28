@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
    <style>
        /* CSS Pagination Lanjutan untuk lannPark */
        nav .pagination { display: flex; list-style: none; gap: 8px; justify-content: center; align-items: center; margin: 0; padding: 0; }
        nav .pagination .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px; background: white; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 700; transition: 0.3s; border: 1px solid #e2e8f0; }
        nav .pagination .page-item.active .page-link { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); }
        nav .pagination .page-item:not(.active):not(.disabled) .page-link:hover { background: #f8fafc; color: #3b82f6; border-color: #cbd5e1; transform: translateY(-2px); }
        nav .pagination .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; cursor: not-allowed; }
        nav p.small.text-muted { display: none !important; }
        .flex.justify-between.flex-1.sm\:hidden { display: none !important; }
    </style>

    <div style="margin-bottom: 30px;">
        <h2 style="font-weight: 800; color: var(--primary-dark);">Log Aktivitas 🕵️‍♂️</h2>
        <p style="color: #64748b; font-size: 14px;">Pantau semua riwayat tindakan dari Admin, Petugas, dan Owner secara real-time.</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">WAKTU</th>
                    <th style="width: 25%;">PENGGUNA</th>
                    <th style="width: 15%;">AKSI</th>
                    <th style="width: 45%;">DETAIL KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        
                        {{-- 1. Waktu --}}
                        <td style="color: #64748b; font-size: 13px;">
                            <i class="far fa-clock" style="margin-right: 5px;"></i> 
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>

                        {{-- 2. Pengguna & Role --}}
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="background: var(--primary); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px;">
                                    {{ strtoupper(substr($log->user->nama_lengkap ?? 'S', 0, 1)) }}
                                </div>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 700; color: var(--text-main);">{{ $log->user->nama_lengkap ?? 'Sistem / Terhapus' }}</span>
                                    <span style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">{{ $log->user->role ?? 'unknown' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 3. Aksi dengan Label Warna Dinamis --}}
                        <td>
                            @php
                                $activity = strtolower($log->activity);
                                $bg = '#f1f5f9'; $color = '#475569'; // Default Gray

                                if(Str::contains($activity, ['masuk', 'tambah', 'simpan'])) { $bg = '#dcfce7'; $color = '#166534'; } 
                                elseif(Str::contains($activity, ['keluar', 'bayar', 'checkout'])) { $bg = '#eff6ff'; $color = '#1e40af'; }
                                elseif(Str::contains($activity, ['hapus', 'delete', 'batal'])) { $bg = '#fee2e2'; $color = '#991b1b'; }
                                elseif(Str::contains($activity, ['edit', 'update'])) { $bg = '#fef3c7'; $color = '#92400e'; }
                            @endphp
                            <span style="background: {{ $bg }}; color: {{ $color }}; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $log->activity }}
                            </span>
                        </td>

                        {{-- 4. Keterangan --}}
                        <td style="color: #475569; font-size: 14px;">
                            {{ $log->description }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <i class="fas fa-clipboard-list" style="font-size: 40px; opacity: 0.5; margin-bottom: 10px; display: block;"></i>
                            Belum ada riwayat aktivitas yang tercatat di sistem.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Navigasi Halaman --}}
    @if($logs->hasPages())
        <div style="margin-top: 20px;">
            <nav>
                {{ $logs->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
@endsection