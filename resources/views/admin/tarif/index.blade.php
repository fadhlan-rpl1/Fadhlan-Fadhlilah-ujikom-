@extends('layouts.admin')

@section('title', 'Kelola Tarif Parkir')

@section('content')
    {{-- CSS Khusus Form & Pagination lannPark --}}
    <style>
        .input-premium { width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; outline: none; transition: 0.3s; background: #f8fafc; }
        .input-premium:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); background: white; }
        
        .btn-add-premium { background: var(--primary); color: white; padding: 12px 20px; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2); font-size: 14px; height: 100%; }
        .btn-add-premium:hover { background: var(--primary-dark); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3); }

        nav .pagination { display: flex; list-style: none; gap: 8px; justify-content: center; align-items: center; margin: 0; padding: 0; }
        nav .pagination .page-item .page-link { display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border-radius: 10px; background: white; color: #64748b; text-decoration: none; font-size: 14px; font-weight: 700; transition: 0.3s; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        nav .pagination .page-item.active .page-link { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); }
        nav .pagination .page-item:not(.active):not(.disabled) .page-link:hover { background: #f8fafc; color: #3b82f6; border-color: #cbd5e1; transform: translateY(-2px); }
        nav .pagination .page-item.disabled .page-link { color: #cbd5e1; background: #f8fafc; cursor: not-allowed; }
        nav p.small.text-muted { display: none !important; }
        .flex.justify-between.flex-1.sm\:hidden { display: none !important; }
    </style>

    <div style="margin-bottom: 30px;">
        <h2 style="font-weight: 800; color: var(--primary-dark); margin-bottom: 5px;">Pengaturan <span style="color: var(--text-main);">Tarif</span> 💰</h2>
        <p style="color: #64748b; font-size: 14px;">Kelola biaya parkir per jam berdasarkan kategori kendaraan.</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- KOTAK TAMBAH TARIF --}}
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 25px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
        <h4 style="margin: 0 0 20px 0; font-size: 14px; font-weight: 800; color: var(--primary-dark); display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-tags" style="color: var(--primary);"></i> TAMBAH TARIF BARU
        </h4>
        
        <form action="/admin/tarif" method="POST" style="display: grid; grid-template-columns: 2fr 1.5fr auto; gap: 15px; align-items: flex-end;">
            @csrf
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">JENIS KENDARAAN</label>
                <input type="text" name="jenis_kendaraan" placeholder="Contoh: Motor / Mobil / Truk" required class="input-premium">
            </div>
            <div>
                <label style="display:block; font-size:12px; font-weight:700; color:#475569; margin-bottom:8px;">BIAYA PER JAM (Rp)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 15px; top: 12px; color: #94a3b8; font-weight: 600;">Rp</span>
                    <input type="number" name="tarif_per_jam" placeholder="3000" required class="input-premium" style="padding-left: 45px;">
                </div>
            </div>
            <div style="height: 47px;">
                <button type="submit" class="btn-add-premium" style="width: 100%;">
                    <i class="fas fa-plus-circle"></i> Simpan Tarif
                </button>
            </div>
        </form>
    </div>

    {{-- TABEL DATA TARIF --}}
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #e2e8f0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0; width: 80px;">NO</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0;">JENIS KENDARAAN</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0;">TARIF / JAM</th>
                        <th style="padding: 15px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #e2e8f0; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifs as $index => $t)
                    <tr style="transition: 0.2s; border-bottom: 1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        
                        {{-- Logika Penomoran Support Pagination --}}
                        <td style="padding: 15px 20px; color: #94a3b8; font-weight: 600;">
                            {{ method_exists($tarifs, 'firstItem') ? $tarifs->firstItem() + $index : $loop->iteration }}
                        </td>
                        
                        <td style="padding: 15px 20px;">
                            <b style="color: var(--primary-dark); font-size: 15px; text-transform: capitalize;">{{ $t->jenis_kendaraan }}</b>
                        </td>
                        
                        <td style="padding: 15px 20px;">
                            <span style="background: #dcfce7; color: #15803d; padding: 6px 14px; border-radius: 8px; font-weight: 800; font-size: 14px;">
                                Rp {{ number_format($t->tarif_per_jam, 0, ',', '.') }}
                            </span>
                        </td>
                        
                        <td style="padding: 15px 20px; display: flex; gap: 8px; justify-content: center;">
                            <button onclick="openEditTarif('{{ $t->id }}', '{{ $t->jenis_kendaraan }}', '{{ $t->tarif_per_jam }}')" style="background: #eff6ff; color: #3b82f6; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'" title="Edit Tarif">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form action="/admin/tarif/{{ $t->id }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis tarif ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Hapus Tarif">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 60px; color: #94a3b8;">
                            <i class="fas fa-money-bill-wave" style="font-size: 40px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                            Belum ada data tarif yang didaftarkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Navigasi Pagination (Jika diaktifkan di Controller) --}}
        @if(method_exists($tarifs, 'hasPages') && $tarifs->hasPages())
            <div style="padding: 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                <nav>
                    {{ $tarifs->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        @endif
    </div>

    {{-- MODAL EDIT TARIF --}}
    <div id="modalEditTarif" style="display:none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div class="card" style="width: 450px; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: none;">
            <h3 style="margin: 0 0 20px 0; color: var(--text-main); font-weight: 800;"><i class="fas fa-edit" style="color: #f59e0b;"></i> Edit Tarif</h3>
            <form id="formEditTarif" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #475569;">JENIS KENDARAAN</label>
                    <input type="text" name="jenis_kendaraan" id="edit_jenis" required class="input-premium">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display:block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #475569;">BIAYA PER JAM (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 15px; top: 12px; color: #94a3b8; font-weight: 600;">Rp</span>
                        <input type="number" name="tarif_per_jam" id="edit_biaya" required class="input-premium" style="padding-left: 45px;">
                    </div>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('modalEditTarif').style.display='none'" style="padding: 12px 20px; border-radius: 10px; border: none; background: #f1f5f9; color: #475569; font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'">Batal</button>
                    <button type="submit" style="padding: 12px 20px; border-radius: 10px; border: none; background: #f59e0b; color: white; font-weight: 700; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);" onmouseover="this.style.background='#d97706'">Update Tarif</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditTarif(id, jenis, biaya) {
            document.getElementById('formEditTarif').action = '/admin/tarif/' + id;
            document.getElementById('edit_jenis').value = jenis;
            document.getElementById('edit_biaya').value = biaya;
            document.getElementById('modalEditTarif').style.display = 'flex';
        }
    </script>
@endsection