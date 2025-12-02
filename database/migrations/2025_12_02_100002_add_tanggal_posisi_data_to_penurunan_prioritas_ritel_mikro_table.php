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
        // Add to penurunan_prioritas_ritel_mikro table (without 's')
        if (Schema::hasTable('penurunan_prioritas_ritel_mikro')) {
            if (!Schema::hasColumn('penurunan_prioritas_ritel_mikro', 'tanggal_posisi_data')) {
                Schema::table('penurunan_prioritas_ritel_mikro', function (Blueprint $table) {
                    $table->date('tanggal_posisi_data')->nullable()->after('id');
                });
            }
            
            if (!Schema::hasColumn('penurunan_prioritas_ritel_mikro', 'tanggal_upload_data')) {
                Schema::table('penurunan_prioritas_ritel_mikro', function (Blueprint $table) {
                    $table->date('tanggal_upload_data')->nullable()->after('tanggal_posisi_data');
                });
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
        if (Schema::hasTable('penurunan_prioritas_ritel_mikro')) {
            Schema::table('penurunan_prioritas_ritel_mikro', function (Blueprint $table) {
                if (Schema::hasColumn('penurunan_prioritas_ritel_mikro', 'tanggal_posisi_data')) {
                    $table->dropColumn('tanggal_posisi_data');
                }
                if (Schema::hasColumn('penurunan_prioritas_ritel_mikro', 'tanggal_upload_data')) {
                    $table->dropColumn('tanggal_upload_data');
                }
            });
        }
    }
};
