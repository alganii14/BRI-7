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
        Schema::table('perusahaan_anaks', function (Blueprint $table) {
            $table->string('kode_uker')->nullable()->after('cabang_induk_terdekat');
            $table->string('nama_uker')->nullable()->after('kode_uker');
            $table->string('rekening_terbentuk')->nullable()->after('status_pipeline');
            $table->string('cif_terbentuk')->nullable()->after('rekening_terbentuk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perusahaan_anaks', function (Blueprint $table) {
            $table->dropColumn(['kode_uker', 'nama_uker', 'rekening_terbentuk', 'cif_terbentuk']);
        });
    }
};
