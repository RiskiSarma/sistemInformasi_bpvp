<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_pelatihan_units', function (Blueprint $table) {
            $table->uuid('id')->primary(); // PK UUID

            $table->unsignedBigInteger('program_pelatihan_id'); // BIGINT FK ke master_programs.id
            $table->enum('type_unit', ['skkni', 'special'])->default('skkni');

            $table->unsignedBigInteger('independent_competency_units'); // BIGINT FK ke independent_competency_units.id

            $table->enum('sub_unit_kompetensi', ['Y', 'N'])->default('N');

            $table->uuid('program_pelatihan_sub_unit_id')->nullable(); // UUID self-referencing (ke id table ini sendiri)

            $table->integer('jp')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('program_pelatihan_id')
                  ->references('id')
                  ->on('master_programs')
                  ->onDelete('cascade');

            $table->foreign('independent_competency_units')
                  ->references('id')
                  ->on('independent_competency_units')
                  ->onDelete('cascade');

            // Self-referencing FK (UUID ke UUID)
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