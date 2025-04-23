<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Delegasi extends Model
{
    protected $guarded = [];

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'id_prestasi', 'id');
    }
}
