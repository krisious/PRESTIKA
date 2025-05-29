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
        Schema::table('kategori_prestasis', function (Blueprint $table) {
            $table->renameColumn('kategori_prestasi', 'kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_prestasis', function (Blueprint $table) {
            $table->renameColumn('kategori', 'kategori_prestasi');
        });
    }
};
