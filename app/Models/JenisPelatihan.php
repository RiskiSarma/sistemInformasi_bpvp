<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JenisPelatihan extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    // KEMBALIKAN KE DEFAULT karena primary key adalah BIGINT auto-increment
    // Hapus atau komen baris ini kalau mau pakai auto-increment
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = [
        'jenis_pelatihan',
        'user_id',
        'is_active',
    ];

    /**
     * Otomatis generate UUID untuk kolom 'uuid' (bukan 'id')
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function paketPelatihans()
    {
        return $this->hasMany(PaketPelatihan::class, 'jenis_pelatihan_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}