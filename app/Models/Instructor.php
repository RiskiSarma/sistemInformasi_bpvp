<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'expertise',
        'education',
        'experience_years',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'experience_years' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

    // === RELASI KE USER ===
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
    /**
     * Get all programs for this instructor (Many to Many)
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'instructor_program');
    }

    /**
     * Get all schedules for this instructor
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Scope to get only active instructors
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive instructors
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get total active programs for this instructor
     */
    public function getActiveProgramsCountAttribute()
    {
        return $this->programs()
            ->where('status', 'ongoing')
            ->count();
    }

    /**
     * Get total participants taught by this instructor
     */
    public function getTotalParticipantsAttribute()
    {
        return $this->programs()
            ->withCount('participants')
            ->get()
            ->sum('participants_count');
    }

    /**
     * Get total teaching hours per week
     */
    public function getWeeklyTeachingHoursAttribute()
    {
        return $this->schedules()
            ->active()
            ->get()
            ->sum(function ($schedule) {
                $start = \Carbon\Carbon::parse($schedule->start_time);
                $end = \Carbon\Carbon::parse($schedule->end_time);
                return $start->diffInHours($end);
            });
    }
}