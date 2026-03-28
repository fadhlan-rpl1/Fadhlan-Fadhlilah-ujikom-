<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
    Schema::create('tb_transaksi', function (Blueprint $table) {
        $table->id();
        $table->string('plat_nomor'); // Berubah dari kendaraan_id ke string plat_nomor
        $table->datetime('waktu_masuk');
        $table->datetime('waktu_keluar')->nullable();
        $table->integer('biaya_total')->default(0);
        $table->enum('status', ['masuk', 'keluar'])->default('masuk');
        $table->integer('durasi_jam')->default(0);
        $table->unsignedBigInteger('tarif_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->unsignedBigInteger('area_parkir_id')->nullable();
        $table->timestamps();
        
        $table->foreign('tarif_id')->references('id')->on('tb_tarif')->onDelete('set null');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('area_parkir_id')->references('id')->on('tb_area_parkir')->onDelete('set null');
    });
}

    public function down(): void {
        Schema::dropIfExists('tb_transaksi');
    }
};