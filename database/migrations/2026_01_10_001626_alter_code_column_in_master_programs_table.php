<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_programs', function (Blueprint $table) {
            // Ubah kolom code jadi VARCHAR(100) atau lebih
            $table->string('code', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('master_programs', function (Blueprint $table) {
            $table->string('code', 20)->change(); // kembalikan ke panjang lama
        });
    }
};