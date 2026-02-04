<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_pelatihan_units', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('program_pelatihan_id'); // BIGINT FK ke master_programs.id
            $table->enum('type_unit', ['skkni', 'special'])->default('skkni');

            $table->unsignedBigInteger('unit_kompetensi_id'); // BIGINT FK ke independent_competency_units.id

            $table->enum('sub_unit_kompetensi', ['Y', 'N'])->default('N');

            $table->uuid('program_pelatihan_sub_unit_id')->nullable(); // UUID self-referencing

            $table->integer('jp')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys ke parent (BIGINT)
            $table->foreign('program_pelatihan_id')
                  ->references('id')
                  ->on('master_programs')
                  ->onDelete('cascade');

            $table->foreign('unit_kompetensi_id')
                  ->references('id')
                  ->on('independent_competency_units')
                  ->onDelete('cascade');

            // Self-referencing FK (UUID)
            $table->foreign('program_pelatihan_sub_unit_id')
                  ->references('id')
                  ->on('program_pelatihan_units')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_pelatihan_units');
    }
};