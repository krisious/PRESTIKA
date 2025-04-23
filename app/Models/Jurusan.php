<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $guarded = [];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_jurusan', 'id');
    }
}
