<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jenis_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_pelatihan', 100); // nama jenis (Non Boarding, PBL, dll)
            $table->string('batch', 255)->nullable(); // batch di level jenis (opsional)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pelatihans');
    }
};
