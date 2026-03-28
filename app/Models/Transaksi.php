<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    // Pastikan nama tabelnya sesuai
    protected $table = 'tb_transaksi'; 
    
    // Guarded kosong agar semua field bisa diisi (termasuk tarif_id dan kendaraan_id)
    protected $guarded = []; 

    /**
     * INI SOLUSI ERRORNYA: Relasi ke tabel Kendaraan
     */
    public function kendaraan()
    {
        // Menyambungkan Transaksi dengan Kendaraan berdasarkan kendaraan_id
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    /**
     * Relasi ke tabel Tarif
     */
    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'tarif_id');
    }

    /**
     * Relasi ke tabel Area Parkir
     */
    public function area()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id');
    }
}