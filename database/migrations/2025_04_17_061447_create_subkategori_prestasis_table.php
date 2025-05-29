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
        Schema::create('subkategori_prestasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kategori_prestasi')->constrained('kategori_prestasis')->onDelete('cascade');
            $table->string('subkategori');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subkategori_prestasis');
    }
};