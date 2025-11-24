@extends('layouts.app')

@section('title', 'Existing Payroll')
@section('page-title', 'Existing Payroll')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.existing-payroll')
])
@endsection
