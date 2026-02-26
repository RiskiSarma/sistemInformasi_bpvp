<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('program_pelatihan_units', function (Blueprint $table) {
            if (!Schema::hasColumn('program_pelatihan_units', 'durasi_jp')) {
                $table->integer('durasi_jp')->default(0)->after('jp');
            }
            
            if (!Schema::hasColumn('program_pelatihan_units', 'urutan')) {
                $table->integer('urutan')->default(0)->after('durasi_jp');
            }
            
            if (!Schema::hasColumn('program_pelatihan_units', 'is_editable')) {
                $table->boolean('is_editable')->default(true)->after('urutan');
            }
        });
    }

    public function down()
    {
        Schema::table('program_pelatihan_units', function (Blueprint $table) {
            $table->dropColumn(['durasi_jp', 'urutan', 'is_editable']);
        });
    }
};