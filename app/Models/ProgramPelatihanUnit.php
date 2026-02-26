<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProgramPelatihanUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'program_pelatihan_units';
    
    // ID sudah CHAR(50), bukan UUID
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'program_pelatihan_id', // Relasi ke programs
        'type_unit', // ENUM: 'skkni', 'special', dll (existing)
        'independent_competency_units_id',
        'sub_unit_kompetensi', // ENUM: 'Y', 'N'
        'master_programs_id',
        'jp', // INT existing
        'durasi_jp', // INT baru - untuk durasi yang bisa diedit
        'urutan', // INT baru - untuk urutan tampilan
        'is_editable', // BOOLEAN baru
    ];

    protected $casts = [
        'jp' => 'integer',
        'durasi_jp' => 'integer',
        'urutan' => 'integer',
        'is_editable' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                // Generate UUID atau ID sesuai kebutuhan
                $model->id = (string) Str::uuid();
            }
            
            // Jika durasi_jp tidak diset, gunakan jp
            if (empty($model->durasi_jp) && !empty($model->jp)) {
                $model->durasi_jp = $model->jp;
            }
        });
    }

    // =====================
    // RELASI
    // =====================

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_pelatihan_id');
    }

    public function independentCompetencyUnit()
    {
        return $this->belongsTo(IndependentCompetencyUnit::class, 'independent_competency_units_id');
    }

    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class, 'master_programs_id');
    }

    // =====================
    // ACCESSOR & HELPER
    // =====================

    /**
     * Get nama unit kompetensi
     */
    public function getUnitNameAttribute()
    {
        return $this->independentCompetencyUnit->name ?? '-';
    }

    /**
     * Get kode unit kompetensi
     */
    public function getUnitCodeAttribute()
    {
        return $this->independentCompetencyUnit->code ?? '-';
    }

    /**
     * Get tipe dalam bahasa Indonesia
     */
    public function getTypeUnitLabelAttribute()
    {
        $labels = [
            'skkni' => 'SKKNI',
            'special' => 'Khusus',
            'reguler' => 'Reguler',
            'softskill' => 'Softskill',
            'industri' => 'Industri',
        ];

        return $labels[$this->type_unit] ?? ucfirst($this->type_unit);
    }

    /**
     * Cek apakah unit ini punya sub unit
     */
    public function hasSubUnits()
    {
        return $this->sub_unit_kompetensi === 'Y';
    }
}