<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kejuruan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kejuruan',
        'code',
        // 'deskripsi',
    ];

    public function bidangPelatihans()
    {
        return $this->hasMany(BidangPelatihan::class);
    }

    // Relasi ke master programs (jika ada)
    public function masterPrograms()
    {
        return $this->hasMany(MasterProgram::class);
    }
}