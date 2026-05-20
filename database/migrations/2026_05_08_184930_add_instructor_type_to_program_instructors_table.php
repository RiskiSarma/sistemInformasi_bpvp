<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('program_instructors', function (Blueprint $table) {
        $table->string('instructor_type')->default('internal'); // internal / external
    });
}

public function down()
{
    Schema::table('program_instructors', function (Blueprint $table) {
        $table->dropColumn('instructor_type');
    });
}
};
