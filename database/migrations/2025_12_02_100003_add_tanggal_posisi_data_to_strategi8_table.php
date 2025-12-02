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
        // Add to strategi8 table
        if (Schema::hasTable('strategi8')) {
            if (!Schema::hasColumn('strategi8', 'tanggal_posisi_data')) {
                Schema::table('strategi8', function (Blueprint $table) {
                    $table->date('tanggal_posisi_data')->nullable()->after('id');
                });
            }
            
            if (!Schema::hasColumn('strategi8', 'tanggal_upload_data')) {
                Schema::table('strategi8', function (Blueprint $table) {
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
        if (Schema::hasTable('strategi8')) {
            Schema::table('strategi8', function (Blueprint $table) {
                if (Schema::hasColumn('strategi8', 'tanggal_posisi_data')) {
                    $table->dropColumn('tanggal_posisi_data');
                }
                if (Schema::hasColumn('strategi8', 'tanggal_upload_data')) {
                    $table->dropColumn('tanggal_upload_data');
                }
            });
        }
    }
};
