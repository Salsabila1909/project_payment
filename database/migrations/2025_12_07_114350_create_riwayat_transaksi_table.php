<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_transaksi', function (Blueprint $table) {
            $table->id();

            // Relasi ke siswa
            $table->foreignId('siswa_id')
                  ->constrained('siswa')
                  ->onDelete('cascade');

            // Tipe transaksi
            $table->enum('tipe', ['topup','payment']);

            // Nominal
            $table->bigInteger('nominal');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_transaksi');
    }
};
