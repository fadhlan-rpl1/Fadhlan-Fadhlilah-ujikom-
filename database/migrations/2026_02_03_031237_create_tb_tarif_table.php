<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up() {
    Schema::create('tb_tarif', function (Blueprint $table) {
        $table->id();
        $table->string('jenis_kendaraan'); // Motor, Mobil, dll
        $table->integer('tarif_per_jam');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tarif');
    }
};
