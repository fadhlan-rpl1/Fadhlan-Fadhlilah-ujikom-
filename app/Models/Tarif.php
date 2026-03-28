<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    use HasFactory;

    protected $table = 'tb_tarif'; // Pastikan nama tabel sesuai di database
    protected $primaryKey = 'id'; // Sesuaikan jika primary key kamu bukan 'id'
    
    protected $fillable = [
        'jenis_kendaraan',
        'tarif_per_jam'
    ];
}