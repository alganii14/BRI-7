@extends('layouts.app')

@section('title', 'Layering')
@section('page-title', 'Layering Wingback')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.layering')
])
@endsection
