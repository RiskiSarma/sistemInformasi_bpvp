<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'master_program_id',
        'batch',
        'angkatan',
        'independent_competency_unit_id',
        'start_date',
        'end_date',
        'status',
        'max_participants',
        'created_by',
        'updated_by',
        'ada_industri',
        'jp_harian',
        'jp',
        'paket_pelatihan_id',
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

    public function paketPelatihan()
    {
        return $this->belongsTo(PaketPelatihan::class, 'paket_pelatihan_id');
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
    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id'); // ← foreign key 'batch_id'
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
    // app/Models/Program.php
    public function getDisplayNameAttribute()
    {
        $master = $this->masterProgram->name ?? 'N/A';
        $batchName = $this->batch->name ?? $this->batch->code ?? 'Batch Tidak Diketahui';
        $angkatan = $this->angkatan ? " ({$this->angkatan})" : '';

        return "{$master} - {$batchName}{$angkatan}";
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
    // public function independentCompetencyUnit()
    // {
    //     return $this->belongsTo(IndependentCompetencyUnit::class, 'independent_competency_unit_id');
    // }
    public function independentCompetencyUnits()
    {
        return $this->belongsToMany(
            IndependentCompetencyUnit::class,
            'independent_competency_unit_program',
            'program_id',
            'independent_competency_unit_id'
        )->withTimestamps();
    }
    public function getAdaIndustriLabelAttribute()
    {
        return $this->ada_industri === 'Y' ? 'Ya' : 'Tidak';
    }
    public function getJpHarianLabelAttribute()
    {
        return $this->jp_harian ? $this->jp_harian . ' jam/hari' : '-';
    }

    public function getJpLabelAttribute()
    {
        return $this->jp ? $this->jp . ' jam total' : '-';
    }
    
    // =====================
    // RELASI PIVOT UNITS & SUB UNITS
    // =====================

    /**
     * Relasi ke Paket Pelatihan Units
     * Program -> PaketPelatihanUnit
     */
    public function paketPelatihanUnits()
    {
        return $this->hasMany(PaketPelatihanUnit::class, 'programs_id', 'id');
    }
    public function units()
{
    return $this->belongsToMany(
        IndependentCompetencyUnit::class, // atau CompetencyUnit tergantung model unit kamu
        'paket_pelatihan_units',
        'programs_id',
        'program_pelatihan_unit_id' // atau nama kolom yang sesuai di pivot
    )->withPivot('jp', 'sub_unit_kompetensi', 'master_program_sub_unit_id');
}
    public function getTotalJpAttribute()
    {
        return $this->paketPelatihanUnits()->sum('jp') ?? 0;
    }

    /**
     * Relasi ke Paket Pelatihan Sub Units (nested via units)
     * Program -> PaketPelatihanUnit -> PaketPelatihanSubUnit
     */
    public function paketPelatihanSubUnits()
    {
        return $this->hasManyThrough(
            PaketPelatihanSubUnit::class,
            PaketPelatihanUnit::class,
            'paket_pelatihan_programs_id',
            'paket_pelatihan_unit_id',
            'id',
            'id'
        );
    }
}
