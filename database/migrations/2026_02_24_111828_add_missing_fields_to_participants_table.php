<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('file_ktp', 255)->nullable()->after('nik');
            $table->string('file_ijazah', 255)->nullable()->after('file_ktp');
            $table->string('nomor_sertifikat', 100)->nullable()->after('status');
            $table->date('tanggal_sertifikat')->nullable()->after('nomor_sertifikat');
            $table->enum('kelulusan', ['Lulus', 'Tidak Lulus'])->default('Tidak Lulus')->after('tanggal_sertifikat');
            
            // Kalau perlu alamat domisili terpisah
            $table->text('alamat_domisili')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir', 'file_ktp', 'file_ijazah', 'nomor_sertifikat',
                'tanggal_sertifikat', 'kelulusan', 'alamat_domisili'
            ]);
        });
    }
};