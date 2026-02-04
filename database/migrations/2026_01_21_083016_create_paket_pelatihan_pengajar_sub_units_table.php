<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_pelatihan_pengajar_sub_units', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('pp_unit_id');
            $table->unsignedBigInteger('programs_id');
            $table->enum('pengajar_eksternal', ['Y', 'N'])->default('N');
            $table->unsignedBigInteger('pengajar_internal_id')->nullable();
            $table->uuid('pengajar_eksternal_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pp_unit_id')
                  ->references('id')
                  ->on('paket_pelatihan_units')
                  ->onDelete('cascade');

            $table->foreign('programs_id')
                  ->references('id')
                  ->on('programs')
                  ->onDelete('cascade');

            $table->foreign('pengajar_eksternal_id')
                  ->references('id')
                  ->on('pengajar_eksternals')
                  ->onDelete('set null');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_pelatihan_pengajar_sub_units');
    }
};