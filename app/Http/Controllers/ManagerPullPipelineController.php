<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MerchantSavol;
use App\Models\PenurunanMerchant;
use App\Models\PenurunanCasaBrilink;
use App\Models\QlolaNonDebitur;
use App\Models\NonDebiturVolBesar;
use App\Models\QlolaNonaktif;
use App\Models\UserAktifCasaKecil;
use App\Models\OptimalisasiBusinessCluster;
use App\Models\ExistingPayroll;
use App\Models\PotensiPayroll;
use App\Models\PerusahaanAnak;
use App\Models\PenurunanPrioritasRitelMikro;
use App\Models\AumDpk;
use App\Models\Strategi8;
use App\Models\Layering;

class ManagerPullPipelineController extends Controller
{
    /**
     * Check if user can access pull pipeline (Manager or RMFT)
     */
    private function canAccessPullPipeline($user)
    {
        return $user->isManager() || $user->isRMFT();
    }

    /**
     * Get kode_kanca for user (handles RMFT case where kode_kanca might be in rmftData)
     */
    private function getUserKodeKanca($user)
    {
        // If user already has kode_kanca, use it
        if ($user->kode_kanca) {
            return $user->kode_kanca;
        }
        
        // For RMFT, try to get kode_kanca from rmftData relation
        if ($user->isRMFT() && $user->rmft_id) {
            $rmftData = $user->rmftData;
            if ($rmftData) {
                // Try from uker relation first
                if ($rmftData->uker_id) {
                    $uker = \App\Models\Uker::find($rmftData->uker_id);
                    if ($uker && $uker->kode_kanca) {
                        return $uker->kode_kanca;
                    }
                }
                // Try to find kode_kanca by kanca name
                if ($rmftData->kanca) {
                    // Check if kanca is already a code (numeric)
                    if (is_numeric($rmftData->kanca)) {
                        return $rmftData->kanca;
                    }
                    // Otherwise, search by kanca name in uker table
                    $uker = \App\Models\Uker::where('kanca', 'LIKE', '%' . $rmftData->kanca . '%')->first();
                    if ($uker && $uker->kode_kanca) {
                        return $uker->kode_kanca;
                    }
                }
            }
        }
        
        return null;
    }

    /**
     * Apply date filters (year and month) to query
     */
    private function applyDateFilters($query, Request $request)
    {
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        
        return $query;
    }

    /**
     * Merchant Savol - Strategi 1
     */
    public function merchantSavol(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = MerchantSavol::where('kode_kanca', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('norekening', 'like', "%{$search}%")
                  ->orWhere('nama_merchant', 'like', "%{$search}%")
                  ->orWhere('tid_store_id', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_merchant')->paginate(20);
        
        return view('manager-pull-pipeline.merchant-savol', compact('data'));
    }

    /**
     * Penurunan Merchant - Strategi 1
     */
    public function penurunanMerchant(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = PenurunanMerchant::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.penurunan-merchant', compact('data'));
    }

    /**
     * Penurunan Casa Brilink - Strategi 1
     */
    public function penurunanCasaBrilink(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = PenurunanCasaBrilink::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.penurunan-casa-brilink', compact('data'));
    }

    /**
     * Qlola Non Debitur - Strategi 1
     */
    public function qlolaNonDebitur(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = QlolaNonDebitur::where('kode_kanca', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('norek', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('id')->paginate(20);
        
        return view('manager-pull-pipeline.qlola-non-debitur', compact('data'));
    }

    /**
     * Non Debitur Vol Besar - Strategi 1
     */
    public function nonDebiturVolBesar(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = NonDebiturVolBesar::where('kode_kanca', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('norek', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('id')->paginate(20);
        
        return view('manager-pull-pipeline.non-debitur-vol-besar', compact('data'));
    }

    /**
     * Qlola Nonaktif - Strategi 2
     */
    public function qlolaNonaktif(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = QlolaNonaktif::where('kode_kanca', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('norek', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('id')->paginate(20);
        
        return view('manager-pull-pipeline.qlola-nonaktif', compact('data'));
    }

    /**
     * User Aktif Casa Kecil - Strategi 2
     */
    public function userAktifCasaKecil(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = UserAktifCasaKecil::where('kode_kanca', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('norek', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('id')->paginate(20);
        
        return view('manager-pull-pipeline.user-aktif-casa-kecil', compact('data'));
    }

    /**
     * Optimalisasi Business Cluster - Strategi 3
     */
    public function optimalisasiBusinessCluster(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = OptimalisasiBusinessCluster::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_usaha_pusat_bisnis', 'like', "%{$search}%")
                  ->orWhere('nama_tenaga_pemasar', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_usaha_pusat_bisnis')->paginate(20);
        
        return view('manager-pull-pipeline.optimalisasi-business-cluster', compact('data'));
    }

    /**
     * Existing Payroll - Strategi 4
     */
    public function existingPayroll(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = ExistingPayroll::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('corporate_code', 'like', "%{$search}%")
                  ->orWhere('nama_perusahaan', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_perusahaan')->paginate(20);
        
        return view('manager-pull-pipeline.existing-payroll', compact('data'));
    }

    /**
     * Potensi Payroll - Strategi 4
     */
    public function potensiPayroll(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = PotensiPayroll::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('perusahaan', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('perusahaan')->paginate(20);
        
        return view('manager-pull-pipeline.potensi-payroll', compact('data'));
    }

    /**
     * Perusahaan Anak - Strategi 6
     */
    public function perusahaanAnak(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = PerusahaanAnak::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_perusahaan_anak', 'like', "%{$search}%")
                  ->orWhere('nama_partner_vendor', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_perusahaan_anak')->paginate(20);
        
        return view('manager-pull-pipeline.perusahaan-anak', compact('data'));
    }

    /**
     * Penurunan Prioritas Ritel Mikro - Strategi 7
     */
    public function penurunanPrioritasRitelMikro(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = PenurunanPrioritasRitelMikro::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.penurunan-prioritas-ritel-mikro', compact('data'));
    }

    /**
     * AUM DPK - Strategi 7
     */
    public function aumDpk(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = AumDpk::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.aum-dpk', compact('data'));
    }

    /**
     * Strategi 8
     */
    public function strategi8(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = Strategi8::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.strategi8', compact('data'));
    }

    /**
     * Layering
     */
    public function layering(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = Layering::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('no_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.layering', compact('data'));
    }
    
    /**
     * Nasabah Downgrade
     */
    public function nasabahDowngrade(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = \App\Models\NasabahDowngrade::where('kode_cabang_induk', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%")
                  ->orWhere('cabang_induk', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_nasabah')->paginate(20);
        
        return view('manager-pull-pipeline.nasabah-downgrade', compact('data'));
    }

    /**
     * Brilink Saldo Kurang - Strategi 7
     */
    public function brilinkSaldoKurang(Request $request)
    {
        $user = Auth::user();
        
        if (!$this->canAccessPullPipeline($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $kodeKanca = $this->getUserKodeKanca($user);
        $query = \App\Models\Brilink::where('kd_cabang', $kodeKanca);
        
        // Apply date filters
        $this->applyDateFilters($query, $request);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('norek', 'like', "%{$search}%")
                  ->orWhere('nama_agen', 'like', "%{$search}%")
                  ->orWhere('id_agen', 'like', "%{$search}%")
                  ->orWhere('cabang', 'like', "%{$search}%");
            });
        }
        
        $data = $query->orderBy('nama_agen')->paginate(20);
        
        return view('manager-pull-pipeline.brilink-saldo-kurang', compact('data'));
    }
}
