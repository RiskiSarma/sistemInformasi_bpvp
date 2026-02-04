<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Skkni extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'skkni',
        'nomor',
        'tanggal',
        'berlaku',
        'file_path',
        'file_name',
        'file_type',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function independentCompetencyUnits()
    {
        return $this->hasMany(IndependentCompetencyUnit::class, 'skkni_id');
    }
    public function independentUnits()
    {
        return $this->hasMany(IndependentCompetencyUnit::class, 'skkni_id'); // sesuaikan foreign key
        // atau belongsToMany kalau pivot table
    }
}