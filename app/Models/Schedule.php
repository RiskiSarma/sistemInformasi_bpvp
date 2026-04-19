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
        'pengajar_eksternal_id',  // ← TAMBAHAN BARU
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

    /**
     * ✅ RELASI BARU: Pengajar Eksternal
     */
    public function pengajarEksternal(): BelongsTo
    {
        return $this->belongsTo(PengajarEksternal::class, 'pengajar_eksternal_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

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

    /**
     * ✅ ACCESSOR BARU: Get nama pengajar (instructor atau eksternal)
     */
    public function getPengajarNameAttribute(): string
    {
        if ($this->instructor_id) {
            return $this->instructor->name ?? 'Instructor';
        }
        if ($this->pengajar_eksternal_id) {
            return $this->pengajarEksternal->nama ?? 'Pengajar Eksternal';
        }
        return '-';
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