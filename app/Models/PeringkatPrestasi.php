<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeringkatPrestasi extends Model
{
    protected $guarded = [];

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'id_prestasi', 'id');
    }
}
