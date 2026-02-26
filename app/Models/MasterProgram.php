<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProgram extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'duration_hours',
        // Foreign keys (yang dipakai sekarang)
        'kejuruan_id',
        'bidang_pelatihan_id',
        'versi',
        'tanggal',
        'file_program',
        'is_active',
        'created_by', 
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'is_active' => 'boolean',
        'duration_hours' => 'integer',
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

    // ========== RELASI USER ==========
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ========== RELASI MASTER DATA ==========
    public function kejuruan(): BelongsTo
    {
        return $this->belongsTo(Kejuruan::class, 'kejuruan_id');
    }

    public function bidangPelatihan(): BelongsTo
    {
        return $this->belongsTo(BidangPelatihan::class, 'bidang_pelatihan_id');
    }

    // ========== RELASI PROGRAMS ==========
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'master_program_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'master_program_id');
    }

    // ========== ACCESSOR ==========
    
    // // Ambil semua unit independen dari semua programs di bawah master ini
    // public function getIndependentCompetencyUnitsAttribute()
    // {
    //     return $this->programs->flatMap(function ($program) {
    //         return $program->independentCompetencyUnits;
    //     })->unique('id'); // unique biar tidak duplikat
    // }

    // // Count untuk performa lebih baik
    // public function getIndependentCompetencyUnitsCountAttribute()
    // {
    //     return $this->independent_competency_units->count();
    // }
    public function programPelatihanUnits()
    {
        return $this->hasMany(ProgramPelatihanUnit::class, 'master_program_id');
    }
    public function independentCompetencyUnits()
{
    return $this->belongsToMany(
        IndependentCompetencyUnit::class,
        'program_pelatihan_units',                // nama tabel pivot
        'master_programs_id',                     // FK ke MasterProgram
        'independent_competency_units_id'         // FK ke IndependentCompetencyUnit
    )->withPivot('type_unit', 'jp', 'durasi_jp', 'urutan', 'is_editable')
     ->withTimestamps()
    ->withTrashed();
}
}