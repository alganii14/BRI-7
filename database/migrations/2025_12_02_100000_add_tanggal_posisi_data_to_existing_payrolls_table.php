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
        // Add to existing_payrolls table
        if (Schema::hasTable('existing_payrolls')) {
            if (!Schema::hasColumn('existing_payrolls', 'tanggal_posisi_data')) {
                Schema::table('existing_payrolls', function (Blueprint $table) {
                    $table->date('tanggal_posisi_data')->nullable()->after('id');
                });
            }
            
            if (!Schema::hasColumn('existing_payrolls', 'tanggal_upload_data')) {
                Schema::table('existing_payrolls', function (Blueprint $table) {
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
        if (Schema::hasTable('existing_payrolls')) {
            Schema::table('existing_payrolls', function (Blueprint $table) {
                if (Schema::hasColumn('existing_payrolls', 'tanggal_posisi_data')) {
                    $table->dropColumn('tanggal_posisi_data');
                }
                if (Schema::hasColumn('existing_payrolls', 'tanggal_upload_data')) {
                    $table->dropColumn('tanggal_upload_data');
                }
            });
        }
    }
};
