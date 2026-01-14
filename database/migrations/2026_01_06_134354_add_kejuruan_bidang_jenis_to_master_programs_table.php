<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_programs', function (Blueprint $table) {
            $table->string('kejuruan', 100)->nullable()->after('name');
            $table->string('bidang', 100)->nullable()->after('kejuruan');
            $table->string('jenis_pelatihan', 50)->nullable()->after('bidang');
        });
    }

    public function down(): void
    {
        Schema::table('master_programs', function (Blueprint $table) {
            $table->dropColumn(['kejuruan', 'bidang', 'jenis_pelatihan']);
        });
    }
};