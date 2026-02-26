<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('program_instructors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('instructor_id');
            $table->boolean('is_penanggung_jawab')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            
            $table->unique(['program_id', 'instructor_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_instructors');
    }
};