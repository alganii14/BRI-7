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
        Schema::table('nasabah_downgrades', function (Blueprint $table) {
            $table->date('tanggal_posisi_data')->nullable()->after('aum');
            $table->index('tanggal_posisi_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nasabah_downgrades', function (Blueprint $table) {
            $table->dropIndex(['tanggal_posisi_data']);
            $table->dropColumn('tanggal_posisi_data');
        });
    }
};
