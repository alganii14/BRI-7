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
        Schema::table('user_aktif_casa_kecils', function (Blueprint $table) {
            $table->renameColumn('nama_nasabah', 'nama_debitur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_aktif_casa_kecils', function (Blueprint $table) {
            $table->renameColumn('nama_debitur', 'nama_nasabah');
        });
    }
};
