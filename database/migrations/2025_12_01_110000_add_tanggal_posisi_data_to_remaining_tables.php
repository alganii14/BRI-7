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
            'debitur_belum_memiliki_qlolas',
            'merchant_savols',
            'brilinks',
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
            'debitur_belum_memiliki_qlolas',
            'merchant_savols',
            'brilinks',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                if (Schema::hasColumn($table, 'tanggal_posisi_data')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropColumn('tanggal_posisi_data');
                    });
                }
                if (Schema::hasColumn($table, 'tanggal_upload_data')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->dropColumn('tanggal_upload_data');
                    });
                }
            }
        }
    }
};
