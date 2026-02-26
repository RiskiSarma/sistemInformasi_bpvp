<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pendidikan extends Model
{
    use SoftDeletes;

    protected $table = 'pendidikans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['pendidikan'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
    // Relasi ke participants (jika ada)
    public function participants()
    {
        return $this->hasMany(Participant::class, 'pendidikan_id', 'id');
    }

    // Relasi ke instructors (jika ada)
    public function instructors()
    {
        return $this->hasMany(Instructor::class, 'pendidikan_id', 'id');
    }

    // Scope untuk ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('pendidikan', 'asc');
    }
    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }
}