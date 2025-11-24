@extends('layouts.app')

@section('title', 'Perusahaan Anak')
@section('page-title', 'Perusahaan Anak')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.perusahaan-anak')
])
@endsection
