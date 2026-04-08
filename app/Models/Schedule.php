<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'instructor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // start_time & end_time TIDAK di-cast ke datetime
        // dibiarkan string "H:i:s" agar Carbon::parse() di controller/view tidak error
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

    // ── Relasi ──────────────────────────────────────────────

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke InstructorAttendance untuk hari ini.
     * JANGAN pakai Auth::user() di sini — relasi dipanggil saat eager load,
     * instructor_id difilter lewat eager load constraint di controller.
     */
    public function attendance()
    {
        return $this->hasOne(InstructorAttendance::class, 'schedule_id');
    }

    // ── Accessors ───────────────────────────────────────────

    public function getDayNameAttribute(): string
    {
        return [
            'monday'    => 'Senin',
            'tuesday'   => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday'  => 'Kamis',
            'friday'    => 'Jumat',
            'saturday'  => 'Sabtu',
            'sunday'    => 'Minggu',
        ][$this->day_of_week] ?? $this->day_of_week;
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    /**
     * Filter jadwal berdasarkan hari ini (day_of_week).
     * Nama hari pakai format lowercase English: monday, tuesday, dst.
     */
    public function scopeToday($query)
    {
        return $query->where('day_of_week', strtolower(now()->englishDayOfWeek));
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
                     ->orderBy('start_time');
    }
}