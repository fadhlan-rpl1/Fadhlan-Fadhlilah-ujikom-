@extends('layouts.admin')

@section('content')
    <div style="margin-bottom: 30px;">
        <h2 style="font-weight: 700;">Panel Operasional Petugas </h2>
        <p style="color: var(--text-muted); font-size: 14px;">Monitor biaya parkir sesuai tarif admin secara realtime.</p>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h4 style="margin-bottom: 20px; color: var(--primary);">Catat Kendaraan Masuk</h4>
        <form action="/petugas/transaksi/store" method="POST" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px; align-items: flex-end;">
            @csrf
            <input type="text" name="plat_nomor" placeholder="B 1234 ABC" required style="padding:12px; border:1px solid #e2e8f0; border-radius:12px; text-transform: uppercase;">
            <select name="tarif_id" required style="padding:12px; border:1px solid #e2e8f0; border-radius:12px; background: white;">
                @foreach($tarifs as $t)
                    <option value="{{ $t->id }}">{{ $t->jenis_kendaraan }}</option>
                @endforeach
            </select>
            <select name="area_parkir_id" required style="padding:12px; border:1px solid #e2e8f0; border-radius:12px; background: white;">
                @foreach($areas as $a)
                    <option value="{{ $a->id }}">{{ $a->nama_area }}</option>
                @endforeach
            </select>
            <button type="submit" class="badge-primary" style="height:46px; border:none; cursor:pointer; padding: 0 25px; border-radius: 12px; font-weight:700; background: var(--primary); color: white;">Simpan</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>PLAT NOMOR</th>
                    <th>JAM MASUK</th>
                    <th>BIAYA (REALTIME)</th>
                    <th style="text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $tr)
                @php
                    // MENCARI DATA (Di Kendaraan ATAU di Transaksi)
                    $platNomor = $tr->kendaraan->plat_nomor ?? $tr->plat_nomor ?? 'Kosong';
                    $hargaPerJam = $tr->kendaraan->tarif->tarif_per_jam ?? $tr->tarif->tarif_per_jam ?? 0;
                @endphp
                <tr>
                    <td style="font-weight: 700; color: var(--primary);">{{ $platNomor }}</td>
                    <td>{{ $tr->created_at->format('H:i') }}</td>
                    
                    <td style="font-weight: 800; color: #059669;" 
                        class="biaya-update" 
                        data-masuk="{{ $tr->created_at->toIso8601String() }}" 
                        data-tarif="{{ $hargaPerJam }}">
                        Rp 0
                    </td>

                    <td style="text-align: center; display: flex; justify-content: center; gap: 8px;">
                        {{-- 1. TOMBOL EDIT --}}
                        <button type="button" onclick="bukaModalEdit({{ $tr->id }}, '{{ $platNomor }}', '{{ $tr->tarif_id }}', '{{ $tr->area_parkir_id }}')" style="border:none; cursor:pointer; padding:8px 15px; border-radius: 8px; font-weight: 700; background: #eab308; color: white;">
                            Edit
                        </button>

                        {{-- 2. TOMBOL BAYAR --}}
                        <form action="/petugas/transaksi/bayar/{{ $tr->id }}" method="POST">
                            @csrf @method('PUT')
                            <button type="submit" style="border:none; cursor:pointer; padding:8px 15px; border-radius: 8px; font-weight: 700; background: #22c55e; color: white;">
                                Bayar
                            </button>
                        </form>

                        {{-- 3. TOMBOL HAPUS --}}
                        <form action="/petugas/transaksi/delete/{{ $tr->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kendaraan ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="border:none; cursor:pointer; padding:8px 15px; border-radius: 8px; font-weight: 700; background: #ef4444; color: white;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; padding:50px; color: var(--text-muted);">Tidak ada kendaraan yang sedang parkir.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="modalEdit" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div class="card" style="width: 400px; position:relative; margin:0; background:white; padding: 25px; border-radius: 12px;">
            <button type="button" onclick="tutupModalEdit()" style="position:absolute; top:15px; right:20px; background:none; border:none; font-size:24px; cursor:pointer; color:#ef4444;">&times;</button>
            <h4 style="color: var(--primary); margin-bottom: 20px;">Edit Kendaraan</h4>
            
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div style="margin-bottom: 15px;">
                    <label style="font-size:12px; font-weight:bold; color:var(--text-muted); display:block; margin-bottom:5px;">Plat Nomor</label>
                    <input type="text" name="plat_nomor" id="edit_plat" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; text-transform: uppercase;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="font-size:12px; font-weight:bold; color:var(--text-muted); display:block; margin-bottom:5px;">Jenis Kendaraan</label>
                    <select name="tarif_id" id="edit_tarif" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                        @foreach($tarifs as $t) 
                            <option value="{{ $t->id }}">{{ $t->jenis_kendaraan }}</option> 
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="font-size:12px; font-weight:bold; color:var(--text-muted); display:block; margin-bottom:5px;">Area Parkir</label>
                    <select name="area_parkir_id" id="edit_area" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
                        @foreach($areas as $a) 
                            <option value="{{ $a->id }}">{{ $a->nama_area }}</option> 
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="width:100%; padding:12px; border:none; border-radius:8px; background:var(--primary); color:white; font-weight:bold; cursor:pointer;">Update Data</button>
            </form>
        </div>
    </div>

    <script>
        // 1. Script Hitung Biaya Otomatis
        function hitungBiayaRealtime() {
            const items = document.querySelectorAll('.biaya-update');
            const now = new Date();

            items.forEach(el => {
                const waktuMasuk = new Date(el.dataset.masuk);
                const tarifPerJam = parseInt(el.dataset.tarif);
                
                const selisihMs = now - waktuMasuk;
                const selisihJam = Math.max(1, Math.ceil(selisihMs / (1000 * 60 * 60)));
                
                const totalBiaya = selisihJam * tarifPerJam;
                el.innerText = "Rp " + totalBiaya.toLocaleString('id-ID');
            });
        }

        setInterval(hitungBiayaRealtime, 1000);
        hitungBiayaRealtime();

        // 2. Script Buka Tutup Modal Edit
        function bukaModalEdit(id, plat, tarifId, areaId) {
            document.getElementById('modalEdit').style.display = 'flex';
            
            // Set action URL form agar mengarah ke ID yang benar
            document.getElementById('formEdit').action = '/petugas/transaksi/update-data/' + id;
            
            // Isi form dengan data kendaraan saat ini
            document.getElementById('edit_plat').value = plat;
            document.getElementById('edit_tarif').value = tarifId;
            document.getElementById('edit_area').value = areaId;
        }

        function tutupModalEdit() {
            document.getElementById('modalEdit').style.display = 'none';
        }
    </script>
@endsection