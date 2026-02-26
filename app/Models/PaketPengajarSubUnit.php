<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class PaketPelatihanPengajarSubUnit extends Model
{
    use HasUuids;

    protected $table = 'paket_pelatihan_pengajar_sub_units';

    protected $fillable = [
        'pp_unit_id',
        'programs_id',
        'pengajar_eksternal',
        'pengajar_internal_id',
        'pengajar_eksternal_id',
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

    // ✅ FIXED: Relation name sesuai yang dipanggil di controller
    public function paketUnit()
    {
        return $this->belongsTo(PaketPelatihanUnit::class, 'pp_unit_id');
    }

    // ✅ ALIAS untuk backward compatibility (opsional)
    public function paketPelatihanUnit()
    {
        return $this->belongsTo(PaketPelatihanUnit::class, 'pp_unit_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'programs_id');
    }

    public function pengajarInternal()
    {
        return $this->belongsTo(Instructor::class, 'pengajar_internal_id');
    }

    // ✅ FIXED: Nama relation sesuai yang dipanggil di controller
    public function pengajarEksternalData()
    {
        return $this->belongsTo(PengajarEksternal::class, 'pengajar_eksternal_id');
    }

    // ✅ ALIAS untuk backward compatibility
    public function pengajarEksternal()
    {
        return $this->belongsTo(PengajarEksternal::class, 'pengajar_eksternal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEksternal($query)
    {
        return $query->where('pengajar_eksternal', 'Y');
    }

    public function scopeInternal($query)
    {
        return $query->where('pengajar_eksternal', 'N');
    }
}