<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pelatihan_id')->constrained('jenis_pelatihans')->onDelete('cascade');
            $table->year('tahun');
            $table->integer('batch')->nullable();
            $table->integer('jp_harian')->default(0);
            $table->integer('jp_industri')->default(0);
            $table->enum('sabtu_masuk', ['Y', 'N'])->default('N');
            $table->enum('minggu_masuk', ['Y', 'N'])->default('N');
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_akhir')->nullable();
            $table->dateTime('tanggal_awal_pendaftaran')->nullable();
            $table->dateTime('tanggal_akhir_pendaftaran')->nullable();
            $table->dateTime('tanggal_awal_tes_tulis')->nullable();
            $table->dateTime('tanggal_akhir_tes_tulis')->nullable();
            $table->dateTime('tanggal_awal_wawancara')->nullable();
            $table->dateTime('tanggal_akhir_wawancara')->nullable();
            $table->dateTime('tanggal_awal_daftar_ulang')->nullable();
            $table->dateTime('tanggal_akhir_daftar_ulang')->nullable();
            $table->dateTime('tanggal_pengumuman')->nullable();
            $table->foreignId('user_id_pengumuman')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pelatihans');
    }
};