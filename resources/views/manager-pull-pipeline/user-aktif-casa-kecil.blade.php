@extends('layouts.app')

@section('title', 'User Aktif Casa Kecil')
@section('page-title', 'User Aktif Casa Kecil')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.user-aktif-casa-kecil')
])
@endsection
