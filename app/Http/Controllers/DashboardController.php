<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\RMFT;
use App\Models\Uker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        } elseif ($user->isManager()) {
            return $this->managerDashboard($request);
        } else {
            return $this->rmftDashboard($request);
        }
    }
    
    /**
     * Dashboard RMFT - Hanya aktivitas sendiri
     */
    private function rmftDashboard(Request $request)
    {
        $user = Auth::user();
        $rmft = $user->rmftData;
        
        // Filter bulan dan tahun
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        if (!$rmft) {
            return view('dashboard.rmft', [
                'totalAktivitasBulanIni' => 0,
                'aktivitasHariIni' => 0,
                'totalTercapai' => 0,
                'totalTidakTercapai' => 0,
                'totalLebih' => 0,
                'aktivitasTerbaru' => collect([]),
                'selectedMonth' => $selectedMonth,
                'selectedYear' => $selectedYear,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        }
        
        // Query aktivitas bulan terpilih untuk RMFT ini
        $aktivitasBulanIni = Aktivitas::where('rmft_id', $rmft->id);
        
        // Filter berdasarkan tanggal range jika ada
        if ($startDate && $endDate) {
            $aktivitasBulanIni->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
        } else {
            $aktivitasBulanIni->whereYear('tanggal', $selectedYear)
                ->whereMonth('tanggal', $selectedMonth);
        }
        
        $totalAktivitasBulanIni = $aktivitasBulanIni->count();
        
        // Aktivitas hari ini
        $aktivitasHariIni = Aktivitas::where('rmft_id', $rmft->id)
            ->whereDate('tanggal', Carbon::today())
            ->count();
        
        // Status realisasi bulan terpilih
        $totalTercapai = (clone $aktivitasBulanIni)->where('status_realisasi', 'tercapai')->count();
        $totalTidakTercapai = (clone $aktivitasBulanIni)->where('status_realisasi', 'tidak_tercapai')->count();
        $totalLebih = (clone $aktivitasBulanIni)->where('status_realisasi', 'lebih')->count();
        
        // Aktivitas terbaru (5 terakhir)
        $aktivitasTerbaru = Aktivitas::where('rmft_id', $rmft->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Pipeline Tervalidasi - filter berdasarkan KC, nama RMFT, dan tanggal
        $rekapQuery = \App\Models\Rekap::where('nama_kc', $rmft->kanca)
            ->where('nama_rmft', $rmft->completename);
        
        // Filter berdasarkan tanggal range jika ada
        if ($startDate && $endDate) {
            $rekapQuery->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
        } else {
            $rekapQuery->whereYear('tanggal', $selectedYear)
                ->whereMonth('tanggal', $selectedMonth);
        }
        
        $totalPipelineValidasi = $rekapQuery->sum('validasi');
        
        return view('dashboard.rmft', compact(
            'totalAktivitasBulanIni',
            'aktivitasHariIni',
            'totalTercapai',
            'totalTidakTercapai',
            'totalLebih',
            'aktivitasTerbaru',
            'totalPipelineValidasi',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Dashboard Manager - Dengan filter RMFT
     */
    private function managerDashboard(Request $request)
    {
        $user = Auth::user();
        $selectedRmftId = $request->get('rmft_id');
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Ambil KC Manager yang sedang login
        $managerKc = $user->nama_kanca;
        
        // Ambil RMFT hanya dari KC Manager tersebut
        $rmftList = RMFT::where('kanca', $managerKc)
            ->orderBy('completename')
            ->get();
        
        // Query dasar aktivitas bulan terpilih - filter berdasarkan KC Manager
        $query = Aktivitas::where('nama_kc', $managerKc);
        
        // Filter berdasarkan tanggal range jika ada
        if ($startDate && $endDate) {
            $query->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
        } else {
            $query->whereYear('tanggal', $selectedYear)
                ->whereMonth('tanggal', $selectedMonth);
        }
        
        // Filter berdasarkan RMFT jika dipilih
        if ($selectedRmftId) {
            $query->where('rmft_id', $selectedRmftId);
        }
        
        $totalAktivitasBulanIni = $query->count();
        
        // Aktivitas hari ini - filter berdasarkan KC Manager
        $aktivitasHariIniQuery = Aktivitas::where('nama_kc', $managerKc)
            ->whereDate('tanggal', Carbon::today());
        if ($selectedRmftId) {
            $aktivitasHariIniQuery->where('rmft_id', $selectedRmftId);
        }
        $aktivitasHariIni = $aktivitasHariIniQuery->count();
        
        // Status realisasi bulan terpilih
        $queryTercapai = clone $query;
        $queryTidakTercapai = clone $query;
        $queryLebih = clone $query;
        
        $totalTercapai = $queryTercapai->where('status_realisasi', 'tercapai')->count();
        $totalTidakTercapai = $queryTidakTercapai->where('status_realisasi', 'tidak_tercapai')->count();
        $totalLebih = $queryLebih->where('status_realisasi', 'lebih')->count();
        
        // Aktivitas terbaru - filter berdasarkan KC Manager
        $aktivitasTerbaruQuery = Aktivitas::where('nama_kc', $managerKc)
            ->orderBy('created_at', 'desc');
        if ($selectedRmftId) {
            $aktivitasTerbaruQuery->where('rmft_id', $selectedRmftId);
        }
        $aktivitasTerbaru = $aktivitasTerbaruQuery->limit(5)->get();
        
        // Pipeline Tervalidasi - filter berdasarkan KC Manager, RMFT, dan tanggal
        $rekapQuery = \App\Models\Rekap::where('nama_kc', $managerKc);
        if ($selectedRmftId) {
            $selectedRmft = RMFT::find($selectedRmftId);
            if ($selectedRmft) {
                $rekapQuery->where('nama_rmft', $selectedRmft->completename);
            }
        }
        
        // Filter berdasarkan tanggal range jika ada
        if ($startDate && $endDate) {
            $rekapQuery->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
        } else {
            $rekapQuery->whereYear('tanggal', $selectedYear)
                ->whereMonth('tanggal', $selectedMonth);
        }
        
        $totalPipelineValidasi = $rekapQuery->sum('validasi');
        
        return view('dashboard.manager', compact(
            'totalAktivitasBulanIni',
            'aktivitasHariIni',
            'totalTercapai',
            'totalTidakTercapai',
            'totalLebih',
            'aktivitasTerbaru',
            'totalPipelineValidasi',
            'rmftList',
            'selectedRmftId',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Dashboard Admin - Dengan filter KC dan RMFT
     */
    private function adminDashboard(Request $request)
    {
        $selectedKc = $request->get('kode_kc');
        $selectedRmftId = $request->get('rmft_id');
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Ambil semua KC untuk dropdown
        $kcList = Uker::select('kode_kanca as kode_kc', 'kanca as nama_kc')
            ->whereNotNull('kode_kanca')
            ->whereNotNull('kanca')
            ->groupBy('kode_kanca', 'kanca')
            ->orderBy('kanca')
            ->get();
        
        // Get nama_kc from selected kode_kc
        $selectedNamaKc = null;
        if ($selectedKc) {
            $ukerData = Uker::where('kode_kanca', $selectedKc)->first();
            $selectedNamaKc = $ukerData ? $ukerData->kanca : null;
        }
        
        // Ambil RMFT berdasarkan KC yang dipilih
        $rmftQuery = RMFT::orderBy('completename');
        if ($selectedNamaKc) {
            $rmftQuery->where('kanca', $selectedNamaKc);
        }
        $rmftList = $rmftQuery->get();
        
        // Query dasar aktivitas bulan terpilih
        $query = Aktivitas::query();
        
        // Filter berdasarkan tanggal range jika ada
        if ($startDate && $endDate) {
            $query->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
        } else {
            $query->whereYear('tanggal', $selectedYear)
                ->whereMonth('tanggal', $selectedMonth);
        }
        
        // Filter berdasarkan KC
        if ($selectedKc) {
            $query->where('kode_kc', $selectedKc);
        }
        
        // Filter berdasarkan RMFT
        if ($selectedRmftId) {
            $query->where('rmft_id', $selectedRmftId);
        }
        
        $totalAktivitasBulanIni = $query->count();
        
        // Aktivitas hari ini
        $aktivitasHariIniQuery = Aktivitas::whereDate('tanggal', Carbon::today());
        if ($selectedKc) {
            $aktivitasHariIniQuery->where('kode_kc', $selectedKc);
        }
        if ($selectedRmftId) {
            $aktivitasHariIniQuery->where('rmft_id', $selectedRmftId);
        }
        $aktivitasHariIni = $aktivitasHariIniQuery->count();
        
        // Status realisasi bulan terpilih
        $queryTercapai = clone $query;
        $queryTidakTercapai = clone $query;
        $queryLebih = clone $query;
        
        $totalTercapai = $queryTercapai->where('status_realisasi', 'tercapai')->count();
        $totalTidakTercapai = $queryTidakTercapai->where('status_realisasi', 'tidak_tercapai')->count();
        $totalLebih = $queryLebih->where('status_realisasi', 'lebih')->count();
        
        // Aktivitas terbaru
        $aktivitasTerbaruQuery = Aktivitas::orderBy('created_at', 'desc');
        if ($selectedKc) {
            $aktivitasTerbaruQuery->where('kode_kc', $selectedKc);
        }
        if ($selectedRmftId) {
            $aktivitasTerbaruQuery->where('rmft_id', $selectedRmftId);
        }
        $aktivitasTerbaru = $aktivitasTerbaruQuery->limit(5)->get();
        
        // Pipeline Tervalidasi - filter berdasarkan KC, RMFT, dan tanggal yang dipilih
        $rekapQuery = \App\Models\Rekap::query();
        if ($selectedNamaKc) {
            $rekapQuery->where('nama_kc', $selectedNamaKc);
        }
        if ($selectedRmftId) {
            $selectedRmft = RMFT::find($selectedRmftId);
            if ($selectedRmft) {
                $rekapQuery->where('nama_rmft', $selectedRmft->completename);
            }
        }
        
        // Filter berdasarkan tanggal range jika ada
        if ($startDate && $endDate) {
            $rekapQuery->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
        } else {
            $rekapQuery->whereYear('tanggal', $selectedYear)
                ->whereMonth('tanggal', $selectedMonth);
        }
        
        $totalPipelineValidasi = $rekapQuery->sum('validasi');
        
        return view('dashboard.admin', compact(
            'totalAktivitasBulanIni',
            'aktivitasHariIni',
            'totalTercapai',
            'totalTidakTercapai',
            'totalLebih',
            'aktivitasTerbaru',
            'totalPipelineValidasi',
            'kcList',
            'rmftList',
            'selectedKc',
            'selectedRmftId',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate'
        ));
    }
}
