<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class InstructorAttendance extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';
    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
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

    protected $fillable = [
        'instructor_id',
        'schedule_id',
        'program_id',
        'pengajar_eksternal_id', 
        'instructor_type',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        'location',
        'duration',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        // date di-cast ke Carbon date object — aman untuk format()
        'date'       => 'date',
        // check_in & check_out TIDAK di-cast ke datetime
        // dibiarkan string "H:i:s" agar Carbon::parse() di controller bisa terima
        'duration'   => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('date', now()->year)
                     ->whereMonth('date', now()->month);
    }

    // ── Accessors ───────────────────────────────────────────

    /**
     * Status badge CSS classes.
     * Nilai status yang valid: present, late, absent, excused, sick
     */
    public function getStatusBadgeAttribute(): string
    {
        return [
            'present' => 'bg-green-100 text-green-800',
            'late'    => 'bg-yellow-100 text-yellow-800',
            'absent'  => 'bg-red-100 text-red-800',
            'excused' => 'bg-blue-100 text-blue-800',
            'sick'    => 'bg-purple-100 text-purple-800',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Status label bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return [
            'present' => 'Hadir',
            'late'    => 'Terlambat',
            'absent'  => 'Tidak Hadir',
            'excused' => 'Izin',
            'sick'    => 'Sakit',
        ][$this->status] ?? 'Unknown';
    }

    /**
     * HAPUS getDurationAttribute() agar tidak override kolom 'duration' di DB.
     * Durasi disimpan langsung ke kolom integer 'duration' (dalam menit)
     * oleh controller saat clock-out.
     */
}