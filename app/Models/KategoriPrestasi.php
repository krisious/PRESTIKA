<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPrestasi extends Model
{
    protected $guarded = [];

    public function subkategoriPrestasi()
    {
        return $this->hasMany(SubkategoriPrestasi::class, 'id_kategori_prestasi', 'id');
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'id_kategori_prestasi', 'id');
    }
}
