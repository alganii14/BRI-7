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
        Schema::table('existing_payroll', function (Blueprint $table) {
            $table->date('tanggal_posisi_data')->nullable()->after('saldo_rekening');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('existing_payroll', function (Blueprint $table) {
            $table->dropColumn('tanggal_posisi_data');
        });
    }
};
