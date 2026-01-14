<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'master_programs',
            'independent_competency_units',
            'batches',
            'competency_units',
            'programs',
            'participants',
            'users',
            'instructors',
            'attendances',
            'certificates',
            'schedules',
            'independent_competency_unit_program',
            // tambahkan nama table lain yang ingin soft delete
        ];

        foreach ($tables as $tableName) {
            // Cek apakah table ada dan kolom deleted_at belum ada
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes(); // tambah deleted_at timestamp nullable
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'master_programs',
            'independent_competency_units',
            'batches',
            'competency_units',
            'programs',
            'participants',
            'users',
            'instructors',
            'attendances',
            'certificates',
            'schedules',
            'independent_competency_unit_program',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};