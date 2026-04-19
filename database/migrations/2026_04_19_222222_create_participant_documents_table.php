<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participant_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // ktp, kk, ijazah, foto, skck, dll
            $table->string('document_label'); // Label tampilan
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan')->nullable(); // Catatan dari admin jika ditolak
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['user_id', 'document_type']); // 1 jenis dokumen per peserta
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participant_documents');
    }
};