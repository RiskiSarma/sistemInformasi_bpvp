<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_program_id',
        'batch',
        'start_date',
        'end_date',
        'status',
        'max_participants',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    // Relasi ke Master Program
    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class, 'master_program_id');
    }

    /**
     * Relasi ke instructor
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Relasi ke Participants
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    // Relasi ke Attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Accessor untuk mendapatkan nama program dari master_programs
    public function getNameAttribute()
    {
        // Cek apakah relasi sudah di-load
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->name : '-';
    }

    // Accessor untuk mendapatkan deskripsi dari master_programs
    public function getDescriptionAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->description : '-';
    }

    // Accessor untuk mendapatkan durasi dari master_programs
    public function getDurationAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->duration_hours : 0;
    }

    // Accessor untuk mendapatkan kode dari master_programs
    public function getCodeAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->code : '-';
    }
}