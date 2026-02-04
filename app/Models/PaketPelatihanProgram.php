<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketPelatihanProgram extends Model
{
    use SoftDeletes;

    protected $table = 'paket_pelatihan_programs';

    protected $fillable = [
        'paket_pelatihan_id',
        'program_pelatihan_id',
        'ada_industri',
        'jp_harian',
        'tanggal_mulai',
        'tanggal_akhir',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'ada_industri'  => 'string',
    ];

    // Relasi ke paket induk
    public function paketPelatihan()
    {
        return $this->belongsTo(PaketPelatihan::class, 'paket_pelatihan_id');
    }

    // Relasi ke program master
    public function programPelatihan()
    {
        return $this->belongsTo(ProgramPelatihan::class, 'program_pelatihan_id');
    }

    // Relasi ke user yang menambahkan
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke peserta (siswa) yang terdaftar di kombinasi ini
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'paket_pelatihan_program_id');
    }
}