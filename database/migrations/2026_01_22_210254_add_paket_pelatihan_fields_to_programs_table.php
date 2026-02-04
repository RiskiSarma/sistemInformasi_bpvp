<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->string('paket_pelatihan_id', 100)->nullable()->after('id'); // FK manual ke paket_pelatihans (varchar/UUID)
            $table->integer('jp')->nullable()->after('paket_pelatihan_id');
            $table->integer('jp_industri')->nullable()->after('jp');
            $table->enum('ada_industri', ['Y', 'N'])->default('N')->after('jp_industri');
            $table->integer('jp_harian')->nullable()->after('ada_industri');
            $table->foreignId('user_id')->nullable()->constrained('users')->after('jp_harian'); 

            // Index untuk pencarian cepat
            $table->index('paket_pelatihan_id');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'paket_pelatihan_id',
                'jp',
                'jp_industri',
                'ada_industri',
                'jp_harian',
                'user_id',
            ]);
        });
    }
};