<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BidangPelatihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'kejuruan_id',
        'bidang_pelatihan',
        // 'deskripsi',
    ];

    public function kejuruan()
    {
        return $this->belongsTo(Kejuruan::class);
    }

    // Relasi ke master programs (jika ada)
    public function masterPrograms()
    {
        return $this->hasMany(MasterProgram::class);
    }
}