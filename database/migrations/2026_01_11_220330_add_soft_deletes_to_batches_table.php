<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            // Cek dulu kalau belum ada kolom deleted_at
            if (!Schema::hasColumn('batches', 'deleted_at')) {
                $table->softDeletes(); // tambah deleted_at timestamp nullable
            }
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            if (Schema::hasColumn('batches', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};