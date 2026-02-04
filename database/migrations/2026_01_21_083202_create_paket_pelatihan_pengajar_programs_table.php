<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_pelatihan_pengajar_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('jenis_materi_pelatihan_id');
            $table->enum('pengajar_eksternal', ['Y', 'N'])->default('N');
            $table->unsignedBigInteger('pengajar_internal_id')->nullable();
            $table->uuid('pengajar_eksternal_id')->nullable();
            $table->unsignedBigInteger('programs_id'); // FK ke paket_pelatihan_programs.id (BIGINT)
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Custom nama constraint pendek (max 64 char)
            $table->foreign('programs_id', 'programs_id')
                  ->references('id')
                  ->on('programs')
                  ->onDelete('cascade');

            $table->foreign('jenis_materi_pelatihan_id', 'jenis_materi_pelatihan_id')
                  ->references('id')
                  ->on('jenis_materi_pelatihans')
                  ->onDelete('cascade');

            $table->foreign('pengajar_internal_id', 'pengajar_internal_id')
                  ->references('id')
                  ->on('instructors')
                  ->onDelete('set null');

            $table->foreign('pengajar_eksternal_id', 'pengajar_eksternal_id')
                  ->references('id')
                  ->on('pengajar_eksternals')
                  ->onDelete('set null');

            $table->foreign('user_id', 'user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pelatihan_pengajar_programs');
    }
};