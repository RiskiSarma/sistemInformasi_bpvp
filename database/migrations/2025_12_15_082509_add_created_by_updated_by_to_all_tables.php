<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Daftar tabel yang perlu ditambahkan created_by dan updated_by
        $tables = [
            'programs',
            'master_programs',
            'competency_units',
            'participants',
            'instructors',
            'instructor_programs',
            'schedules',
            'attendances',
            'certificates',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
                    }
                    if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                        $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'programs',
            'master_programs',
            'competency_units',
            'participants',
            'instructors',
            'instructor_programs',
            'schedules',
            'attendances',
            'certificates',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['created_by']);
                    $table->dropForeign(['updated_by']);
                    $table->dropColumn(['created_by', 'updated_by']);
                });
            }
        }
    }
};