<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            if (!Schema::hasColumn('participants', 'nik')) {
                $table->string('nik', 20)->nullable()->after('name');
            }
            if (!Schema::hasColumn('participants', 'birth_place')) {
                $table->string('birth_place', 100)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('participants', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('birth_place');
            }
        });
        
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'duration')) {
                $table->integer('duration')->nullable()->after('end_date')->comment('Durasi dalam jam');
            }
        });
        
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'signatory_name')) {
                $table->string('signatory_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('certificates', 'signatory_nip')) {
                $table->string('signatory_nip', 50)->nullable()->after('signatory_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['nik', 'birth_place', 'birth_date']);
        });
        
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['duration']);
        });
        
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['signatory_name', 'signatory_nip']);
        });
    }
};
?>