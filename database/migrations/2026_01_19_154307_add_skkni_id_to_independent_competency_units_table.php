<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('independent_competency_units', function (Blueprint $table) {
            // Tambah field skkni_id (UUID, karena table skknis pakai UUID)
            $table->uuid('skkni_id')->nullable()->after('id');

            // Buat foreign key constraint
            $table->foreign('skkni_id')
                  ->references('id')
                  ->on('skknis')
                  ->onDelete('set null'); // jika SKKNI dihapus, unit tetap ada tapi skkni_id jadi null
        });
    }

    public function down(): void
    {
        Schema::table('independent_competency_units', function (Blueprint $table) {
            // Hapus foreign key dulu sebelum drop column
            $table->dropForeign(['skkni_id']);
            $table->dropColumn('skkni_id');
        });
    }
};