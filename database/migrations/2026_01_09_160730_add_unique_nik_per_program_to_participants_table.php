<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            // Cek dan hapus unique global NIK kalau ada
            $indexes = DB::select("SHOW INDEX FROM participants WHERE Column_name = 'nik' AND Non_unique = 0");

            foreach ($indexes as $index) {
                if ($index->Key_name !== 'PRIMARY' && str_contains($index->Key_name, 'nik')) {
                    $table->dropUnique($index->Key_name);
                }
            }

            // Tambah unique baru: NIK unik per program_id
            $table->unique(['program_id', 'nik'], 'participants_program_nik_unique');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropUnique('participants_program_nik_unique');

            // Kembalikan unique global NIK (opsional)
            $table->unique('nik');
        });
    }
};