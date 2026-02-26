<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class JenisMateriPelatihan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jenis_materi_pelatihan';
    
    protected $fillable = [
        'jenis_materi_pelatihan',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan pivot programs
    public function pengajarPrograms()
    {
        return $this->hasMany(PaketPelatihanPengajarProgram::class);
    }
}