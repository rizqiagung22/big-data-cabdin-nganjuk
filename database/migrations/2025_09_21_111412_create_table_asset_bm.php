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
        Schema::create('belanja_modal', function (Blueprint $table) { // belanja modal
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->onDelete('cascade')->on; // jika data di parent di hapus, maka table yang di relesi disini akan ikut dihapus
            $table->string('bos_name_file')->nullable();
            $table->string('bos_path')->nullable();
            $table->string('bpopp_name_file')->nullable();
            $table->string('bpopp_path')->nullable();
            $table->string('bp_name_file')->nullable(); // bantuan pemerintah
            $table->string('bp_path')->nullable(); // bantuan pemerintah
            $table->string('pm_name_file')->nullable(); // partisipasi masyarakat
            $table->string('pm_path')->nullable(); // partisipasi masyarakat
            $table->enum('type', ['1', '2', '3', '4']); // tri wulan 1,2,3,4
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('belanja_modal');
    }
};
