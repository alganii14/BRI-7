<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brilinks', function (Blueprint $table) {
            $table->date('tanggal_posisi_data')->nullable()->after('casa');
            $table->date('tanggal_upload_data')->nullable()->after('tanggal_posisi_data');
            
            $table->index('tanggal_upload_data');
        });
    }

    public function down(): void
    {
        Schema::table('brilinks', function (Blueprint $table) {
            $table->dropIndex(['tanggal_upload_data']);
            $table->dropColumn(['tanggal_posisi_data', 'tanggal_upload_data']);
        });
    }
};
