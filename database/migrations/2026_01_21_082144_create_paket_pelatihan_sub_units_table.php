<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_pelatihan_sub_units', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('paket_pelatihan_unit_id'); // FK ke paket_pelatihan_units.id (BIGINT)
            $table->unsignedBigInteger('master_programs_id');    // FK ke program_pelatihans.id (BIGINT)
            $table->integer('jp')->nullable();                     // jam pelatihan
            $table->unsignedBigInteger('independent_competency_units');      // FK ke unit_kompetensis.id (BIGINT)

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('paket_pelatihan_unit_id')
                  ->references('id')
                  ->on('paket_pelatihan_units')
                  ->onDelete('cascade');

            $table->foreign('master_programs_id')
                  ->references('id')
                  ->on('master_programs')
                  ->onDelete('cascade');

            $table->foreign('independent_competency_units')
                  ->references('id')
                  ->on('independent_competency_units')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pelatihan_sub_units');
    }
};