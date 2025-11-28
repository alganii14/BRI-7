@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Data Brilink</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('brilink.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Cabang</label>
                                <input type="text" name="kd_cabang" class="form-control @error('kd_cabang') is-invalid @enderror" value="{{ old('kd_cabang') }}">
                                @error('kd_cabang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cabang</label>
                                <input type="text" name="cabang" class="form-control @error('cabang') is-invalid @enderror" value="{{ old('cabang') }}">
                                @error('cabang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Uker</label>
                                <input type="text" name="kd_uker" class="form-control @error('kd_uker') is-invalid @enderror" value="{{ old('kd_uker') }}">
                                @error('kd_uker')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Kerja</label>
                                <input type="text" name="uker" class="form-control @error('uker') is-invalid @enderror" value="{{ old('uker') }}">
                                @error('uker')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Agen <span class="text-danger">*</span></label>
                            <input type="text" name="nama_agen" class="form-control @error('nama_agen') is-invalid @enderror" value="{{ old('nama_agen') }}" required>
                            @error('nama_agen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID Agen</label>
                                <input type="text" name="id_agen" class="form-control @error('id_agen') is-invalid @enderror" value="{{ old('id_agen') }}">
                                @error('id_agen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" class="form-control @error('kelas') is-invalid @enderror" value="{{ old('kelas') }}" placeholder="JAWARA / JURAGAN">
                                @error('kelas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Telpon</label>
                                <input type="text" name="no_telpon" class="form-control @error('no_telpon') is-invalid @enderror" value="{{ old('no_telpon') }}">
                                @error('no_telpon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bidang Usaha</label>
                                <input type="text" name="bidang_usaha" class="form-control @error('bidang_usaha') is-invalid @enderror" value="{{ old('bidang_usaha') }}">
                                @error('bidang_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Rekening</label>
                                <input type="text" name="norek" class="form-control @error('norek') is-invalid @enderror" value="{{ old('norek') }}">
                                @error('norek')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CASA</label>
                                <input type="text" name="casa" class="form-control @error('casa') is-invalid @enderror" value="{{ old('casa') }}" placeholder="contoh: 26.331">
                                @error('casa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="create_aktivitas" value="1" id="create_aktivitas">
                                <label class="form-check-label" for="create_aktivitas">
                                    Buat Aktivitas sekaligus
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('brilink.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
