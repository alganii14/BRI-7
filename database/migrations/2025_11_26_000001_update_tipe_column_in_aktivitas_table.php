<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ubah kolom tipe dari enum ke string menggunakan raw SQL
        DB::statement("ALTER TABLE aktivitas MODIFY COLUMN tipe VARCHAR(20) DEFAULT 'lama'");
        
        // Update data existing: self -> lama, assigned -> lama
        DB::statement("UPDATE aktivitas SET tipe = 'lama' WHERE tipe IN ('self', 'assigned') OR tipe IS NULL OR tipe = ''");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Update data ke nilai enum yang valid
        DB::statement("UPDATE aktivitas SET tipe = 'self' WHERE tipe NOT IN ('self', 'assigned')");
        
        // Kembalikan ke enum
        DB::statement("ALTER TABLE aktivitas MODIFY COLUMN tipe ENUM('self', 'assigned') DEFAULT 'self'");
    }
};
