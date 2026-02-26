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
        'paket_pelatihan_id',
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
        'instructor_id',
        'batch_id',
        'user_id',
        'selected_units_config', // JSON field untuk simpan units yang dipilih
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'selected_units_config' => 'array', // Auto decode JSON
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
            
            // // Auto generate angkatan berdasarkan master_program_id dan jenis_pelatihan dari paket
            // if (empty($model->angkatan) && $model->master_program_id && $model->paket_pelatihan_id) {
            //     $paket = \App\Models\PaketPelatihan::with('jenisPelatihan')->find($model->paket_pelatihan_id);
            //     $jenisPelatihan = $paket->jenisPelatihan->jenis_pelatihan ?? $paket->jenis_pelatihan ?? null;
                
            //     if ($jenisPelatihan) {
            //         $model->angkatan = self::generateAngkatan(
            //             $model->master_program_id,
            //             $jenisPelatihan
            //         );
            //     }
            // }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Generate angkatan otomatis berdasarkan jenis pelatihan (dari paket) dan master program
     */
    public static function generateAngkatan($masterProgramId, $jenisPelatihan)
    {
        $count = self::where('master_program_id', $masterProgramId)
            ->whereHas('paketPelatihan', function($q) use ($jenisPelatihan) {
                $q->where('jenis_pelatihan', $jenisPelatihan);
            })
            ->count();
        
        $angkatanNumber = $count + 1;
        
        // Convert ke Romawi
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X',
                   'XI', 'XII', 'XIII', 'XIV', 'XV', 'XVI', 'XVII', 'XVIII', 'XIX', 'XX'];
        
        return $romawi[$angkatanNumber] ?? (string)$angkatanNumber;
    }

    // =====================
    // RELASI
    // =====================

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class, 'master_program_id');
    }

    public function paketPelatihan()
    {
        return $this->belongsTo(PaketPelatihan::class, 'paket_pelatihan_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    /**
     * Relasi ke instruktur program (many-to-many via program_instructors)
     */
    public function programInstructors()
    {
        return $this->hasMany(ProgramInstructor::class, 'program_id');
    }

    public function instructors()
    {
        return $this->belongsToMany(
            Instructor::class,
            'program_instructors',
            'program_id',
            'instructor_id'
        )->withPivot('is_penanggung_jawab', 'keterangan')
          ->withTimestamps();
    }

    // =====================
    // ACCESSOR & HELPER
    // =====================

    /**
     * Get instruktur penanggung jawab
     */
    public function getPenanggungJawabAttribute()
    {
        return $this->instructors()
            ->wherePivot('is_penanggung_jawab', true)
            ->first();
    }

    /**
     * Get jenis pelatihan dari paket
     */
    public function getJenisPelatihanAttribute()
    {
        if (!$this->paketPelatihan) {
            return '-';
        }
        
        // Prioritas: relasi jenisPelatihan > field jenis_pelatihan
        return $this->paketPelatihan->jenisPelatihan->jenis_pelatihan 
            ?? $this->paketPelatihan->jenis_pelatihan 
            ?? '-';
    }

    /**
     * Get selected units dengan detail dari master program
     */
    public function getSelectedUnitsWithDetailsAttribute()
    {
        if (!$this->selected_units_config) {
            return collect([]);
        }

        $unitIds = collect($this->selected_units_config)->pluck('unit_id')->toArray();
        $units = \App\Models\IndependentCompetencyUnit::whereIn('id', $unitIds)->get();

        return collect($this->selected_units_config)->map(function ($config) use ($units) {
            $unit = $units->firstWhere('id', $config['unit_id']);
            return [
                'unit' => $unit,
                'custom_duration' => $config['custom_duration'] ?? 0,
                'type' => $config['type'] ?? 'reguler',
            ];
        });
    }

    /**
     * Get total JP dari units yang dipilih
     */
    public function getTotalJpFromSelectedUnitsAttribute()
    {
        if (!$this->selected_units_config) {
            return 0;
        }

        return collect($this->selected_units_config)->sum('custom_duration');
    }

    public function getNameAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->name : '-';
    }

    public function getDisplayNameAttribute()
    {
        $master = $this->masterProgram->name ?? 'N/A';
        $jenis = $this->jenis_pelatihan ? " ({$this->jenis_pelatihan})" : '';
        $angkatan = $this->angkatan ? " - Angkatan {$this->angkatan}" : '';

        return "{$master}{$jenis}{$angkatan}";
    }

    public function getDescriptionAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->description : '-';
    }

    public function getDurationAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->duration_hours : 0;
    }

    public function getCodeAttribute()
    {
        if (!$this->relationLoaded('masterProgram')) {
            $this->load('masterProgram');
        }
        
        return $this->masterProgram ? $this->masterProgram->code : '-';
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

    // Backward compatibility - relasi lama
    public function independentCompetencyUnits()
    {
        return $this->belongsToMany(
            IndependentCompetencyUnit::class,
            'independent_competency_unit_program',
            'program_id',
            'independent_competency_unit_id'
        )->withTimestamps();
    }

    public function pengajarAssignments()
    {
        return $this->hasMany(
            \App\Models\PaketPelatihanPengajarProgram::class, 
            'programs_id'
        );
    }

    // Jika model Instructor dan PengajarEksternal punya nama berbeda, sesuaikan juga:
    public function pengajarInternal()
    {
        return $this->hasManyThrough(
            Instructor::class,
            PaketPelatihanPengajarProgram::class,
            'programs_id',
            'id',
            'id',
            'pengajar_internal_id'
        );
    }

    public function pengajarEksternal()
    {
        return $this->hasManyThrough(
            PengajarEksternal::class,
            PaketPelatihanPengajarProgram::class,
            'programs_id',
            'id',
            'id',
            'pengajar_eksternal_id'
        );
    }
}