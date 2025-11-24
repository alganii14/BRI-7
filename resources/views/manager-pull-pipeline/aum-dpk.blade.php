@extends('layouts.app')

@section('title', 'AUM DPK')
@section('page-title', 'AUM>2M DPK<50 juta')

@section('content')
@include('manager-pull-pipeline.partials.read-only-table', [
    'data' => $data,
    'route' => route('manager-pull-pipeline.aum-dpk')
])
@endsection
