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
        Schema::create('document_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'sk-peserta', 'st-instruktur', dll
            $table->string('name'); // 'SK Peserta', 'ST Instruktur'
            
            // Dasar Hukum
            $table->text('dasar_hukum_1')->nullable();
            $table->text('dasar_hukum_2')->nullable();
            $table->text('dasar_hukum_3')->nullable();
            $table->text('dasar_hukum_4')->nullable();
            $table->text('dasar_hukum_5')->nullable();
            
            // Logo & Header
            $table->string('logo_path')->nullable();
            $table->text('kop_surat')->nullable();
            
            // Format Surat
            $table->string('format_nomor')->nullable(); // "Nomor: {nomor}/BPVP/{tahun}"
            $table->string('tempat_surat')->default('Banda Aceh');
            
            // TTD (untuk variable Srikandi)
            $table->string('ttd_pengirim')->nullable(); // ${ttd_pengirim}
            $table->string('nama_pengirim')->nullable(); // ${nama_pengirim}
            $table->string('nip_pengirim')->nullable(); // NIP ${nip_pengirim}
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_settings');
    }
};