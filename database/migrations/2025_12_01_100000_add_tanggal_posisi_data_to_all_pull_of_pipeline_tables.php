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
        $tables = [
            'aum_dpks',
            'existing_payrolls',
            'layerings',
            'merchant_savol_edcs',
            'merchant_savol_qris',
            'non_debitur_vol_besars',
            'optimalisasi_business_clusters',
            'penurunan_casa_brilinks',
            'penurunan_prioritas_ritel_mikros',
            'perusahaan_anaks',
            'potensi_payrolls',
            'qlola_non_debiturs',
            'qlola_nonaktifs',
            'qlola_user_tidak_aktifs',
            'strategi8s',
            'user_aktif_casa_kecils',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Add tanggal_posisi_data if not exists
                if (!Schema::hasColumn($table, 'tanggal_posisi_data')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->date('tanggal_posisi_data')->nullable()->after('id');
                    });
                }
                
                // Add tanggal_upload_data if not exists
                if (!Schema::hasColumn($table, 'tanggal_upload_data')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->date('tanggal_upload_data')->nullable()->after('tanggal_posisi_data');
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = [
            'aum_dpks',
            'existing_payrolls',
            'layerings',
            'merchant_savol_edcs',
            'merchant_savol_qris',
            'non_debitur_vol_besars',
            'optimalisasi_business_clusters',
            'penurunan_casa_brilinks',
            'penurunan_prioritas_ritel_mikros',
            'perusahaan_anaks',
            'potensi_payrolls',
            'qlola_non_debiturs',
            'qlola_nonaktifs',
            'qlola_user_tidak_aktifs',
            'strategi8s',
            'user_aktif_casa_kecils',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn(['tanggal_posisi_data', 'tanggal_upload_data']);
                });
            }
        }
    }
};
