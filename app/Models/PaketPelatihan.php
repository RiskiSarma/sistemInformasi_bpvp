<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketPelatihan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'jenis_pelatihan_id',
        'tahun',
        'batch',
        'jp_harian',
        'jp_industri',
        'sabtu_masuk',
        'minggu_masuk',
        'tanggal_mulai',
        'tanggal_akhir',
        'tanggal_awal_pendaftaran',
        'tanggal_akhir_pendaftaran',
        'tanggal_awal_tes_tulis',
        'tanggal_akhir_tes_tulis',
        'tanggal_awal_wawancara',
        'tanggal_akhir_wawancara',
        'tanggal_awal_daftar_ulang',
        'tanggal_akhir_daftar_ulang',
        'tanggal_pengumuman',
        'user_id_pengumuman',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime:Y-m-d',
        'tanggal_akhir' => 'datetime:Y-m-d',
        'tanggal_awal_pendaftaran' => 'datetime:Y-m-d',
        'tanggal_akhir_pendaftaran' => 'datetime:Y-m-d',
        'tanggal_awal_tes_tulis' => 'datetime:Y-m-d',
        'tanggal_akhir_tes_tulis' => 'datetime:Y-m-d',
        'tanggal_awal_wawancara' => 'datetime:Y-m-d',
        'tanggal_akhir_wawancara' => 'datetime:Y-m-d',
        'tanggal_awal_daftar_ulang' => 'datetime:Y-m-d',
        'tanggal_akhir_daftar_ulang' => 'datetime:Y-m-d',
        'tanggal_pengumuman' => 'datetime:Y-m-d',
    ];

    public function jenisPelatihan()
    {
        return $this->belongsTo(JenisPelatihan::class, 'jenis_pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userPengumuman()
    {
        return $this->belongsTo(User::class, 'user_id_pengumuman');
    }

    public function programs()
    {
        return $this->hasManyThrough(Program::class, PaketPelatihanProgram::class);
    }
}