<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kode_kc');
            $table->string('nama_kc');
            $table->string('kode_uker');
            $table->string('nama_uker');
            $table->string('strategy_pipeline')->nullable();
            $table->string('kategori_strategi')->nullable();
            $table->string('tipe')->default('lama'); // lama = di dalam pipeline, baru = di luar pipeline
            $table->string('nama_nasabah');
            $table->string('norek')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('nasabah_id')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            
            $table->timestamps();
            
            $table->index('kode_kc');
            $table->index('kode_uker');
            $table->index('tanggal');
            $table->index('nasabah_id');
            $table->index('assigned_by');
            
            $table->foreign('nasabah_id')->references('id')->on('nasabahs')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipelines');
    }
};
