<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->string('asal_kabupaten', 100)->nullable()->after('address');
            $table->string('asal_kecamatan', 100)->nullable()->after('asal_kabupaten');
            $table->string('asal_kelurahan', 100)->nullable()->after('asal_kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['asal_kabupaten', 'asal_kecamatan', 'asal_kelurahan']);
        });
    }
};