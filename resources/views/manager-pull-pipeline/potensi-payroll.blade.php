@extends('layouts.app')

@section('title', 'Potensi Payroll')
@section('page-title', 'Potensi Payroll')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.potensi-payroll')
])
@endsection
