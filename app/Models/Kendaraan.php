<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'tb_kendaraan';
    protected $fillable = ['plat_nomor', 'jenis_kendaraan', 'user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}