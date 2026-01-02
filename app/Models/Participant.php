<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAudit;
use Illuminate\Support\Facades\Auth;

class Participant extends Model
{
    use HasFactory, HasAudit;

    protected $fillable = [
        'user_id',
        'program_id',
        'nik',
        'phone',
        'address',
        'education',
        'status',
        'batch',
        'enrollment_date',
        'completion_date',
        'created_by',
        'updated_by',
        'birth_place',
        'birth_date',
    ];

    protected $casts = [
        'enrollment_date'   => 'date',
        'completion_date'   => 'date',
        'birth_date'        => 'date',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Relationship to Attendances
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getAttendancePercentage()
    {
        // Ambil semua record attendance peserta ini (sudah di-load via eager loading atau fresh)
        $attendances = $this->attendances;

        // Total pertemuan = jumlah record absensi yang sudah diinput
        $totalMeetings = $attendances->count();

        if ($totalMeetings <= 0) {
            return '0.00';
        }

        // Hitung jumlah 'present' (sesuai scopePresent di model Attendance)
        $attended = $attendances->where('status', 'present')->count();

        // Persentase
        $percentage = ($attended / $totalMeetings) * 100;

        return number_format($percentage, 2);
    }
    /**
     * Relationship to Certificates
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
    /**
     * Accessor untuk mendapatkan nama dari user
     */
    public function getNameAttribute()
    {
        return $this->user->name ?? null;
    }

    /**
     * Accessor untuk mendapatkan email dari user
     */
    public function getEmailAttribute()
    {
        return $this->user->email ?? null;
    }
    /**
 * Relasi ke user yang membuat data peserta
 */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang terakhir mengubah data peserta
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function isEligibleForCertificate(): bool
    {
        // Harus status 'graduated'
        if ($this->status !== 'graduated') {
            return false;
        }

        // Harus belum punya sertifikat
        if ($this->certificate !== null) {
            return false;
        }

        // Kehadiran minimal 75%
        $percentage = (float) $this->getAttendancePercentage();
        if ($percentage < 75.00) {
            return false;
        }

        return true;
    }
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age . ' tahun' : null;
    }
}