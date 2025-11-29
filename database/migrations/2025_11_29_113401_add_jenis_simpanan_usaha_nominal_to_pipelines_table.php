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
        Schema::table('pipelines', function (Blueprint $table) {
            $table->string('jenis_simpanan')->nullable()->after('tipe');
            $table->string('jenis_usaha')->nullable()->after('jenis_simpanan');
            $table->decimal('nominal', 20, 2)->nullable()->after('jenis_usaha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pipelines', function (Blueprint $table) {
            $table->dropColumn(['jenis_simpanan', 'jenis_usaha', 'nominal']);
        });
    }
};
