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
        Schema::table('qlola_nonaktifs', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('qlola_nonaktifs', 'pn_pengelola')) {
                $table->dropColumn('pn_pengelola');
            }
            
            // Add new columns
            if (!Schema::hasColumn('qlola_nonaktifs', 'balance')) {
                $table->string('balance')->nullable()->after('norek_simpanan');
            }
            if (!Schema::hasColumn('qlola_nonaktifs', 'pn_pengelola_1')) {
                $table->string('pn_pengelola_1')->nullable()->after('plafon');
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
        Schema::table('qlola_nonaktifs', function (Blueprint $table) {
            // Restore old structure
            if (Schema::hasColumn('qlola_nonaktifs', 'balance')) {
                $table->dropColumn('balance');
            }
            if (Schema::hasColumn('qlola_nonaktifs', 'pn_pengelola_1')) {
                $table->dropColumn('pn_pengelola_1');
            }
            if (!Schema::hasColumn('qlola_nonaktifs', 'pn_pengelola')) {
                $table->string('pn_pengelola')->nullable();
            }
        });
    }
};
