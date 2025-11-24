@extends('layouts.app')

@section('title', 'Optimalisasi Business Cluster')
@section('page-title', 'Optimalisasi Business Cluster')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.optimalisasi-business-cluster')
])
@endsection
