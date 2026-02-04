<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // STEP 1: Pastikan kolom uuid sudah ada (dari migration sebelumnya)
        Schema::table('program_pelatihan_units', function (Blueprint $table) {
            if (!Schema::hasColumn('program_pelatihan_units', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
            }
        });

        // STEP 2: Generate UUID untuk data lama (aman jika sudah ada atau table kosong)
        DB::table('program_pelatihan_units')->get()->each(function ($row) {
            if (empty($row->uuid)) {
                DB::table('program_pelatihan_units')
                    ->where('id', $row->id)
                    ->update(['uuid' => (string) Str::uuid()]);
            }
        });

        Schema::table('program_pelatihan_units', function (Blueprint $table) {
            // STEP 3: Jadikan uuid NOT NULL
            $table->uuid('uuid')->nullable(false)->change();

            // STEP 4: Drop primary key lama
            $table->dropPrimary(['id']);

            // STEP 5: Jadikan uuid sebagai primary key baru
            $table->primary('uuid');

            // STEP 6: Update self-referencing FK ke UUID
            if (Schema::hasColumn('program_pelatihan_units', 'program_pelatihan_sub_unit_id')) {
                $table->uuid('program_pelatihan_sub_unit_id')->nullable()->change();
            }
        });

        // STEP 7: Rename uuid ke id dengan CHANGE COLUMN (FIX untuk MySQL)
        DB::statement('ALTER TABLE program_pelatihan_units CHANGE COLUMN uuid id CHAR(36) NOT NULL');

        // STEP 8: Drop kolom id lama jika masih ada (aman)
        Schema::table('program_pelatihan_units', function (Blueprint $table) {
            if (Schema::hasColumn('program_pelatihan_units', 'id') && Schema::getColumnType('program_pelatihan_units', 'id') !== 'string') {
                $table->dropColumn('id');
            }
        });
    }

    public function down(): void
    {
        // Rollback sulit, skip atau restore dari backup
    }
};