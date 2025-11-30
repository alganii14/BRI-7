@extends('layouts.app')

@section('title', 'Merchant QRIS Savol Besar')
@section('page-title', 'Data Merchant QRIS Savol Besar Casa Kecil (Strategi 1)')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.merchant-savol-qris'),
    'columns' => [
        'Kode Kanca' => 'kode_kanca',
        'Nama Kanca' => 'nama_kanca',
        'Kode Uker' => 'kode_uker',
        'Nama Uker' => 'nama_uker',
        'Store ID' => 'storeid',
        'Nama Merchant' => 'nama_merchant',
        'No Rek' => 'no_rek',
        'CIF' => 'cif',
        'Akumulasi SV' => 'akumulasi_sv_total',
        'Posisi SV' => 'posisi_sv_total',
        'Saldo Posisi' => 'saldo_posisi',
    ]
])
@endsection
