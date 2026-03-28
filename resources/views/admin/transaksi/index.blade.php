@extends('layouts.admin')

@section('title', 'Monitoring Kendaraan')

@section('content')
    {{-- CSS Khusus Pagination & Badge --}}
    <style>
        nav .pagination { display: flex; list-style: none; gap: 8px; justify-content: center; align-items: center; margin: 0; padding: 0; }
        nav .pagination .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px; background: white; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 700; transition: 0.3s; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        nav .pagination .page-item.active .page-link { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); }
        nav .pagination .page-item:not(.active):not(.disabled) .page-link:hover { background: #f8fafc; color: #3b82f6; border-color: #cbd5e1; transform: translateY(-2px); }
        nav .pagination .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; cursor: not-allowed; }
        nav p.small.text-muted { display: none !important; }
        .flex.justify-between.flex-1.sm\:hidden { display: none !important; }
        
        .btn-add-premium { background: var(--primary); color: white; padding: 12px 20px; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2); font-size: 14px; }
        .btn-add-premium:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3); }
    </style>

    <div style="margin-bottom: 30px;">
        <h2 style="font-weight: 800; color: var(--primary-dark); margin-bottom: 5px;">Monitoring lann<span style="color: var(--text-main);">Park</span> 🚘</h2>
        <p style="color: #64748b; font-size: 14px;">Kelola data kendaraan yang sedang parkir atau riwayat transaksi.</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 25px; border: 1px solid #e2e8f0;">
        
        {{-- Tombol Tambah --}}
        <div style="margin-bottom: 20px;">
            <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn-add-premium">
                <i class="fas fa-plus-circle"></i> Tambah Kendaraan Manual
            </button>
        </div>
        
        <div style="overflow-x: auto; border-radius: 10px; border: 1px solid #e2e8f0;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0;">WAKTU MASUK</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0;">PLAT NOMOR</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0;">AREA PARKIR</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0;">STATUS</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                    <tr style="transition: 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 15px 20px; color: #64748b; font-size: 14px;">
                            <i class="far fa-clock" style="margin-right: 5px;"></i> 
                            {{ \Carbon\Carbon::parse($t->created_at ?? $t->waktu_masuk)->format('d M Y, H:i') }}
                        </td>
                        <td style="padding: 15px 20px;">
                            <b style="color: var(--primary); font-size: 15px; letter-spacing: 1px;">{{ $t->kendaraan->plat_nomor ?? $t->plat_nomor ?? '-' }}</b>
                        </td>
                        <td style="padding: 15px 20px; color: #475569; text-transform: capitalize;">
                            {{ $t->area->nama_area ?? 'Tanpa Area' }}
                        </td>
                        <td style="padding: 15px 20px;">
                            @if(strtolower($t->status) == 'masuk')
                                <span style="background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px;">MASUK (PARKIR)</span>
                            @else
                                <span style="background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px;">KELUAR / SELESAI</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px; display: flex; gap: 8px; justify-content: center;">
                            {{-- Tombol Edit --}}
                            <button onclick="openEditTransaksi('{{ $t->id }}', '{{ $t->kendaraan->plat_nomor ?? $t->plat_nomor ?? '' }}', '{{ $t->area_parkir_id }}')" style="background: #eff6ff; color: #3b82f6; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'" title="Edit Area/Plat">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Tombol Hapus --}}
                            <form action="/admin/transaksi/{{ $t->id }}" method="POST" onsubmit="return confirm('Hapus data kendaraan ini secara permanen?')" style="margin: 0;">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Hapus Data">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <i class="fas fa-car-side" style="font-size: 40px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                            Belum ada data kendaraan yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Navigasi Pagination Laravel --}}
        @if(method_exists($transaksis, 'hasPages') && $transaksis->hasPages())
            <div style="margin-top: 25px;">
                <nav>
                    {{ $transaksis->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH KENDARAAN --}}
    <div id="modalTambah" style="display:none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 450px; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: none;">
            <h3 style="margin: 0 0 20px 0; color: var(--text-main); font-weight: 800;"><i class="fas fa-car-side" style="color: var(--primary);"></i> Input Kendaraan</h3>
            <form action="/admin/transaksi" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #475569;">PLAT NOMOR</label>
                    <input type="text" name="plat_nomor" placeholder="Contoh: D 1234 ABC" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 10px; text-transform: uppercase; font-weight: bold; font-size: 15px; outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display:block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #475569;">AREA PARKIR</label>
                    <select name="area_parkir_id" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; cursor: pointer;">
                        <option value="">-- Pilih Lokasi Parkir --</option>
                        @foreach($areas as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_area }} (Sisa Slot: {{ $a->kapasitas }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="padding: 12px 20px; border-radius: 10px; border: none; background: #f1f5f9; color: #475569; font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'">Batal</button>
                    <button type="submit" style="padding: 12px 20px; border-radius: 10px; border: none; background: var(--primary); color: white; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);" onmouseover="this.style.background='var(--primary-dark)'">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT KENDARAAN --}}
    <div id="modalEdit" style="display:none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 450px; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: none;">
            <h3 style="margin: 0 0 20px 0; color: var(--text-main); font-weight: 800;"><i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Data Parkir</h3>
            <form id="formEditTransaksi" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #475569;">PLAT NOMOR</label>
                    <input type="text" name="plat_nomor" id="edit_plat" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 10px; text-transform: uppercase; font-weight: bold; font-size: 15px; outline: none;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display:block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #475569;">PINDAHKAN AREA</label>
                    <select name="area_parkir_id" id="edit_area" required style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; cursor: pointer;">
                        @foreach($areas as $a)
                            <option value="{{ $a->id }}">{{ $a->nama_area }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modalEdit').style.display='none'" style="padding: 12px 20px; border-radius: 10px; border: none; background: #f1f5f9; color: #475569; font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'">Batal</button>
                    <button type="submit" style="padding: 12px 20px; border-radius: 10px; border: none; background: #f59e0b; color: white; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);" onmouseover="this.style.background='#d97706'">Update Data</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditTransaksi(id, plat, areaId) {
            document.getElementById('formEditTransaksi').action = '/admin/transaksi/' + id;
            document.getElementById('edit_plat').value = plat;
            document.getElementById('edit_area').value = areaId;
            document.getElementById('modalEdit').style.display = 'flex';
        }
    </script>
@endsection