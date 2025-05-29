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
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('id_kategori_prestasi')->constrained('kategori_prestasis')->onDelete('cascade');
            $table->foreignId('id_subkategori_prestasi')->constrained('subkategori_prestasis')->onDelete('cascade');
            $table->foreignId('id_tingkat_prestasi')->constrained('tingkat_prestasis')->onDelete('cascade');
            $table->foreignId('id_peringkat_prestasi')->constrained('peringkat_prestasis')->onDelete('cascade');
            $table->foreignId('id_delegasi')->constrained('delegasis')->onDelete('cascade');
            $table->string('nama_lomba');
            $table->date('tanggal_perolehan');
            $table->string('penyelenggara');
            $table->string('lokasi');
            $table->string('bukti_prestasi')->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};