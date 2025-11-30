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
        Schema::table('qlola_non_debiturs', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'no_rekening',
                'segmentasi',
                'cek_qcash',
                'cek_cms',
                'cek_ib'
            ]);
        });

        Schema::table('qlola_non_debiturs', function (Blueprint $table) {
            // Add new columns after cifno
            $table->string('norek_simpanan')->nullable()->after('cifno');
            $table->string('norek_pinjaman')->nullable()->after('norek_simpanan');
            $table->string('balance')->nullable()->after('norek_pinjaman');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qlola_non_debiturs', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'norek_simpanan',
                'norek_pinjaman',
                'balance'
            ]);
        });

        Schema::table('qlola_non_debiturs', function (Blueprint $table) {
            // Restore old columns
            $table->string('no_rekening')->nullable()->after('cifno');
            $table->string('segmentasi')->nullable()->after('nama_nasabah');
            $table->string('cek_qcash')->nullable()->after('segmentasi');
            $table->string('cek_cms')->nullable()->after('cek_qcash');
            $table->string('cek_ib')->nullable()->after('cek_cms');
        });
    }
};
