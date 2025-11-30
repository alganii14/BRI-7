@extends('layouts.app')

@section('title', 'Edit Merchant EDC')
@section('page-title', 'Edit Data Merchant EDC')

@section('content')
<style>
    .form-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-group input { width: 100%; padding: 10px 16px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.3s; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    .form-actions { display: flex; gap: 10px; margin-top: 30px; }
</style>

<div class="form-container">
    <form action="{{ route('merchant-savol-edc.update', $merchant->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="kode_kanca">Kode Kanca</label>
            <input type="text" id="kode_kanca" name="kode_kanca" value="{{ old('kode_kanca', $merchant->kode_kanca) }}">
        </div>

        <div class="form-group">
            <label for="nama_kanca">Nama Kanca</label>
            <input type="text" id="nama_kanca" name="nama_kanca" value="{{ old('nama_kanca', $merchant->nama_kanca) }}">
        </div>

        <div class="form-group">
            <label for="kode_uker">Kode Uker</label>
            <input type="text" id="kode_uker" name="kode_uker" value="{{ old('kode_uker', $merchant->kode_uker) }}">
        </div>

        <div class="form-group">
            <label for="nama_uker">Nama Uker</label>
            <input type="text" id="nama_uker" name="nama_uker" value="{{ old('nama_uker', $merchant->nama_uker) }}">
        </div>

        <div class="form-group">
            <label for="nama_merchant">Nama Merchant</label>
            <input type="text" id="nama_merchant" name="nama_merchant" value="{{ old('nama_merchant', $merchant->nama_merchant) }}">
        </div>

        <div class="form-group">
            <label for="norek">No Rekening</label>
            <input type="text" id="norek" name="norek" value="{{ old('norek', $merchant->norek) }}">
        </div>

        <div class="form-group">
            <label for="cifno">CIFNO</label>
            <input type="text" id="cifno" name="cifno" value="{{ old('cifno', $merchant->cifno) }}">
        </div>

        <div class="form-group">
            <label for="jumlah_tid">Jumlah TID</label>
            <input type="text" id="jumlah_tid" name="jumlah_tid" value="{{ old('jumlah_tid', $merchant->jumlah_tid) }}">
        </div>

        <div class="form-group">
            <label for="jumlah_trx">Jumlah TRX</label>
            <input type="text" id="jumlah_trx" name="jumlah_trx" value="{{ old('jumlah_trx', $merchant->jumlah_trx) }}">
        </div>

        <div class="form-group">
            <label for="sales_volume">Sales Volume</label>
            <input type="text" id="sales_volume" name="sales_volume" value="{{ old('sales_volume', $merchant->sales_volume) }}">
        </div>

        <div class="form-group">
            <label for="saldo_posisi">Saldo Posisi</label>
            <input type="text" id="saldo_posisi" name="saldo_posisi" value="{{ old('saldo_posisi', $merchant->saldo_posisi) }}">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update</button>
            <a href="{{ route('merchant-savol-edc.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </form>
</div>
@endsection
