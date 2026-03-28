<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaParkir extends Model
{
    protected $table = 'tb_area_parkir'; // Sesuai database kita
    protected $fillable = ['nama_area', 'kapasitas', 'terisi'];
}