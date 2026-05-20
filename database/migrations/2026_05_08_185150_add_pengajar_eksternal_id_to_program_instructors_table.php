<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('program_instructors', function (Blueprint $table) {
            $table->char('pengajar_eksternal_id', 36)->nullable()->after('instructor_id');
            $table->string('instructor_type', 20)->default('internal')->after('pengajar_eksternal_id');
        });
    }

    public function down()
    {
        Schema::table('program_instructors', function (Blueprint $table) {
            $table->dropColumn(['pengajar_eksternal_id', 'instructor_type']);
        });
    }
};