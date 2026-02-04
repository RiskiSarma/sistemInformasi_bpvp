<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_pelatihans', function (Blueprint $table) {
            $table->unsignedBigInteger('integer_id')->nullable()->after('id');
            $table->index('integer_id');
        });

        // Optional: isi integer_id untuk data existing (jika ada data lama)
        \DB::statement("UPDATE paket_pelatihans SET integer_id = id WHERE integer_id IS NULL");
        // Atau pakai loop kalau id-nya bukan auto-increment
    }

    public function down(): void
    {
        Schema::table('paket_pelatihans', function (Blueprint $table) {
            $table->dropColumn('integer_id');
        });
    }
};