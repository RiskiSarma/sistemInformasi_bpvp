<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'email',
        'pendidikan_terakhir',
        'program_id',
        'foto',
        'status'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function pelatihan()
    {
        return $this->belongsToMany(Pelatihan::class, 'pelatihan_siswa');
    }

    public function sertifikat()
    {
        return $this->hasMany(Sertifikat::class);
    }

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }
}