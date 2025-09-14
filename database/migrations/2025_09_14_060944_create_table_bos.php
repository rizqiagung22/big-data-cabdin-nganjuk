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
        Schema::create('bos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            $table->foreignId('lembaga_id')->constrained('lembaga')->onDelete('cascade'); // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            $table->enum('jenis_laporan', ['pagu', 'rkas', 'usulan per bulan', 'realisasi', 'penyerapan tiap bulan']);
            $table->string('name_file')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();

            // Menambahkan unique constraint untuk memastikan kombinasi tahun, lembaga dan jenis laporan yang unik
            $table->unique(['tahun_id', 'lembaga_id', 'jenis_laporan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bos');
    }
};
