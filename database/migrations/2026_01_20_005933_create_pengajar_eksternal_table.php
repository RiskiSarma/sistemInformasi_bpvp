<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajar_eksternals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 255);
            $table->string('nik', 100)->unique();
            $table->string('instansi', 255)->nullable();
            $table->string('jabatan', 255)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->uuid('pendidikan_id')->nullable(); // FK ke pendidikan
            $table->string('kejuruan_pendidikan', 255)->nullable(); // field baru
            $table->uuid('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pendidikan_id')->references('id')->on('pendidikan')->onDelete('set null');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajar_eksternals');
    }
};