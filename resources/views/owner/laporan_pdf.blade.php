<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">LAPORAN PENDAPATAN lannpark</h2>
    <p>Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Plat Nomor</th>
                <th>Jenis</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $index => $tr)
            @php
                $masuk = \Carbon\Carbon::parse($tr->created_at);
                $keluar = \Carbon\Carbon::parse($tr->updated_at);
                $durasi = max(1, ceil($masuk->diffInHours($keluar)));
                $biaya = $durasi * ($tr->tarif->tarif_per_jam ?? 0);
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $tr->kendaraan->plat_nomor ?? $tr->plat_nomor }}</td>
                <td>{{ $tr->tarif->jenis_kendaraan ?? '-' }}</td>
                <td>{{ $masuk->format('H:i') }}</td>
                <td>{{ $keluar->format('H:i') }}</td>
                <td class="text-right">Rp {{ number_format($biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL KESELURUHAN</th>
                <th class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>