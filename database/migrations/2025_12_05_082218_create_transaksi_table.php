<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');

            $table->date('tanggal'); // tgl-bln-thn
            $table->enum('tipe', ['topup', 'pembelian']);
            $table->bigInteger('jumlah');
            $table->string('keterangan')->nullable();

            // pending = fingerprint belum Scan
            // sukses = fingerprint cocok
            $table->enum('status', ['pending', 'sukses'])->default('pending');

            $table->timestamps();

            $table->foreign('siswa_id')
                ->references('id')
                ->on('siswa')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
