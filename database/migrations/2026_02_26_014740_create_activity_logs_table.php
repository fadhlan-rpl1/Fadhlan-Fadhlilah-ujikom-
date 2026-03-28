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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        // Manual foreign key agar lebih akurat
        $table->unsignedBigInteger('user_id'); 
        $table->string('activity');
        $table->text('description');
        $table->timestamps();

        // Menyambungkan ke id_user di tabel users
        $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
