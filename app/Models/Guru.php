<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class Guru extends Model
{
    use SoftDeletes, HasRoles;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    protected static function booted()
    {
        static::forceDeleted(function ($guru) {
            // Hanya soft delete user saat force delete siswa
            $guru->user?->delete();
        });

        // Jika kamu ingin restore user saat siswa di-restore
        static::restored(function ($guru) {
            $guru->user?->restore();
        });
    }
}
