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
        Schema::create('merchant_savol_edcs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kanca')->nullable();
            $table->string('nama_kanca')->nullable();
            $table->string('kode_uker')->nullable();
            $table->string('nama_uker')->nullable();
            $table->string('nama_merchant')->nullable();
            $table->string('norek')->nullable();
            $table->string('cifno')->nullable();
            $table->string('jumlah_tid')->nullable();
            $table->string('jumlah_trx')->nullable();
            $table->string('sales_volume')->nullable();
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
        Schema::dropIfExists('merchant_savol_edcs');
    }
};
