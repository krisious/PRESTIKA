<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Siswa extends Model
{
    use SoftDeletes, HasRoles;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    public function prestasi()
    {
        return $this->hasMany(Prestasi::class, 'id_siswa', 'id');
    }

    protected static function booted()
    {
        static::forceDeleted(function ($siswa) {
            // Hanya soft delete user saat force delete siswa
            $siswa->user?->delete();
        });

        // Jika kamu ingin restore user saat siswa di-restore
        static::restored(function ($siswa) {
            $siswa->user?->restore();
        });
    }

    public function prestasis()
    {
        return $this->belongsToMany(Prestasi::class, 'prestasi_siswa', 'siswa_id', 'prestasi_id');
    }

    public function prestasiAnggota()
    {
        return $this->belongsToMany(Prestasi::class, 'anggota_prestasi', 'siswa_id', 'prestasi_id');
    }
}
