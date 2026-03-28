<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id('id_user'); // Menggunakan id_user sesuai ERD
        $table->string('nama_lengkap', 50);
        $table->string('username', 50)->unique();
        $table->string('password');
        $table->enum('role', ['admin', 'petugas', 'owner']);
        $table->tinyInteger('status_aktif')->default(1); // Kolom ini harus ada di sini
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};  