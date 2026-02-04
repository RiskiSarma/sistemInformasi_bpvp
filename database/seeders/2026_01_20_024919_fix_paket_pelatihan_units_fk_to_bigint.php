<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_pelatihan_units', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('paket_pelatihan_programs_id'); // FIX: BIGINT match parent
            $table->unsignedBigInteger('program_pelatihan_unit_id')->nullable(); // BIGINT match program_pelatihan_units.id (jika BIGINT)
            $table->unsignedBigInteger('master_program_sub_unit_id')->nullable(); // BIGINT match master_programs.id
            $table->integer('jp')->nullable();
            $table->enum('sub_unit_kompetensi', ['Y', 'N'])->default('N');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys (BIGINT)
            $table->foreign('paket_pelatihan_programs_id')
                  ->references('id')
                  ->on('paket_pelatihan_programs')
                  ->onDelete('cascade');

            $table->foreign('program_pelatihan_unit_id')
                  ->references('id')
                  ->on('program_pelatihan_units')
                  ->onDelete('cascade');

            $table->foreign('master_program_sub_unit_id')
                  ->references('id')
                  ->on('master_programs')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pelatihan_units');
    }
};