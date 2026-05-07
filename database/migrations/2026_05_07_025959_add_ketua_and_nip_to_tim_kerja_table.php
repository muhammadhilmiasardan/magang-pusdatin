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
        Schema::table('tim_kerja', function (Blueprint $table) {
            $table->string('ketua_tim')->nullable()->after('nama_tim');
            $table->string('nip_ketua_tim')->nullable()->after('ketua_tim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tim_kerja', function (Blueprint $table) {
            $table->dropColumn(['ketua_tim', 'nip_ketua_tim']);
        });
    }
};
