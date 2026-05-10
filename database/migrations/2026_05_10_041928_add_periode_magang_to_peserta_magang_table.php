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
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->string('periode_magang')->nullable()->after('status_magang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_magang', function (Blueprint $table) {
            $table->dropColumn('periode_magang');
        });
    }
};
