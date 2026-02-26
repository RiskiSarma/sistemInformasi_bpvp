<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgramInstructor extends Model
{
    use HasFactory;

    protected $table = 'program_instructors';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'program_id',
        'instructor_id',
        'is_penanggung_jawab',
        'keterangan',
    ];

    protected $casts = [
        'is_penanggung_jawab' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}