<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brilinks', function (Blueprint $table) {
            $table->id();
            $table->string('kd_cabang')->nullable();
            $table->string('cabang')->nullable();
            $table->string('kd_uker')->nullable();
            $table->string('uker')->nullable();
            $table->string('nama_agen')->nullable();
            $table->string('id_agen')->nullable();
            $table->string('kelas')->nullable();
            $table->string('no_telpon')->nullable();
            $table->string('bidang_usaha')->nullable();
            $table->string('norek')->nullable();
            $table->string('casa')->nullable();
            $table->timestamps();
            
            $table->index('kd_cabang');
            $table->index('kd_uker');
            $table->index('id_agen');
            $table->index('nama_agen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brilinks');
    }
};
