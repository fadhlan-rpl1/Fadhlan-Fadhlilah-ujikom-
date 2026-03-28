<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    // Pastikan nama tabelnya sesuai
    protected $table = 'tb_transaksi'; 
    
    // Guarded kosong agar semua field bisa diisi
    protected $guarded = []; 

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
    
    /**
     * Relasi ke tabel Users (Petugas)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}