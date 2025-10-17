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
        Schema::create('asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            // tri wulan 1
            $table->foreignId('bm_id_1')->constrained('belanja_modal')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            // tri wulan 2
            $table->foreignId('bm_id_2')->constrained('belanja_modal')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            // tri wulan 3
            $table->foreignId('bm_id_3')->constrained('belanja_modal')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            // tri wulan 4
            $table->foreignId('bm_id_4')->constrained('belanja_modal')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus

            $table->string('scan_name_file')->nullable(); //
            $table->string('scan_path')->nullable(); //
            $table->string('sertifikat_name_file')->nullable(); //
            $table->string('sertifikat_path')->nullable(); //

            $table->timestamps();
            // Menambahkan unique constraint untuk memastikan yang unik
            $table->unique(['tahun_id', 'bm_id_1', 'bm_id_2', 'bm_id_3', 'bm_id_4']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
