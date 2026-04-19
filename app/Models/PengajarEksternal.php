<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class PengajarEksternal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengajar_eksternal';
    
    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'nip',
        'instansi',
        'jabatan',
        'alamat',
        'telepon',
        'email',
        'pendidikan_id',
        'kejuruan_pendidikan',
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

    // ✅ Relasi
    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Many-to-Many dengan Program via pivot
    public function programs()
    {
        return $this->belongsToMany(
            Program::class,
            'paket_pelatihan_pengajar_programs',
            'pengajar_eksternal_id',
            'programs_id'
        )->withPivot([
            'jenis_materi_pelatihan_id',
            'pengajar_internal_id',
            'pengajar_eksternal'
        ]);
    }

    // Relasi dengan sub units via pivot
    public function subUnits()
    {
        return $this->belongsToMany(
            PaketPelatihanUnit::class,
            'paket_pelatihan_pengajar_sub_units',
            'pengajar_eksternal_id',
            'paket_pelatihan_unit_id'
        )->withPivot([
            'programs_id',
            'pengajar_internal_id',
            'pengajar_eksternal'
        ]);
    }

    // Relasi ke program assignments
    public function programAssignments()
    {
        return $this->hasMany(PaketPelatihanPengajarProgram::class, 'pengajar_eksternal_id')
                    ->where('pengajar_eksternal', 'Y');
    }

    // Relasi ke sub unit assignments
    public function subUnitAssignments()
    {
        return $this->hasMany(PaketPelatihanPengajarSubUnit::class, 'pengajar_eksternal_id')
                    ->where('pengajar_eksternal', 'Y');
    }
        /**
     * Relasi ke jadwal mengajar (Schedule)
     */
    /**
     * Relasi ke Schedule (Jadwal Mengajar)
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'pengajar_eksternal_id');
    }
    
    /**
     * Relasi ke InstructorAttendance (Absensi Mengajar)
     */
    public function attendances()
    {
        return $this->hasMany(InstructorAttendance::class, 'pengajar_eksternal_id');
    }
    
    /**
     * Check if pengajar eksternal has user account
     */
    public function hasUser(): bool
    {
        return !is_null($this->user_id);
    }
}