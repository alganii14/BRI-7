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
        // Reorder columns by recreating the table with correct order
        Schema::table('user_aktif_casa_kecils', function (Blueprint $table) {
            // Move cifno after uker
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN cifno VARCHAR(255) AFTER uker');
            
            // Move norek_pinjaman after cifno
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN norek_pinjaman VARCHAR(255) AFTER cifno');
            
            // Move norek_simpanan after norek_pinjaman
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN norek_simpanan VARCHAR(255) AFTER norek_pinjaman');
            
            // Move balance after norek_simpanan
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN balance VARCHAR(255) AFTER norek_simpanan');
            
            // Move volume after balance
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN volume VARCHAR(255) AFTER balance');
            
            // Move nama_debitur after volume
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN nama_debitur VARCHAR(255) AFTER volume');
            
            // Move plafon after nama_debitur
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN plafon VARCHAR(255) AFTER nama_debitur');
            
            // Move pn_pengelola_1 after plafon
            DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN pn_pengelola_1 VARCHAR(255) AFTER plafon');
            
            // Add keterangan if not exists, or move it after pn_pengelola_1
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'keterangan')) {
                DB::statement('ALTER TABLE user_aktif_casa_kecils ADD COLUMN keterangan TEXT AFTER pn_pengelola_1');
            } else {
                DB::statement('ALTER TABLE user_aktif_casa_kecils MODIFY COLUMN keterangan TEXT AFTER pn_pengelola_1');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to reverse, as this is just reordering
    }
};
