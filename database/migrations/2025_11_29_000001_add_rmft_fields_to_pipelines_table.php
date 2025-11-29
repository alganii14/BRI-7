<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pipelines', function (Blueprint $table) {
            $table->string('nama_rmft')->nullable()->after('tanggal');
            $table->string('pn_rmft')->nullable()->after('nama_rmft');
        });
    }

    public function down(): void
    {
        Schema::table('pipelines', function (Blueprint $table) {
            $table->dropColumn(['nama_rmft', 'pn_rmft']);
        });
    }
};
