<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestasi extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kategoriPrestasi()
    {
        return $this->belongsTo(KategoriPrestasi::class, 'id_kategori_prestasi', 'id');
    }

    public function subkategoriPrestasi()
    {
        return $this->belongsTo(SubkategoriPrestasi::class, 'id_subkategori_prestasi', 'id');
    }

    public function tingkatPrestasi()
    {
        return $this->belongsTo(TingkatPrestasi::class, 'id_tingkat_prestasi', 'id');
    }
    
    public function peringkatPrestasi()
    {
        return $this->belongsTo(PeringkatPrestasi::class, 'id_peringkat_prestasi', 'id');
    }
    
    public function delegasi()
    {
        return $this->belongsTo(Delegasi::class, 'id_delegasi', 'id');
    }

    public function anggotaTim()
    {
        return $this->belongsToMany(Siswa::class, 'prestasi_siswa', 'prestasi_id', 'siswa_id');
    }

}