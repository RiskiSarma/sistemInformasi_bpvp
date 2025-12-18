<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('participants', function (Blueprint $table) {
            // Hapus kolom name dan email
            if (Schema::hasColumn('participants', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('participants', 'email')) {
                $table->dropColumn('email');
            }
            
            // Tambahkan kolom yang mungkin belum ada
            if (!Schema::hasColumn('participants', 'batch')) {
                $table->string('batch', 50)->nullable()->after('program_id');
            }
            if (!Schema::hasColumn('participants', 'enrollment_date')) {
                $table->date('enrollment_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('participants', 'completion_date')) {
                $table->date('completion_date')->nullable()->after('enrollment_date');
            }
        });
    }

    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->dropColumn(['batch', 'enrollment_date', 'completion_date']);
        });
    }
};