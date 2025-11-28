@extends('layouts.app')

@section('title', 'Nasabah Downgrade')
@section('page-title', 'Pipeline - Nasabah Downgrade')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.nasabah-downgrade')
])
@endsection
