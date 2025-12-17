<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();

            $table->string('nis')->unique();
            $table->string('nama');

            $table->string('contact')->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();

            // fingerprint
            $table->integer('fingerprint_id')
                  ->nullable()
                  ->unique();

            // status fingerprint
            $table->enum('status', [
                'Belum Terdaftar',
                'Terdaftar'
            ])->default('Belum Terdaftar');

            // saldo
            $table->bigInteger('saldo')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
