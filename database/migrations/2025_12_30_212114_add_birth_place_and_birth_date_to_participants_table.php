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
        Schema::table('participants', function (Blueprint $table) {
            // Tambahkan kolom setelah kolom tertentu (misal setelah 'nik' atau 'phone')
            // Jika tidak tahu urutan pastinya, bisa pakai ->after('nama_kolom') atau hilangkan saja
            $table->string('birth_place')->nullable()->after('nik');
            $table->date('birth_date')->nullable()->after('birth_place');
            
            // Alternatif tanpa after() jika urutan tidak penting:
            // $table->string('birth_place')->nullable();
            // $table->date('birth_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['birth_place', 'birth_date']);
        });
    }
};