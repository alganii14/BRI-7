@extends('layouts.app')

@section('title', 'Merchant EDC Savol Besar')
@section('page-title', 'Data Merchant EDC Savol Besar Casa Kecil (Strategi 1)')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.merchant-savol-edc'),
    'columns' => [
        'Kode Kanca' => 'kode_kanca',
        'Nama Kanca' => 'nama_kanca',
        'Kode Uker' => 'kode_uker',
        'Nama Uker' => 'nama_uker',
        'Nama Merchant' => 'nama_merchant',
        'No Rek' => 'norek',
        'CIFNO' => 'cifno',
        'Jumlah TID' => 'jumlah_tid',
        'Jumlah TRX' => 'jumlah_trx',
        'Sales Volume' => 'sales_volume',
        'Saldo Posisi' => 'saldo_posisi',
    ]
])
@endsection
