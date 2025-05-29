<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubkategoriPrestasi extends Model
{
    protected $guarded = [];

    public function kategoriPrestasi()
    {
        return $this->belongsTo(KategoriPrestasi::class, 'id_kategori_prestasi', 'id');
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'id_subkategori_prestasi', 'id');
    }
}
