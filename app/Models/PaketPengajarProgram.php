<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class PaketPengajarProgram extends Model
{
    use HasUuids;

    protected $table = 'paket_pelatihan_pengajar_programs';

    protected $fillable = [
        'jenis_materi_pelatihan_id',
        'pengajar_eksternal',
        'pengajar_internal_id',
        'pengajar_eksternal_id',
        'programs_id',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check() && !$model->user_id) {
                $model->user_id = Auth::id();
            }
        });
    }

    // ✅ Relasi
    public function jenisMateri()
    {
        return $this->belongsTo(JenisMateriPelatihan::class, 'jenis_materi_pelatihan_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'programs_id');
    }

    public function pengajarInternal()
    {
        return $this->belongsTo(Instructor::class, 'pengajar_internal_id');
    }

    public function pengajarEksternalData()
    {
        return $this->belongsTo(PengajarEksternal::class, 'pengajar_eksternal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope helper
    public function scopeEksternal($query)
    {
        return $query->where('pengajar_eksternal', 'Y');
    }

    public function scopeInternal($query)
    {
        return $query->where('pengajar_eksternal', 'N');
    }
}