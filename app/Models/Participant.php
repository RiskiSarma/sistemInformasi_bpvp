<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAudit;

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
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'completion_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        $model->created_by = auth()->id();
        $model->updated_by = auth()->id();
    });

    static::updating(function ($model) {
        $model->updated_by = auth()->id();
    });
}
    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Program
     */
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
        // Ambil total pertemuan dari program (asumsi ada kolom total_meetings di tabel programs)
        $totalMeetings = $this->program?->total_meetings ?? 0;

        // Jika program tidak punya total_meetings, atau 0, kembalikan 0
        if ($totalMeetings <= 0) {
            return '0.00';
        }

        // Hitung jumlah kehadiran (status 'present')
        $attended = $this->attendances()
            ->where('status', 'present')
            ->count();

        // Hitung persentase
        $percentage = ($attended / $totalMeetings) * 100;

        // Return dengan 2 desimal
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
}