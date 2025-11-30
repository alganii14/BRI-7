@extends('layouts.app')

@section('title', 'Edit Merchant QRIS')
@section('page-title', 'Edit Data Merchant QRIS')

@section('content')
<style>
    .form-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .form-group input, .form-group textarea { width: 100%; padding: 10px 16px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; transition: all 0.3s; }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .btn-secondary { background: #6c757d; color: white; }
    .form-actions { display: flex; gap: 10px; margin-top: 30px; }
</style>

<div class="form-container">
    <form action="{{ route('merchant-savol-qris.update', $merchant->id) }}" method="POST">
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
            <label for="storeid">Store ID</label>
            <input type="text" id="storeid" name="storeid" value="{{ old('storeid', $merchant->storeid) }}">
        </div>

        <div class="form-group">
            <label for="nama_merchant">Nama Merchant</label>
            <input type="text" id="nama_merchant" name="nama_merchant" value="{{ old('nama_merchant', $merchant->nama_merchant) }}">
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3">{{ old('alamat', $merchant->alamat) }}</textarea>
        </div>

        <div class="form-group">
            <label for="no_rek">No Rekening</label>
            <input type="text" id="no_rek" name="no_rek" value="{{ old('no_rek', $merchant->no_rek) }}">
        </div>

        <div class="form-group">
            <label for="cif">CIF</label>
            <input type="text" id="cif" name="cif" value="{{ old('cif', $merchant->cif) }}">
        </div>

        <div class="form-group">
            <label for="akumulasi_sv_total">Akumulasi SV Total</label>
            <input type="text" id="akumulasi_sv_total" name="akumulasi_sv_total" value="{{ old('akumulasi_sv_total', $merchant->akumulasi_sv_total) }}">
        </div>

        <div class="form-group">
            <label for="posisi_sv_total">Posisi SV Total</label>
            <input type="text" id="posisi_sv_total" name="posisi_sv_total" value="{{ old('posisi_sv_total', $merchant->posisi_sv_total) }}">
        </div>

        <div class="form-group">
            <label for="saldo_posisi">Saldo Posisi</label>
            <input type="text" id="saldo_posisi" name="saldo_posisi" value="{{ old('saldo_posisi', $merchant->saldo_posisi) }}">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update</button>
            <a href="{{ route('merchant-savol-qris.index') }}" class="btn btn-secondary">← Kembali</a>
        </div>
    </form>
</div>
@endsection
