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

            $table->uuid('program_pelatihan_id'); // FK ke master_programs (program_pelatihans)
            $table->enum('type_unit', ['skkni', 'special'])->default('skkni');

            $table->uuid('unit_kompetensi_id'); // FK ke independent_competency_units (alias unit_kompetensis)

            $table->enum('sub_unit_kompetensi', ['Y', 'N'])->default('N');

            $table->uuid('program_pelatihan_sub_unit_id')->nullable(); // self-referencing FK (sub-unit)

            $table->integer('jp')->nullable(); // total JP per unit di master program

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('program_pelatihan_id')
                  ->references('id')
                  ->on('master_programs') // atau 'program_pelatihans' jika nama table beda
                  ->onDelete('cascade');

            $table->foreign('unit_kompetensi_id')
                  ->references('id')
                  ->on('independent_competency_units')
                  ->onDelete('cascade');

            $table->foreign('program_pelatihan_sub_unit_id')
                  ->references('id')
                  ->on('program_pelatihan_units') // self-reference
                  ->onDelete('set null'); // jika sub-unit dihapus, parent tetap ada tapi sub_unit_id null
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_pelatihan_units');
    }
};