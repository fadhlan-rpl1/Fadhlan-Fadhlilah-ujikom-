<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
    Schema::create('tb_transaksi', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('kendaraan_id'); // Tanpa ->constrained()
        $table->unsignedBigInteger('area_parkir_id');
        $table->unsignedBigInteger('user_id');
        $table->string('kode_transaksi');
        $table->datetime('waktu_masuk');
        $table->datetime('waktu_keluar')->nullable();
        $table->integer('total_biaya')->default(0);
        $table->enum('status', ['masuk', 'keluar'])->default('masuk');
        $table->timestamps();
    });
}

    public function down(): void {
        Schema::dropIfExists('tb_transaksi');
    }
};