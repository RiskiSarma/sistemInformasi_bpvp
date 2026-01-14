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
        Schema::table('batches', function (Blueprint $table) {
            $table->foreignId('jenis_pelatihan_id')->nullable()->constrained('jenis_pelatihans')->onDelete('set null');
            // Opsional: hapus kolom jenis_pelatihan varchar lama kalau sudah tidak dipakai
            // $table->dropColumn('jenis_pelatihan');
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['jenis_pelatihan_id']);
            $table->dropColumn('jenis_pelatihan_id');
            // $table->string('jenis_pelatihan')->nullable();
        });
    }
};
