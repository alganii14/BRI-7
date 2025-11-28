@extends('layouts.app')

@section('title', 'Dashboard RMFT')
@section('page-title', 'Dashboard RMFT')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    /* Responsive Dashboard Styles */
    @media (max-width: 768px) {
        .page-header {
            padding: 6px 0 !important;
        }
        .page-header h2 {
            font-size: 15px !important;
            margin-bottom: 2px !important;
        }
        .page-header p {
            font-size: 10px !important;
        }
        .dashboard-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .card {
            padding: 10px !important;
            margin-bottom: 10px !important;
        }
        .card h3 {
            font-size: 13px !important;
            margin-bottom: 6px !important;
        }
        .card p {
            font-size: 11px !important;
        }
        .card p:first-of-type {
            font-size: 26px !important;
            margin: 6px 0 !important;
        }
        .filter-grid {
            grid-template-columns: 1fr !important;
            gap: 6px !important;
        }
        .summary-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }
        .summary-grid > div {
            padding: 12px !important;
        }
        .summary-grid > div > div:first-child {
            font-size: 11px !important;
            margin-bottom: 4px !important;
        }
        .summary-grid > div > div:last-child {
            font-size: 18px !important;
        }
        .form-group {
            margin-bottom: 0 !important;
        }
        .form-group label {
            font-size: 11px !important;
            margin-bottom: 3px !important;
        }
        .form-control, select.form-control, input.form-control {
            padding: 6px 8px !important;
            font-size: 13px !important;
        }
        .btn {
            width: 100%;
            margin-bottom: 6px;
            padding: 7px 14px !important;
            font-size: 12px !important;
        }
        .card > div {
            padding: 10px !important;
        }
        #targetRealisasiChart {
            height: 220px !important;
        }
        .card > div > div[style*="position: relative; height: 350px"] {
            height: 220px !important;
        }
        svg {
            width: 12px !important;
            height: 12px !important;
        }
        .filter-card {
            margin-bottom: 8px !important;
        }
        .dashboard-grid .card {
            padding: 12px !important;
        }
        .filter-card-inner {
            padding: 8px !important;
        }
        .filter-card-inner > div:first-child {
            margin-bottom: 6px !important;
        }
        .filter-card-inner > div:first-child svg {
            width: 14px !important;
            height: 14px !important;
            margin-right: 4px !important;
        }
        .filter-card-inner h3 {
            font-size: 13px !important;
        }
        .filter-card-inner .form-group {
            margin-bottom: 6px !important;
        }
        .filter-card-inner .btn {
            padding: 7px 12px !important;
            font-size: 12px !important;
        }
        .filter-card-inner form > div {
            gap: 6px !important;
            margin-bottom: 6px !important;
        }
        .filter-card-inner form > div:last-child {
            margin-bottom: 0 !important;
            gap: 4px !important;
        }
        .dashboard-grid .card {
            padding: 14px !important;
        }
        .card p:first-of-type {
            margin: 8px 0 !important;
        }
    }
    @media (min-width: 769px) and (max-width: 1024px) {
        .filter-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .dashboard-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .summary-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    /* iPhone 12 Pro specific */
    @media (min-width: 381px) and (max-width: 430px) {
        .filter-grid,
        .dashboard-grid,
        .summary-grid {
            grid-template-columns: 1fr !important;
        }
        .filter-card-inner {
            padding: 8px !important;
        }
        .filter-card-inner > div:first-child {
            margin-bottom: 6px !important;
        }
        .filter-card-inner h3 {
            font-size: 13px !important;
        }
        .form-group {
            margin-bottom: 6px !important;
        }
        .form-group label {
            font-size: 11px !important;
            margin-bottom: 3px !important;
        }
        .form-control, select.form-control, input.form-control {
            padding: 6px 8px !important;
            font-size: 13px !important;
        }
        .filter-card-inner .btn {
            padding: 7px 12px !important;
            font-size: 12px !important;
        }
        .filter-card-inner form > div {
            gap: 6px !important;
            margin-bottom: 6px !important;
        }
        .card {
            padding: 10px !important;
        }
        .dashboard-grid .card {
            padding: 12px !important;
        }
    }
    /* iPhone SE and smaller devices */
    @media (max-width: 380px) {
        .page-header h2 {
            font-size: 14px !important;
        }
        .page-header p {
            font-size: 9px !important;
        }
        .filter-card-inner {
            padding: 6px !important;
        }
        .filter-card-inner h3 {
            font-size: 12px !important;
        }
        .filter-card-inner .form-group {
            margin-bottom: 5px !important;
        }
        .form-group label {
            font-size: 10px !important;
            margin-bottom: 2px !important;
        }
        .form-control, select.form-control, input.form-control {
            padding: 5px 6px !important;
            font-size: 12px !important;
        }
        .filter-card-inner .btn {
            padding: 6px 10px !important;
            font-size: 11px !important;
        }
        .card {
            padding: 8px !important;
        }
        .dashboard-grid .card {
            padding: 10px !important;
        }
        .card h3 {
            font-size: 12px !important;
        }
        .card p:first-of-type {
            font-size: 22px !important;
        }
        .summary-grid > div {
            padding: 10px !important;
        }
        .summary-grid > div > div:first-child {
            font-size: 10px !important;
        }
        .summary-grid > div > div:last-child {
            font-size: 16px !important;
        }
        .filter-card-inner form > div {
            gap: 5px !important;
            margin-bottom: 5px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h2>Dashboard RMFT - {{ Auth::user()->name }}</h2>
    <p>Aktivitas dan Target Pipeline Anda - {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }}</p>
</div>

<!-- Filter Periode -->
<div class="card filter-card" style="margin-bottom: 24px; background: linear-gradient(135deg, #0066AE 0%, #004A7F 100%); border: none; box-shadow: 0 8px 24px rgba(0, 102, 174, 0.25);">
    <div class="filter-card-inner" style="background: white; padding: 24px; border-radius: 8px;">
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0066AE" viewBox="0 0 16 16" style="margin-right: 12px;">
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
            </svg>
            <h3 style="margin: 0; color: #0066AE; font-weight: 700; font-size: 20px;">Filter Periode</h3>
        </div>
        <form method="GET" action="{{ route('dashboard') }}">
            <div class="filter-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="month" style="display: block; margin-bottom: 8px; color: #004A7F; font-weight: 600; font-size: 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 4px; vertical-align: text-top;">
                            <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-3.5-7h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                        Bulan
                    </label>
                    <select name="month" id="month" class="form-control" style="border: 2px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; font-size: 15px; transition: all 0.3s ease; background: white;" onfocus="this.style.borderColor='#0066AE'; this.style.boxShadow='0 0 0 3px rgba(0, 102, 174, 0.1)'" onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="year" style="display: block; margin-bottom: 8px; color: #004A7F; font-weight: 600; font-size: 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 4px; vertical-align: text-top;">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/>
                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>
                        Tahun
                    </label>
                    <select name="year" id="year" class="form-control" style="border: 2px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; font-size: 15px; transition: all 0.3s ease; background: white;" onfocus="this.style.borderColor='#0066AE'; this.style.boxShadow='0 0 0 3px rgba(0, 102, 174, 0.1)'" onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                        @php
                            $currentYear = \Carbon\Carbon::now()->year;
                            $startYear = $currentYear;
                            $endYear = $currentYear + 1;
                        @endphp
                        @for($y = $startYear; $y <= $endYear; $y++)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="jenis_simpanan" style="display: block; margin-bottom: 8px; color: #004A7F; font-weight: 600; font-size: 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 4px; vertical-align: text-top;">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                        </svg>
                        Jenis Simpanan
                    </label>
                    <select name="jenis_simpanan" id="jenis_simpanan" class="form-control" style="border: 2px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; font-size: 15px; transition: all 0.3s ease; background: white;" onfocus="this.style.borderColor='#0066AE'; this.style.boxShadow='0 0 0 3px rgba(0, 102, 174, 0.1)'" onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                        <option value="">Semua Jenis</option>
                        <option value="Tabungan" {{ ($selectedJenisSimpanan ?? '') == 'Tabungan' ? 'selected' : '' }}>Tabungan</option>
                        <option value="Giro" {{ ($selectedJenisSimpanan ?? '') == 'Giro' ? 'selected' : '' }}>Giro</option>
                        <option value="Deposito" {{ ($selectedJenisSimpanan ?? '') == 'Deposito' ? 'selected' : '' }}>Deposito</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="start_date" style="display: block; margin-bottom: 8px; color: #004A7F; font-weight: 600; font-size: 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 4px; vertical-align: text-top;">
                            <path d="M6.445 11.688V6.354h-.633A12.6 12.6 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61h.675zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82h-.684zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        Dari Tanggal
                    </label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" style="border: 2px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; font-size: 15px; transition: all 0.3s ease; background: white;" onfocus="this.style.borderColor='#0066AE'; this.style.boxShadow='0 0 0 3px rgba(0, 102, 174, 0.1)'" onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="end_date" style="display: block; margin-bottom: 8px; color: #004A7F; font-weight: 600; font-size: 14px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 4px; vertical-align: text-top;">
                            <path d="M6.445 11.688V6.354h-.633A12.6 12.6 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61h.675zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82h-.684zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        Sampai Tanggal
                    </label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" style="border: 2px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; font-size: 15px; transition: all 0.3s ease; background: white;" onfocus="this.style.borderColor='#0066AE'; this.style.boxShadow='0 0 0 3px rgba(0, 102, 174, 0.1)'" onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0066AE 0%, #004A7F 100%); border: none; padding: 12px 28px; font-weight: 700; box-shadow: 0 4px 14px rgba(0, 102, 174, 0.35); transition: all 0.3s ease; border-radius: 8px; font-size: 15px; color: white;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 102, 174, 0.45)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(0, 102, 174, 0.35)'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 16 16" style="margin-right: 8px; vertical-align: text-bottom;">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                    Filter
                </button>
                @if($selectedMonth != \Carbon\Carbon::now()->month || $selectedYear != \Carbon\Carbon::now()->year || $startDate || $endDate || ($selectedJenisSimpanan ?? null))
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="background: white; border: 2px solid #0066AE; color: #0066AE; padding: 12px 28px; font-weight: 700; box-shadow: 0 4px 14px rgba(0, 102, 174, 0.15); transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; border-radius: 8px; font-size: 15px;" onmouseover="this.style.background='#F0F8FF'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0, 102, 174, 0.25)'" onmouseout="this.style.background='white'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(0, 102, 174, 0.15)'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <div class="card">
        <h3 style="color: #667eea;">Aktivitas Bulan Ini</h3>
        <p style="font-size: 36px; font-weight: 700; color: #333; margin-top: 12px;">{{ number_format($totalAktivitasBulanIni) }}</p>
        <p style="font-size: 13px; color: #666; margin-top: 8px;">Total aktivitas {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }}</p>
    </div>
    
    <div class="card">
        <h3 style="color: #4caf50;">Tercapai</h3>
        <p style="font-size: 36px; font-weight: 700; color: #333; margin-top: 12px;">{{ number_format($totalTercapai) }}</p>
        <p style="font-size: 13px; color: #666; margin-top: 8px;">Aktivitas terealisasi</p>
    </div>
    
    <div class="card">
        <h3 style="color: #f44336;">Tidak Tercapai</h3>
        <p style="font-size: 36px; font-weight: 700; color: #333; margin-top: 12px;">{{ number_format($totalTidakTercapai) }}</p>
        <p style="font-size: 13px; color: #666; margin-top: 8px;">Belum terealisasi</p>
    </div>
    
    <div class="card">
        <h3 style="color: #764ba2;">Lebih dari Target</h3>
        <p style="font-size: 36px; font-weight: 700; color: #333; margin-top: 12px;">{{ number_format($totalLebih) }}</p>
        <p style="font-size: 13px; color: #666; margin-top: 8px;">Melebihi target</p>
    </div>
</div>

<div class="card">
    <h3>Target dan Realisasi</h3>
    <div style="padding: 20px;">
        @php
            // Ambil data per hari dalam bulan terpilih atau range tanggal
            $rmftId = Auth::user()->rmftData?->id;
            $filterJenisSimpanan = $selectedJenisSimpanan ?? null;
            
            // Tentukan range tanggal
            if ($startDate && $endDate) {
                $startDateCarbon = \Carbon\Carbon::parse($startDate);
                $endDateCarbon = \Carbon\Carbon::parse($endDate);
                $dateRange = \Carbon\CarbonPeriod::create($startDateCarbon, $endDateCarbon);
            } else {
                $currentMonth = $selectedMonth;
                $currentYear = $selectedYear;
                $daysInMonth = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->daysInMonth;
                $startDateCarbon = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                $endDateCarbon = \Carbon\Carbon::create($currentYear, $currentMonth, $daysInMonth);
                $dateRange = \Carbon\CarbonPeriod::create($startDateCarbon, $endDateCarbon);
            }
            
            $dailyData = [];
            foreach ($dateRange as $date) {
                $targetQuery = \App\Models\Aktivitas::where('rmft_id', $rmftId)
                    ->whereDate('tanggal', $date);
                if ($filterJenisSimpanan) {
                    $targetQuery->where('jenis_simpanan', $filterJenisSimpanan);
                }
                // Ambil data dan konversi manual karena rp_jumlah adalah string
                $targetData = $targetQuery->get();
                $targetPerDay = 0;
                foreach ($targetData as $item) {
                    // Hapus "Rp", ".", dan spasi, lalu konversi ke integer
                    $cleanValue = preg_replace('/[^0-9]/', '', $item->rp_jumlah);
                    $targetPerDay += intval($cleanValue);
                }
                
                $realisasiQuery = \App\Models\Aktivitas::where('rmft_id', $rmftId)
                    ->whereDate('tanggal', $date)
                    ->whereIn('status_realisasi', ['tercapai', 'lebih']);
                if ($filterJenisSimpanan) {
                    $realisasiQuery->where('jenis_simpanan', $filterJenisSimpanan);
                }
                // Ambil data dan konversi manual karena nominal_realisasi adalah string
                $realisasiData = $realisasiQuery->get();
                $realisasiPerDay = 0;
                foreach ($realisasiData as $item) {
                    // Hapus "Rp", ".", dan spasi, lalu konversi ke integer
                    $cleanValue = preg_replace('/[^0-9]/', '', $item->nominal_realisasi);
                    $realisasiPerDay += intval($cleanValue);
                }
                
                $dailyData[] = [
                    'day' => $date->format('d/m'),
                    'target' => $targetPerDay,
                    'realisasi' => $realisasiPerDay
                ];
            }
            
            $totalTarget = array_sum(array_column($dailyData, 'target'));
            $totalRealisasi = array_sum(array_column($dailyData, 'realisasi'));
            $persenRealisasi = $totalTarget > 0 ? round(($totalRealisasi / $totalTarget) * 100, 1) : 0;
        @endphp
        
        <div class="summary-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; color: white;">
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">Total Target</div>
                <div style="font-size: 24px; font-weight: 700;">Rp {{ number_format($totalTarget / 1000000, 0) }} juta</div>
            </div>
            
            <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #4caf50 0%, #45a049 100%); border-radius: 8px; color: white;">
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">Total Realisasi</div>
                <div style="font-size: 24px; font-weight: 700;">Rp {{ number_format($totalRealisasi / 1000000, 0) }} juta</div>
            </div>
            
            <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #28a745 0%, #218838 100%); border-radius: 8px; color: white;">
                <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">Pipeline Tervalidasi</div>
                <div style="font-size: 24px; font-weight: 700;">Rp {{ number_format($totalPipelineValidasi / 1000000, 0) }} juta</div>
            </div>
        </div>
        
        <!-- Line Chart -->
        <div style="position: relative; height: 350px; margin-top: 24px;">
            <canvas id="targetRealisasiChart"></canvas>
        </div>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('targetRealisasiChart').getContext('2d');
    
    const chartData = {!! json_encode($dailyData) !!};
    const labels = chartData.map(d => d.day);
    const targetData = chartData.map(d => d.target / 1000000); // Convert to millions
    const realisasiData = chartData.map(d => d.realisasi / 1000000);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Target (Juta Rp)',
                    data: targetData,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(102, 126, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Realisasi (Juta Rp)',
                    data: realisasiData,
                    borderColor: 'rgb(76, 175, 80)',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(76, 175, 80)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        title: function(context) {
                            @if($startDate && $endDate)
                                return 'Tanggal ' + context[0].label;
                            @else
                                return 'Tanggal ' + context[0].label + ' ' + '{{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat("F Y") }}';
                            @endif
                        },
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Rp ' + context.parsed.y.toFixed(0) + ' juta';
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tanggal',
                        font: {
                            size: 13,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nominal (Juta Rupiah)',
                        font: {
                            size: 13,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toFixed(0) + ' juta';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            }
        }
    });
});
</script>
@endsection
