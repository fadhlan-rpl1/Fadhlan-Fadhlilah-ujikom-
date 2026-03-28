<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'activity', 'description'];

    // Relasi: Setiap log dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user'); 
        // Sesuaikan 'id_user' dengan nama primary key di tabel users kamu
    }
}