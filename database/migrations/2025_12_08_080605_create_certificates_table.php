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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->string('certificate_number', 100)->unique();
            $table->date('issue_date');
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['draft', 'issued', 'revoked'])->default('issued');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index('certificate_number');
            $table->index('status');
            $table->index('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
?>