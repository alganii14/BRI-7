<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UkerController;
use App\Http\Controllers\RMFTController;
use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenurunanCasaBrilinkController;
use App\Http\Controllers\PenurunanMerchantController;
use App\Http\Controllers\PenurunanPrioritasRitelMikroController;
use App\Http\Controllers\MerchantSavolController;
use App\Http\Controllers\QlolaNonDebiturController;
use App\Http\Controllers\NonDebiturVolBesarController;
use App\Http\Controllers\QlolaNonaktifController;
use App\Http\Controllers\ManagerPipelineController;
use App\Http\Controllers\ManagerPullPipelineController;
use App\Http\Controllers\RencanaAktivitasController;
use App\Http\Controllers\OptimalisasiBusinessClusterController;
use App\Http\Controllers\Strategi8Controller;
use App\Http\Controllers\ExistingPayrollController;
use App\Http\Controllers\PotensiPayrollController;
use App\Http\Controllers\PerusahaanAnakController;
use App\Http\Controllers\AumDpkController;
use App\Http\Controllers\UserAktifCasaKecilController;
use App\Http\Controllers\LayeringController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\NasabahDowngradeController;
use App\Http\Controllers\BrilinkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PipelineController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware(['auth', 'check.password.changed', 'update.last.activity'])->group(function () {
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Bulk Sick/Leave Routes - RMFT only (MUST BE BEFORE resource route)
    Route::get('aktivitas/bulk-sick-leave', [AktivitasController::class, 'showSickLeaveForm'])->name('aktivitas.sick-leave.form')->middleware('role:rmft');
    Route::post('aktivitas/bulk-sick-leave', [AktivitasController::class, 'processSickLeave'])->name('aktivitas.sick-leave.process')->middleware('role:rmft');
    
    // Feedback Routes - RMFT only (MUST BE BEFORE resource route)
    Route::get('aktivitas/{id}/feedback', [AktivitasController::class, 'feedback'])->name('aktivitas.feedback');
    Route::post('aktivitas/{id}/feedback', [AktivitasController::class, 'storeFeedback'])->name('aktivitas.storeFeedback');
    
    // Rekap Aktivitas - accessible by all authenticated users
    Route::get('aktivitas-rekap', [AktivitasController::class, 'rekap'])->name('aktivitas.rekap');
    
    // Export Rekap Aktivitas - accessible by all authenticated users
    Route::get('aktivitas-export-rekap', [AktivitasController::class, 'exportRekap'])->name('aktivitas.export-rekap');
    
    // Delete All Aktivitas - Admin only
    Route::post('aktivitas-delete-all', [AktivitasController::class, 'deleteAll'])->name('aktivitas.delete-all')->middleware('role:admin');
    
    // Aktivitas - accessible by both Manager and RMFT
    Route::resource('aktivitas', AktivitasController::class);
    
    // Profile Routes - All authenticated users
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Notification Routes - All authenticated users
    Route::get('api/notifications/count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.count');
    Route::get('api/notifications', [NotificationController::class, 'getNotifications'])->name('api.notifications');
    
    // API for nasabah autocomplete
    Route::get('api/nasabah/search', [NasabahController::class, 'searchByNorek'])->name('api.nasabah.search');
    Route::get('api/nasabah/get', [NasabahController::class, 'getByNorek'])->name('api.nasabah.get');
    
    // API for pipeline search (dari pull of pipeline - untuk manager pilih pipeline)
    Route::get('api/pipeline/search', [NasabahController::class, 'searchPipeline'])->name('api.pipeline.search');
    
    // API for pipeline available years
    Route::get('api/pipeline/years', [NasabahController::class, 'getAvailableYears'])->name('api.pipeline.years');
    
    // API for aktivitas search from pipelines table (untuk RMFT pilih nasabah dari pipeline yang sudah dipilih)
    Route::get('api/aktivitas/search-from-pipeline', [PipelineController::class, 'searchForAktivitas'])->name('api.aktivitas.search-from-pipeline');
    
    // API for pipeline create - search from pull of pipeline (tanpa filter, boleh duplikat)
    Route::get('api/pipeline/search-from-pull', [NasabahController::class, 'searchFromPullOfPipeline'])->name('api.pipeline.search-from-pull');
    
    // API for nasabah by strategy and kategori
    Route::get('api/nasabah', [NasabahController::class, 'searchPipeline'])->name('api.nasabah.index');
    
    // API for uker by KC
    Route::get('api/uker/by-kc', [UkerController::class, 'getByKC'])->name('api.uker.by-kc');
    
    // API for RMFT by KC
    Route::get('api/rmft/by-kc', [RMFTController::class, 'getByKC'])->name('api.rmft.by-kc');
    
    // API for Rencana Aktivitas by RMFT
    Route::get('api/rencana-aktivitas/by-rmft/{rmftId}', [RencanaAktivitasController::class, 'getByRMFT'])->name('api.rencana-aktivitas.by-rmft');
    
    // Manager & RMFT Pipeline Routes (Read-only view per KC)
    Route::middleware(['role:manager,rmft'])->group(function () {
        // Strategi 1
        Route::get('manager-pull-pipeline/merchant-savol', [ManagerPullPipelineController::class, 'merchantSavol'])->name('manager-pull-pipeline.merchant-savol');
        Route::get('manager-pull-pipeline/penurunan-merchant', [ManagerPullPipelineController::class, 'penurunanMerchant'])->name('manager-pull-pipeline.penurunan-merchant');
        Route::get('manager-pull-pipeline/penurunan-casa-brilink', [ManagerPullPipelineController::class, 'penurunanCasaBrilink'])->name('manager-pull-pipeline.penurunan-casa-brilink');
        Route::get('manager-pull-pipeline/qlola-non-debitur', [ManagerPullPipelineController::class, 'qlolaNonDebitur'])->name('manager-pull-pipeline.qlola-non-debitur');
        Route::get('manager-pull-pipeline/non-debitur-vol-besar', [ManagerPullPipelineController::class, 'nonDebiturVolBesar'])->name('manager-pull-pipeline.non-debitur-vol-besar');
        
        // Strategi 2
        Route::get('manager-pull-pipeline/qlola-nonaktif', [ManagerPullPipelineController::class, 'qlolaNonaktif'])->name('manager-pull-pipeline.qlola-nonaktif');
        Route::get('manager-pull-pipeline/user-aktif-casa-kecil', [ManagerPullPipelineController::class, 'userAktifCasaKecil'])->name('manager-pull-pipeline.user-aktif-casa-kecil');
        
        // Strategi 3
        Route::get('manager-pull-pipeline/optimalisasi-business-cluster', [ManagerPullPipelineController::class, 'optimalisasiBusinessCluster'])->name('manager-pull-pipeline.optimalisasi-business-cluster');
        
        // Strategi 4
        Route::get('manager-pull-pipeline/existing-payroll', [ManagerPullPipelineController::class, 'existingPayroll'])->name('manager-pull-pipeline.existing-payroll');
        Route::get('manager-pull-pipeline/potensi-payroll', [ManagerPullPipelineController::class, 'potensiPayroll'])->name('manager-pull-pipeline.potensi-payroll');
        
        // Strategi 6
        Route::get('manager-pull-pipeline/perusahaan-anak', [ManagerPullPipelineController::class, 'perusahaanAnak'])->name('manager-pull-pipeline.perusahaan-anak');
        
        // Strategi 7
        Route::get('manager-pull-pipeline/penurunan-prioritas-ritel-mikro', [ManagerPullPipelineController::class, 'penurunanPrioritasRitelMikro'])->name('manager-pull-pipeline.penurunan-prioritas-ritel-mikro');
        Route::get('manager-pull-pipeline/aum-dpk', [ManagerPullPipelineController::class, 'aumDpk'])->name('manager-pull-pipeline.aum-dpk');
        
        // Strategi 8
        Route::get('manager-pull-pipeline/strategi8', [ManagerPullPipelineController::class, 'strategi8'])->name('manager-pull-pipeline.strategi8');
        
        // Layering
        Route::get('manager-pull-pipeline/layering', [ManagerPullPipelineController::class, 'layering'])->name('manager-pull-pipeline.layering');
        
        // Nasabah Downgrade
        Route::get('manager-pull-pipeline/nasabah-downgrade', [ManagerPullPipelineController::class, 'nasabahDowngrade'])->name('manager-pull-pipeline.nasabah-downgrade');
        
        // Brilink Saldo Kurang
        Route::get('manager-pull-pipeline/brilink-saldo-kurang', [ManagerPullPipelineController::class, 'brilinkSaldoKurang'])->name('manager-pull-pipeline.brilink-saldo-kurang');
    });
    
    // Pipeline Routes - Index untuk semua role (termasuk RMFT)
    Route::get('pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
    Route::get('pipeline-export', [PipelineController::class, 'export'])->name('pipeline.export');
    
    // Manager and Admin Routes
    Route::middleware(['role:manager,admin'])->group(function () {
        
        // Pipeline Routes - Create, Edit, Delete hanya untuk Manager dan Admin
        Route::get('pipeline/create', [PipelineController::class, 'create'])->name('pipeline.create');
        Route::post('pipeline', [PipelineController::class, 'store'])->name('pipeline.store');
        Route::get('pipeline/{pipeline}/edit', [PipelineController::class, 'edit'])->name('pipeline.edit');
        Route::put('pipeline/{pipeline}', [PipelineController::class, 'update'])->name('pipeline.update');
        Route::delete('pipeline/{pipeline}', [PipelineController::class, 'destroy'])->name('pipeline.destroy');
        Route::delete('pipeline-delete-all', [PipelineController::class, 'deleteAll'])->name('pipeline.delete-all')->middleware('role:admin');
    });
    
    // Pipeline Show - untuk semua role (harus setelah route create agar tidak bentrok)
    Route::get('pipeline/{pipeline}', [PipelineController::class, 'show'])->name('pipeline.show');
    
    // Continue with other Manager/Admin routes
    Route::middleware(['role:manager,admin'])->group(function () {
        
        // Rekap Routes (Validasi)
        Route::get('rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('rekap/import', [RekapController::class, 'importForm'])->name('rekap.import.form');
        Route::post('rekap/import', [RekapController::class, 'import'])->name('rekap.import');
        Route::get('rekap/template', [RekapController::class, 'downloadTemplate'])->name('rekap.template');
        Route::post('rekap-delete-all', [RekapController::class, 'deleteAll'])->name('rekap.delete-all')->middleware('role:admin');
        
        // Rencana Aktivitas Routes
        Route::resource('rencana-aktivitas', RencanaAktivitasController::class);
        
        // Nasabah Routes
        Route::resource('nasabah', NasabahController::class);
        Route::get('nasabah-import', [NasabahController::class, 'importForm'])->name('nasabah.import.form');
        Route::post('nasabah-import', [NasabahController::class, 'import'])->name('nasabah.import');
        Route::delete('nasabah-delete-all', [NasabahController::class, 'deleteAll'])->name('nasabah.delete-all');
        
        // Akun Routes - Index untuk manager & admin
        Route::get('akun', [AkunController::class, 'index'])->name('akun.index');
        
        // Akun CRUD - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('akun/create', [AkunController::class, 'create'])->name('akun.create');
            Route::post('akun', [AkunController::class, 'store'])->name('akun.store');
            Route::get('akun/{id}/edit', [AkunController::class, 'edit'])->name('akun.edit');
            Route::put('akun/{id}', [AkunController::class, 'update'])->name('akun.update');
            Route::delete('akun/{id}', [AkunController::class, 'destroy'])->name('akun.destroy');
        });
        
        // Uker Routes
        Route::resource('uker', UkerController::class);
        Route::post('uker/import', [UkerController::class, 'import'])->name('uker.import');
        Route::delete('uker-delete-all', [UkerController::class, 'deleteAll'])->name('uker.delete-all');
        
        // RMFT Routes
        Route::resource('rmft', RMFTController::class);
        Route::post('rmft/import', [RMFTController::class, 'import'])->name('rmft.import');
        Route::delete('rmft-delete-all', [RMFTController::class, 'deleteAll'])->name('rmft.delete-all');

        // Penurunan Casa Brilink Routes (Strategi 1: PENURUNAN CASA BRILINK) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('penurunan-casa-brilink/import', [PenurunanCasaBrilinkController::class, 'importForm'])->name('penurunan-casa-brilink.import.form');
            Route::post('penurunan-casa-brilink/import', [PenurunanCasaBrilinkController::class, 'import'])->name('penurunan-casa-brilink.import');
            Route::delete('penurunan-casa-brilink-delete-all', [PenurunanCasaBrilinkController::class, 'deleteAll'])->name('penurunan-casa-brilink.delete-all');
            Route::resource('penurunan-casa-brilink', PenurunanCasaBrilinkController::class);
        });

        // Qlola Non Debitur Routes (Strategi 1: QLOLA NON DEBITUR) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('qlola-non-debitur/import', [QlolaNonDebiturController::class, 'importForm'])->name('qlola-non-debitur.import.form');
            Route::post('qlola-non-debitur/import', [QlolaNonDebiturController::class, 'import'])->name('qlola-non-debitur.import');
            Route::delete('qlola-non-debitur-delete-all', [QlolaNonDebiturController::class, 'deleteAll'])->name('qlola-non-debitur.delete-all');
            Route::resource('qlola-non-debitur', QlolaNonDebiturController::class);
        });

        // Non Debitur Vol Besar Routes (Strategi 1: NON DEBITUR VOL BESAR CASA KECIL) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('non-debitur-vol-besar/import', [NonDebiturVolBesarController::class, 'importForm'])->name('non-debitur-vol-besar.import.form');
            Route::post('non-debitur-vol-besar/import', [NonDebiturVolBesarController::class, 'import'])->name('non-debitur-vol-besar.import');
            Route::delete('non-debitur-vol-besar-delete-all', [NonDebiturVolBesarController::class, 'deleteAll'])->name('non-debitur-vol-besar.delete-all');
            Route::resource('non-debitur-vol-besar', NonDebiturVolBesarController::class);
        });

        // Qlola Nonaktif Routes (Strategi 2: QLOLA NONAKTIF) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('qlola-nonaktif/import', [QlolaNonaktifController::class, 'importForm'])->name('qlola-nonaktif.import.form');
            Route::post('qlola-nonaktif/import', [QlolaNonaktifController::class, 'import'])->name('qlola-nonaktif.import');
            Route::delete('qlola-nonaktif-delete-all', [QlolaNonaktifController::class, 'deleteAll'])->name('qlola-nonaktif.delete-all');
            Route::resource('qlola-nonaktif', QlolaNonaktifController::class);
        });

        // User Aktif Casa Kecil Routes (Strategi 2: USER AKTIF CASA KECIL) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('user-aktif-casa-kecil/import', [UserAktifCasaKecilController::class, 'importForm'])->name('user-aktif-casa-kecil.import.form');
            Route::post('user-aktif-casa-kecil/import', [UserAktifCasaKecilController::class, 'import'])->name('user-aktif-casa-kecil.import');
            Route::delete('user-aktif-casa-kecil-delete-all', [UserAktifCasaKecilController::class, 'deleteAll'])->name('user-aktif-casa-kecil.delete-all');
            Route::resource('user-aktif-casa-kecil', UserAktifCasaKecilController::class);
        });

        // Merchant Savol Routes (Strategi 1: MERCHANT SAVOL BESAR CASA KECIL) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('merchant-savol/import', [MerchantSavolController::class, 'importForm'])->name('merchant-savol.import.form');
            Route::post('merchant-savol/import', [MerchantSavolController::class, 'import'])->name('merchant-savol.import');
            Route::delete('merchant-savol-delete-all', [MerchantSavolController::class, 'deleteAll'])->name('merchant-savol.delete-all');
            Route::resource('merchant-savol', MerchantSavolController::class);
        });

        // Non Debitur Vol Besar Routes (Strategi 1: NON DEBITUR VOL BESAR CASA KECIL) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('non-debitur-vol-besar/import', [NonDebiturVolBesarController::class, 'importForm'])->name('non-debitur-vol-besar.import.form');
            Route::post('non-debitur-vol-besar/import', [NonDebiturVolBesarController::class, 'import'])->name('non-debitur-vol-besar.import');
            Route::delete('non-debitur-vol-besar-delete-all', [NonDebiturVolBesarController::class, 'deleteAll'])->name('non-debitur-vol-besar.delete-all');
            Route::resource('non-debitur-vol-besar', NonDebiturVolBesarController::class);
        });

        // Penurunan Merchant Routes (Strategi 1: PENURUNAN CASA MERCHANT QRIS & EDC) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('penurunan-merchant/import', [PenurunanMerchantController::class, 'importForm'])->name('penurunan-merchant.import.form');
            Route::post('penurunan-merchant/import', [PenurunanMerchantController::class, 'import'])->name('penurunan-merchant.import');
            Route::delete('penurunan-merchant-delete-all', [PenurunanMerchantController::class, 'deleteAll'])->name('penurunan-merchant.delete-all');
            Route::resource('penurunan-merchant', PenurunanMerchantController::class);
        });

        // Optimalisasi Business Cluster Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('optimalisasi-business-cluster/import', [OptimalisasiBusinessClusterController::class, 'importForm'])->name('optimalisasi-business-cluster.import.form');
            Route::post('optimalisasi-business-cluster/import', [OptimalisasiBusinessClusterController::class, 'import'])->name('optimalisasi-business-cluster.import');
            Route::delete('optimalisasi-business-cluster-delete-all', [OptimalisasiBusinessClusterController::class, 'deleteAll'])->name('optimalisasi-business-cluster.delete-all');
            Route::resource('optimalisasi-business-cluster', OptimalisasiBusinessClusterController::class);
        });

        // Strategi 8 Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('strategi8/import', [Strategi8Controller::class, 'importForm'])->name('strategi8.import.form');
            Route::post('strategi8/import', [Strategi8Controller::class, 'import'])->name('strategi8.import');
            Route::delete('strategi8-delete-all', [Strategi8Controller::class, 'deleteAll'])->name('strategi8.delete-all');
            Route::resource('strategi8', Strategi8Controller::class);
        });

        // Existing Payroll Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('existing-payroll/import', [ExistingPayrollController::class, 'importForm'])->name('existing-payroll.import.form');
            Route::post('existing-payroll/import', [ExistingPayrollController::class, 'import'])->name('existing-payroll.import');
            Route::delete('existing-payroll-delete-all', [ExistingPayrollController::class, 'deleteAll'])->name('existing-payroll.delete-all');
            Route::resource('existing-payroll', ExistingPayrollController::class);
        });

        // Potensi Payroll Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('potensi-payroll/import', [PotensiPayrollController::class, 'importForm'])->name('potensi-payroll.import.form');
            Route::post('potensi-payroll/import', [PotensiPayrollController::class, 'import'])->name('potensi-payroll.import');
            Route::delete('potensi-payroll-delete-all', [PotensiPayrollController::class, 'deleteAll'])->name('potensi-payroll.delete-all');
            Route::resource('potensi-payroll', PotensiPayrollController::class);
        });

        // Perusahaan Anak Routes (Strategi 6) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('perusahaan-anak/import', [PerusahaanAnakController::class, 'importForm'])->name('perusahaan-anak.import.form');
            Route::post('perusahaan-anak/import', [PerusahaanAnakController::class, 'import'])->name('perusahaan-anak.import');
            Route::delete('perusahaan-anak-delete-all', [PerusahaanAnakController::class, 'deleteAll'])->name('perusahaan-anak.delete-all');
            Route::resource('perusahaan-anak', PerusahaanAnakController::class);
        });

        // Penurunan Prioritas Ritel & Mikro Routes (Strategi 7) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('penurunan-prioritas-ritel-mikro/import', [PenurunanPrioritasRitelMikroController::class, 'importForm'])->name('penurunan-prioritas-ritel-mikro.import.form');
            Route::post('penurunan-prioritas-ritel-mikro/import', [PenurunanPrioritasRitelMikroController::class, 'import'])->name('penurunan-prioritas-ritel-mikro.import');
            Route::delete('penurunan-prioritas-ritel-mikro-delete-all', [PenurunanPrioritasRitelMikroController::class, 'deleteAll'])->name('penurunan-prioritas-ritel-mikro.delete-all');
            Route::resource('penurunan-prioritas-ritel-mikro', PenurunanPrioritasRitelMikroController::class);
        });

        // AUM DPK Routes (Strategi 7) - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('aum-dpk/import', [AumDpkController::class, 'importForm'])->name('aum-dpk.import.form');
            Route::post('aum-dpk/import', [AumDpkController::class, 'import'])->name('aum-dpk.import');
            Route::delete('aum-dpk-delete-all', [AumDpkController::class, 'deleteAll'])->name('aum-dpk.delete-all');
            Route::resource('aum-dpk', AumDpkController::class);
        });

        // Layering Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('layering/import', [LayeringController::class, 'importForm'])->name('layering.import.form');
            Route::post('layering/import', [LayeringController::class, 'import'])->name('layering.import');
            Route::delete('layering-delete-all', [LayeringController::class, 'deleteAll'])->name('layering.delete-all');
            Route::resource('layering', LayeringController::class);
        });

        // Nasabah Downgrade Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('nasabah-downgrade/import', [NasabahDowngradeController::class, 'importForm'])->name('nasabah-downgrade.import.form');
            Route::post('nasabah-downgrade/import', [NasabahDowngradeController::class, 'import'])->name('nasabah-downgrade.import');
            Route::delete('nasabah-downgrade-delete-all', [NasabahDowngradeController::class, 'deleteAll'])->name('nasabah-downgrade.delete-all');
            Route::resource('nasabah-downgrade', NasabahDowngradeController::class);
        });

        // Brilink Routes - Admin Only
        Route::middleware(['role:admin'])->group(function () {
            Route::get('brilink/import', [BrilinkController::class, 'importForm'])->name('brilink.import.form');
            Route::post('brilink/import', [BrilinkController::class, 'import'])->name('brilink.import');
            Route::delete('brilink-delete-all', [BrilinkController::class, 'deleteAll'])->name('brilink.delete-all');
            Route::resource('brilink', BrilinkController::class);
        });
    });
    
    // API for perusahaan anak dropdown (accessible by all authenticated users)
    Route::get('api/perusahaan-anak/list', [PerusahaanAnakController::class, 'getListPerusahaanAnak'])->name('api.perusahaan-anak.list');
});
