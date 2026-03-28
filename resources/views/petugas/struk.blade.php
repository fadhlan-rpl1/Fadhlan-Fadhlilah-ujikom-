<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk lannPark - {{ $platNomor }}</title>
    <style>
        /* Base Setup */
        * { box-sizing: border-box; }
        
        body { 
            background: #f1f5f9; 
            font-family: 'Courier New', Courier, monospace; 
            display: flex; 
            flex-direction: column;
            align-items: center; 
            padding: 40px 0; 
            margin: 0;
        }
        
        /* Container Struk */
        .struk-container { 
            background: white; 
            width: 350px; 
            padding: 30px; 
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); 
            border-radius: 4px;
            position: relative;
        }

        /* Header Branding lannPark */
        .text-center { text-align: center; }
        .header-title { 
            font-size: 24px; 
            font-weight: 900; 
            letter-spacing: 1px; 
            margin: 0; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        .header-title span { color: #3b82f6; }
        .header-sub { font-size: 11px; color: #666; margin: 5px 0; line-height: 1.4; font-family: sans-serif; }
        
        .divider { border-bottom: 1px dashed #444; margin: 15px 0; }
        
        /* Baris Data */
        .row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
        .label { color: #555; text-transform: uppercase; }
        .value { font-weight: bold; text-align: right; }
        
        /* Total Section */
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            margin-top: 15px; 
            padding: 12px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        .total-label { font-size: 16px; font-weight: 900; }
        .total-value { font-size: 20px; font-weight: 900; }

        /* Tombol Aksi - Simetris Sempurna */
        .actions { 
            width: 350px; 
            margin-top: 25px; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }

        .btn-print, .btn-back { 
            width: 100%; 
            padding: 14px; 
            font-size: 14px;
            font-weight: bold; 
            text-align: center; 
            text-decoration: none; 
            border-radius: 10px; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: 0.3s;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-sizing: border-box;
        }

        .btn-print { 
            background: #1e293b; 
            color: white; 
        }

        .btn-back { 
            background: #ffffff; 
            color: #64748b; 
            border: 1px solid #cbd5e1; 
        }

        .btn-print:hover { background: #0f172a; transform: translateY(-2px); }
        .btn-back:hover { background: #f8fafc; border-color: #94a3b8; transform: translateY(-2px); }

        /* Barcode Visual */
        .barcode { 
            display: flex; 
            justify-content: center; 
            gap: 2px; 
            height: 35px; 
            margin-top: 20px; 
            opacity: 0.8; 
        }
        .barcode div { background: black; height: 100%; }

        @media print {
            body { background: white; padding: 0; }
            .struk-container { box-shadow: none; width: 100%; border: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

    <div class="struk-container">
        <div class="text-center">
            <h2 class="header-title">lann<span>Park</span></h2>
            <p class="header-sub">Sistem Manajemen Parkir Mandiri<br>Bandung, Jawa Barat</p>
        </div>
        
        <div class="divider"></div>
        
        <div class="row">
            <span class="label">KODE TRANS:</span> 
            <span class="value">{{ $transaksi->kode_transaksi }}</span>
        </div>
        <div class="row">
            <span class="label">PETUGAS:</span> 
            <span class="value">{{ auth()->user()->nama_lengkap }}</span>
        </div>
        
        <div class="divider"></div>
        
        <div class="row">
            <span class="label">PLAT NOMOR:</span> 
            <span class="value" style="font-size: 16px; letter-spacing: 1px;">{{ $platNomor }}</span>
        </div>
        <div class="row">
            <span class="label">KENDARAAN:</span> 
            <span class="value">{{ $transaksi->tarif->jenis_kendaraan ?? '-' }}</span>
        </div>
        
        <div class="divider"></div>
        
        <div class="row">
            <span class="label">WAKTU MASUK:</span> 
            <span class="value">{{ $masuk->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span class="label">WAKTU KELUAR:</span> 
            <span class="value">{{ $keluar->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span class="label">DURASI:</span> 
            <span class="value">{{ $durasi }} JAM</span>
        </div>
        
        <div class="total-row">
            <span class="total-label">TOTAL</span>
            <span class="total-value">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</span>
        </div>
        
        <p class="text-center" style="font-size: 10px; margin-top: 25px; line-height: 1.5; color: #444; font-family: sans-serif;">
            *** TERIMA KASIH ***<br>
            Layanan Parkir Terintegrasi by <b>lannPark</b><br>
            Simpan struk ini sebagai bukti transaksi resmi.
        </p>

        <div class="barcode">
            <div style="width: 2px;"></div><div style="width: 4px;"></div><div style="width: 1px;"></div>
            <div style="width: 3px;"></div><div style="width: 2px;"></div><div style="width: 5px;"></div>
            <div style="width: 1px;"></div><div style="width: 2px;"></div><div style="width: 4px;"></div>
            <div style="width: 2px;"></div><div style="width: 3px;"></div><div style="width: 1px;"></div>
        </div>
    </div>

    <div class="actions">
        <button class="btn-print" onclick="window.print()">
            Cetak Struk lannPark
        </button>
        <a href="/petugas/dashboard" class="btn-back">
            Kembali ke Dashboard
        </a>
    </div>

</body>
</html>