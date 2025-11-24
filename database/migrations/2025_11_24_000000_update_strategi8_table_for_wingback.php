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
        Schema::table('strategi8', function (Blueprint $table) {
            // Drop old columns that don't match wingback.csv
            $table->dropColumn([
                'regional_office',
                'ytd',
                'product_type',
                'jenis_nasabah',
                'segmentasi_bpr'
            ]);
        });

        Schema::table('strategi8', function (Blueprint $table) {
            // Add new column that matches wingback.csv
            $table->string('segmentasi')->nullable()->after('nama_nasabah');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('strategi8', function (Blueprint $table) {
            // Restore old columns
            $table->string('regional_office')->nullable()->after('id');
            $table->string('ytd')->nullable()->after('no_rekening');
            $table->string('product_type')->nullable()->after('ytd');
            $table->string('jenis_nasabah')->nullable()->after('nama_nasabah');
            $table->string('segmentasi_bpr')->nullable()->after('jenis_nasabah');
            
            // Drop new column
            $table->dropColumn('segmentasi');
        });
    }
};
