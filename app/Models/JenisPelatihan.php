<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPelatihan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'jenis_pelatihan',
        'user_id',
        'is_active',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}