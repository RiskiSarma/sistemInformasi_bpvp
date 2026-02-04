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
        Schema::table('master_programs', function (Blueprint $table) {
            // 1. Tambah kolom UUID baru
            $table->uuid('new_id')->nullable()->after('id');

            // 2. Isi UUID untuk semua data existing
            DB::table('master_programs')->get()->each(function ($row) {
                DB::table('master_programs')
                    ->where('id', $row->id)
                    ->update(['new_id' => (string) Str::uuid()]);
            });

            // 3. Jadikan new_id NOT NULL
            $table->uuid('new_id')->nullable(false)->change();

            // 4. Drop foreign keys yang mengarah ke table ini (jika ada)
            // Contoh: drop foreign di table lain yang refer ke master_programs.id
            // Lakukan manual jika ada pivot lain

            // 5. Drop old PK dan kolom id
            $table->dropPrimary('id');
            $table->dropColumn('id');

            // 6. Rename new_id jadi id
            $table->renameColumn('new_id', 'id');

            // 7. Jadikan id primary key baru
            $table->primary('id');
        });
    }

    public function down(): void
    {
        // Rollback sulit karena data UUID tidak bisa balik ke bigint
        // Opsional: skip atau buat manual
    }
};