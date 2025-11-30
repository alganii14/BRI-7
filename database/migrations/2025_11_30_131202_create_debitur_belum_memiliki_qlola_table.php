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
        Schema::create('debitur_belum_memiliki_qlola', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kanca')->nullable();
            $table->string('kanca')->nullable();
            $table->string('kode_uker')->nullable();
            $table->string('uker')->nullable();
            $table->string('cifno')->nullable();
            $table->string('norek_pinjaman')->nullable();
            $table->string('norek_simpanan')->nullable();
            $table->string('balance')->nullable();
            $table->string('nama_debitur')->nullable();
            $table->string('plafon')->nullable();
            $table->string('pn_pengelola_1')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debitur_belum_memiliki_qlola');
    }
};
