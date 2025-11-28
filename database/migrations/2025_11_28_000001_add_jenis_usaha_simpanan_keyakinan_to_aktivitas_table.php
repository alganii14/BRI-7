<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->string('jenis_usaha')->nullable()->after('tipe');
            $table->string('jenis_simpanan')->nullable()->after('jenis_usaha');
            $table->string('tingkat_keyakinan')->nullable()->after('jenis_simpanan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->dropColumn(['jenis_usaha', 'jenis_simpanan', 'tingkat_keyakinan']);
        });
    }
};
