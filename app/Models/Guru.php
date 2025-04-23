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
}
