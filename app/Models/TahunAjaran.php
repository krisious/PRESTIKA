<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $guarded = [];

    public function prestasis(): HasMany
    {
        return $this->hasMany(Prestasi::class, 'id_tahun_ajaran');
    }
}
