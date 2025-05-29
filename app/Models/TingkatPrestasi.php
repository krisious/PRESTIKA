<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TingkatPrestasi extends Model
{
    protected $guarded = [];

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'id_tingkat_prestasi', 'id');
    }
}
