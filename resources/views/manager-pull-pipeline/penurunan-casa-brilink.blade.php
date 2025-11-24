@extends('layouts.app')

@section('title', 'Penurunan Casa Brilink')
@section('page-title', 'Penurunan Casa Brilink')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.penurunan-casa-brilink')
])
@endsection
