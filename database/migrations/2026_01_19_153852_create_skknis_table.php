<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skknis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('skkni', 255);
            $table->string('nomor', 255);
            $table->date('tanggal');
            $table->enum('berlaku', ['Y', 'N'])->default('Y');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skknis');
    }
};