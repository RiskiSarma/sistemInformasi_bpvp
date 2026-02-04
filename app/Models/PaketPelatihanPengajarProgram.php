<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaketPelatihanPengajarProgram extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'paket_pelatihan_program_id',
        'jenis_materi_pelatihan_id',
        'pengajar_eksternal',
        'pengajar_internal_id',
        'pengajar_eksternal_id',
        'user_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function paketPelatihanProgram()
    {
        return $this->belongsTo(PaketPelatihanProgram::class);
    }

    public function jenisMateriPelatihan()
    {
        return $this->belongsTo(JenisMateriPelatihan::class);
    }

    public function pengajarInternal()
    {
        return $this->belongsTo(Instructor::class, 'pengajar_internal_id');
    }

    public function pengajarEksternal()
    {
        return $this->belongsTo(PengajarEksternal::class, 'pengajar_eksternal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}