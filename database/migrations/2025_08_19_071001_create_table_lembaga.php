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
        Schema::create('lembaga', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan_pendidikan');
            $table->string('npsn')->nullable();
            $table->enum('bentuk_pendidikan', ['SLB', 'SMA', 'SMK'])->nullable(); // Menggunakan tipe data ENUM
            $table->enum('status_sekolah', ['Negeri', 'Swasta'])->nullable();     // Menggunakan tipe data ENUM
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.b
     */
    public function down(): void
    {
        Schema::dropIfExists('lembaga');
    }
};
