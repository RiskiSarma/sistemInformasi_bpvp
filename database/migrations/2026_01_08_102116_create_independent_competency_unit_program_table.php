<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('independent_competency_unit_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')
                  ->constrained('programs')
                  ->onDelete('cascade')
                  ->name('fk_icup_program_id');  // nama pendek untuk foreign key

            $table->foreignId('independent_competency_unit_id')
                  ->constrained('independent_competency_units')
                  ->onDelete('cascade')
                  ->name('fk_icup_unit_id');  // nama pendek untuk foreign key

            $table->timestamps();

            // Unique constraint dengan nama pendek
            $table->unique(
                ['program_id', 'independent_competency_unit_id'],
                'unique_icup_program_unit'  // ← nama custom pendek
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('independent_competency_unit_program');
    }
};