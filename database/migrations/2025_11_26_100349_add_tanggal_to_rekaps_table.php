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
        Schema::table('rekaps', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('nama_kc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
