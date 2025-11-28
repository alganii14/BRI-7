@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Data Nasabah Downgrade</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('nasabah-downgrade.update', $nasabahDowngrade->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Cabang Induk</label>
                                <input type="text" name="kode_cabang_induk" class="form-control @error('kode_cabang_induk') is-invalid @enderror" value="{{ old('kode_cabang_induk', $nasabahDowngrade->kode_cabang_induk) }}">
                                @error('kode_cabang_induk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cabang Induk</label>
                                <input type="text" name="cabang_induk" class="form-control @error('cabang_induk') is-invalid @enderror" value="{{ old('cabang_induk', $nasabahDowngrade->cabang_induk) }}">
                                @error('cabang_induk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Uker</label>
                                <input type="text" name="kode_uker" class="form-control @error('kode_uker') is-invalid @enderror" value="{{ old('kode_uker', $nasabahDowngrade->kode_uker) }}">
                                @error('kode_uker')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Kerja</label>
                                <input type="text" name="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" value="{{ old('unit_kerja', $nasabahDowngrade->unit_kerja) }}">
                                @error('unit_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SLP</label>
                                <input type="text" name="slp" class="form-control @error('slp') is-invalid @enderror" value="{{ old('slp', $nasabahDowngrade->slp) }}">
                                @error('slp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">PBO</label>
                                <input type="text" name="pbo" class="form-control @error('pbo') is-invalid @enderror" value="{{ old('pbo', $nasabahDowngrade->pbo) }}">
                                @error('pbo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CIF</label>
                                <input type="text" name="cif" class="form-control @error('cif') is-invalid @enderror" value="{{ old('cif', $nasabahDowngrade->cif) }}">
                                @error('cif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID Prioritas</label>
                                <input type="text" name="id_prioritas" class="form-control @error('id_prioritas') is-invalid @enderror" value="{{ old('id_prioritas', $nasabahDowngrade->id_prioritas) }}">
                                @error('id_prioritas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Nasabah <span class="text-danger">*</span></label>
                            <input type="text" name="nama_nasabah" class="form-control @error('nama_nasabah') is-invalid @enderror" value="{{ old('nama_nasabah', $nasabahDowngrade->nama_nasabah) }}" required>
                            @error('nama_nasabah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening" class="form-control @error('nomor_rekening') is-invalid @enderror" value="{{ old('nomor_rekening', $nasabahDowngrade->nomor_rekening) }}">
                                @error('nomor_rekening')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">AUM</label>
                                <input type="text" name="aum" class="form-control @error('aum') is-invalid @enderror" value="{{ old('aum', $nasabahDowngrade->aum) }}" placeholder="contoh: 14.841.424,00">
                                @error('aum')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('nasabah-downgrade.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
