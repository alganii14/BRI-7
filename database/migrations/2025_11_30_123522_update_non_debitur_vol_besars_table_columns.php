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
        Schema::table('non_debitur_vol_besars', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['no_rekening', 'segmentasi', 'vol_qcash', 'vol_qib', 'saldo']);
            
            // Add new columns matching CSV structure
            $table->string('norek_pinjaman')->nullable()->after('cifno');
            $table->string('norek_simpanan')->nullable()->after('norek_pinjaman');
            $table->string('balance')->nullable()->after('norek_simpanan');
            $table->string('volume')->nullable()->after('balance');
            $table->string('keterangan')->nullable()->after('nama_nasabah');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('non_debitur_vol_besars', function (Blueprint $table) {
            // Restore old columns
            $table->dropColumn(['norek_pinjaman', 'norek_simpanan', 'balance', 'volume', 'keterangan']);
            
            $table->string('no_rekening')->nullable()->after('cifno');
            $table->string('segmentasi')->nullable()->after('nama_nasabah');
            $table->string('vol_qcash')->nullable()->after('segmentasi');
            $table->string('vol_qib')->nullable()->after('vol_qcash');
            $table->string('saldo')->nullable()->after('vol_qib');
        });
    }
};
