<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('dasar_hukum_1')->nullable();
            $table->text('dasar_hukum_2')->nullable();
            $table->text('dasar_hukum_3')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('kop_surat')->nullable();
            $table->string('format_nomor')->nullable();
            $table->string('tempat_surat')->nullable();
            $table->string('nama_penandatangan')->nullable();
            $table->string('jabatan_penandatangan')->nullable();
            $table->string('nip_penandatangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_settings');
    }
};