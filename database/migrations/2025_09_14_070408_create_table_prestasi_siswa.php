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
        Schema::create('prestasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            $table->enum('jenjang', ['SMA', 'SMK', 'SLB']);
            $table->enum('tingkat', ['Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional']);
            $table->string('name_file')->nullable();
            $table->string('path')->nullable();
            $table->timestamps();

            // Menambahkan unique constraint untuk memastikan yang unik
            $table->unique(['tahun_id', 'jenjang', 'tingkat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswa');
    }
};
