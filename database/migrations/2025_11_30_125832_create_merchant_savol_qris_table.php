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
        Schema::create('merchant_savol_qris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kanca')->nullable();
            $table->string('nama_kanca')->nullable();
            $table->string('kode_uker')->nullable();
            $table->string('nama_uker')->nullable();
            $table->string('storeid')->nullable();
            $table->string('nama_merchant')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('cif')->nullable();
            $table->string('akumulasi_sv_total')->nullable();
            $table->string('posisi_sv_total')->nullable();
            $table->string('saldo_posisi')->nullable();
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
        Schema::dropIfExists('merchant_savol_qris');
    }
};
