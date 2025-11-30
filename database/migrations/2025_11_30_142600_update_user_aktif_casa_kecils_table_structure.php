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
        Schema::table('user_aktif_casa_kecils', function (Blueprint $table) {
            // Drop old columns
            if (Schema::hasColumn('user_aktif_casa_kecils', 'saldo_bulan_lalu')) {
                $table->dropColumn('saldo_bulan_lalu');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'saldo_bulan_berjalan')) {
                $table->dropColumn('saldo_bulan_berjalan');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'delta_saldo')) {
                $table->dropColumn('delta_saldo');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'nama_rm_pemrakarsa')) {
                $table->dropColumn('nama_rm_pemrakarsa');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'qcash')) {
                $table->dropColumn('qcash');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'qib')) {
                $table->dropColumn('qib');
            }
            
            // Add new columns
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'norek_simpanan')) {
                $table->string('norek_simpanan')->nullable()->after('norek_pinjaman');
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'balance')) {
                $table->string('balance')->nullable()->after('norek_simpanan');
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'volume')) {
                $table->string('volume')->nullable()->after('balance');
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'plafon')) {
                $table->string('plafon')->nullable()->after('nama_nasabah');
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'pn_pengelola_1')) {
                $table->string('pn_pengelola_1')->nullable()->after('plafon');
            }
            
            // Rename nama_nasabah if needed (keep it as is)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_aktif_casa_kecils', function (Blueprint $table) {
            // Restore old columns
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'saldo_bulan_lalu')) {
                $table->string('saldo_bulan_lalu')->nullable();
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'saldo_bulan_berjalan')) {
                $table->string('saldo_bulan_berjalan')->nullable();
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'delta_saldo')) {
                $table->string('delta_saldo')->nullable();
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'nama_rm_pemrakarsa')) {
                $table->string('nama_rm_pemrakarsa')->nullable();
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'qcash')) {
                $table->string('qcash')->nullable();
            }
            if (!Schema::hasColumn('user_aktif_casa_kecils', 'qib')) {
                $table->string('qib')->nullable();
            }
            
            // Drop new columns
            if (Schema::hasColumn('user_aktif_casa_kecils', 'norek_simpanan')) {
                $table->dropColumn('norek_simpanan');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'balance')) {
                $table->dropColumn('balance');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'volume')) {
                $table->dropColumn('volume');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'plafon')) {
                $table->dropColumn('plafon');
            }
            if (Schema::hasColumn('user_aktif_casa_kecils', 'pn_pengelola_1')) {
                $table->dropColumn('pn_pengelola_1');
            }
        });
    }
};
