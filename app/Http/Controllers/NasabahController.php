<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\PenurunanPrioritasRitelMikro;
use App\Models\PenurunanMerchant;
use App\Models\PenurunanCasaBrilink;
use App\Models\MerchantSavol;
use App\Models\QlolaNonDebitur;
use App\Models\NonDebiturVolBesar;
use App\Models\QlolaNonaktif;
use App\Models\UserAktifCasaKecil;
use App\Models\OptimalisasiBusinessCluster;
use App\Models\Strategi8;
use App\Models\ExistingPayroll;
use App\Models\PotensiPayroll;
use App\Models\PenurunanMantri;
use App\Models\PenurunanMerchantMikro;
use App\Models\PenurunanMerchantRitel;
use App\Models\PenurunanRitel;
use App\Models\PenurunanSmeRitel;
use App\Models\Top10QrisPerUnit;
use App\Models\AumDpk;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    /**
     * Get available years from pipeline tables
     */
    public function getAvailableYears(Request $request)
    {
        $strategy = $request->get('strategy');
        $kategori = $request->get('kategori'); // Tambah parameter kategori
        
        if (!$strategy) {
            return response()->json([]);
        }
        
        // Tentukan model berdasarkan kategori atau strategy
        $model = null;
        $searchKey = $kategori ?: $strategy;
        
        switch ($searchKey) {
            case 'List Perusahaan Anak':
            case 'Strategi 6':
                $model = PerusahaanAnak::class;
                break;
            case 'Optimalisasi Business Cluster':
            case 'Strategi 3':
                $model = OptimalisasiBusinessCluster::class;
                break;
            case 'Strategi 8':
                $model = Strategi8::class;
                break;
            case 'Existing Payroll':
                $model = ExistingPayroll::class;
                break;
            case 'Potensi Payroll':
            case 'Strategi 4':
                $model = PotensiPayroll::class;
                break;
            case 'AUM>2M DPK<50 juta':
                $model = AumDpk::class;
                break;
            case 'Penurunan Prioritas Ritel & Mikro':
                $model = PenurunanPrioritasRitelMikro::class;
                break;
            case 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)':
                $model = MerchantSavol::class;
                break;
            case 'PENURUNAN CASA MERCHANT (QRIS & EDC)':
                $model = PenurunanMerchant::class;
                break;
            case 'PENURUNAN CASA BRILINK':
                $model = PenurunanCasaBrilink::class;
                break;
            case 'Qlola Non Debitur':
                $model = QlolaNonDebitur::class;
                break;
            case 'Non Debitur Vol Besar CASA Kecil':
                $model = NonDebiturVolBesar::class;
                break;
            case 'Qlola Nonaktif':
            case 'Qlola (Belum ada Qlola / ada namun nonaktif)':
            case 'Strategi 2':
                $model = QlolaNonaktif::class;
                break;
            case 'User Aktif Casa Kecil':
                $model = UserAktifCasaKecil::class;
                break;
            case 'Strategi 1':
            case 'Strategi 7':
                // Untuk strategi yang belum ada tabelnya, gunakan Nasabah biasa
                $model = Nasabah::class;
                break;
            case 'Penurunan Mantri':
                $model = PenurunanMantri::class;
                break;
            case 'Penurunan Merchant Mikro':
                $model = PenurunanMerchantMikro::class;
                break;
            case 'Penurunan Merchant Ritel':
                $model = PenurunanMerchantRitel::class;
                break;
            case 'Penurunan Ritel':
                $model = PenurunanRitel::class;
                break;
            case 'Penurunan SME Ritel':
                $model = PenurunanSmeRitel::class;
                break;
            case 'Top 10 QRIS Per Unit':
                $model = Top10QrisPerUnit::class;
                break;
            default:
                return response()->json([]);
        }
        
        // Get distinct years from created_at
        $years = $model::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
        
        return response()->json($years);
    }
    
    /**
     * Search nasabah from pipeline tables based on strategy
     */
    public function searchPipeline(Request $request)
    {
        $search = $request->get('search');
        $kode_kc = $request->get('kode_kc');
        $kode_uker = $request->get('kode_uker');
        $strategy = $request->get('strategy');
        $kategori = $request->get('kategori'); // Kategori spesifik dari strategi
        $load_all = $request->get('load_all'); // Parameter untuk load semua data
        $page = $request->get('page', 1); // Current page, default 1
        $perPage = 10; // 10 items per page
        $month = $request->get('month'); // Filter bulan
        $year = $request->get('year'); // Filter tahun
        
        if (!$strategy) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'total' => 0
            ]);
        }
        
        // Jika load_all atau search kosong, tidak perlu minimum 2 karakter
        if (!$load_all && $search && strlen($search) < 2) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'total' => 0
            ]);
        }
        
        // Tentukan model berdasarkan kategori atau strategy
        $model = null;
        $isQris = false;
        $isOptimalisasiBC = false;
        $isExistingPayroll = false;
        $isPotensiPayroll = false;
        $isPerusahaanAnak = false;
        
        // Gunakan kategori jika ada, jika tidak gunakan strategy
        $searchKey = $kategori ?: $strategy;
        
        switch ($searchKey) {
            case 'List Perusahaan Anak':
            case 'Strategi 6':
                $model = \App\Models\PerusahaanAnak::class;
                $isPerusahaanAnak = true;
                break;
            case 'Penurunan Prioritas Ritel & Mikro':
                $model = \App\Models\PenurunanPrioritasRitelMikro::class;
                break;
            case 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)':
                $model = \App\Models\MerchantSavol::class;
                break;
            case 'PENURUNAN CASA MERCHANT (QRIS & EDC)':
                $model = PenurunanMerchant::class;
                break;
            case 'PENURUNAN CASA BRILINK':
                $model = PenurunanCasaBrilink::class;
                break;
            case 'Qlola Non Debitur':
                $model = QlolaNonDebitur::class;
                break;
            case 'AUM>2M DPK<50 juta':
            case 'Strategi 7':
                $model = AumDpk::class;
                break;
            case 'Optimalisasi Business Cluster':
            case 'Strategi 3':
                $model = OptimalisasiBusinessCluster::class;
                $isOptimalisasiBC = true;
                break;
            case 'Strategi 8':
            case 'Wingback Penguatan Produk & Fungsi RM':
                $model = Strategi8::class;
                break;
            case 'Layering':
            case 'Wingback':
                $model = \App\Models\Layering::class;
                break;
            case 'Existing Payroll':
                $model = ExistingPayroll::class;
                $isExistingPayroll = true;
                break;
            case 'Potensi Payroll':
            case 'Strategi 4':
                $model = PotensiPayroll::class;
                $isPotensiPayroll = true;
                break;
            case 'Non Debitur Vol Besar CASA Kecil':
                $model = NonDebiturVolBesar::class;
                break;
            case 'Qlola Nonaktif':
            case 'Qlola (Belum ada Qlola / ada namun nonaktif)':
            case 'Strategi 2':
                $model = QlolaNonaktif::class;
                break;
            case 'User Aktif Casa Kecil':
                $model = UserAktifCasaKecil::class;
                break;
            case 'Strategi 1':
                // Untuk strategi yang belum ada tabelnya, gunakan Nasabah biasa
                $model = Nasabah::class;
                break;
            case 'Penurunan Mantri':
                $model = PenurunanMantri::class;
                break;
            case 'Penurunan Merchant Mikro':
                $model = PenurunanMerchantMikro::class;
                break;
            case 'Penurunan Merchant Ritel':
                $model = PenurunanMerchantRitel::class;
                break;
            case 'Penurunan Ritel':
                $model = PenurunanRitel::class;
                break;
            case 'Penurunan SME Ritel':
                $model = PenurunanSmeRitel::class;
                break;
            case 'Top 10 QRIS Per Unit':
                $model = Top10QrisPerUnit::class;
                $isQris = true;
                break;
            default:
                return response()->json([]);
        }
        
        $query = $model::query();
        
        if ($isPerusahaanAnak) {
            // Perusahaan Anak - tampilkan semua data tanpa filter KC
            // Order by id untuk konsistensi
            $query->orderBy('id', 'asc');
            
            // Search by nama partner, perusahaan anak, atau cabang
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_partner_vendor', 'LIKE', "%{$search}%")
                      ->orWhere('nama_perusahaan_anak', 'LIKE', "%{$search}%")
                      ->orWhere('cabang_induk_terdekat', 'LIKE', "%{$search}%")
                      ->orWhere('jenis_usaha', 'LIKE', "%{$search}%");
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->get([
                                'id',
                                'nama_partner_vendor',
                                'jenis_usaha',
                                'alamat',
                                'cabang_induk_terdekat',
                                'nama_pic_partner',
                                'hp_pic_partner',
                                'nama_perusahaan_anak',
                                'status_pipeline'
                            ])
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'nama_partner_vendor' => $item->nama_partner_vendor,
                                    'jenis_usaha' => $item->jenis_usaha,
                                    'alamat' => $item->alamat,
                                    'cabang_induk_terdekat' => $item->cabang_induk_terdekat,
                                    'nama_pic_partner' => $item->nama_pic_partner,
                                    'hp_pic_partner' => $item->hp_pic_partner,
                                    'nama_perusahaan_anak' => $item->nama_perusahaan_anak,
                                    'status_pipeline' => $item->status_pipeline,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        }
        
        if ($isExistingPayroll) {
            // Existing Payroll - struktur field berbeda
            // Untuk Existing Payroll, tampilkan semua data tanpa filter KC
            // karena struktur data berbeda (per perusahaan, bukan per nasabah)
            
            // Order by id untuk konsistensi
            $query->orderBy('id', 'asc');
            
            // Search by corporate code atau nama perusahaan
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('corporate_code', 'LIKE', "%{$search}%")
                      ->orWhere('nama_perusahaan', 'LIKE', "%{$search}%")
                      ->orWhere('cabang_induk', 'LIKE', "%{$search}%")
                      ->orWhere('kode_cabang_induk', 'LIKE', "%{$search}%");
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->get([
                                'id',
                                'corporate_code',
                                'nama_perusahaan',
                                'kode_cabang_induk',
                                'cabang_induk',
                                'jumlah_rekening',
                                'saldo_rekening'
                            ])
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->corporate_code,
                                    'no_rekening' => '',
                                    'nama_nasabah' => $item->nama_perusahaan,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => '',
                                    'unit_kerja' => '',
                                    'saldo_terupdate' => 0,
                                    'saldo_last_eom' => 0,
                                    'delta' => null,
                                    'jumlah_rekening' => $item->jumlah_rekening,
                                    'saldo_rekening' => $item->saldo_rekening,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        }
        
        if ($isPotensiPayroll) {
            // Potensi Payroll - struktur field berbeda
            // Filter berdasarkan kode_cabang_induk sesuai dengan KC user
            if ($kode_kc) {
                $query->where('kode_cabang_induk', $kode_kc);
            }
            
            // Order by id untuk konsistensi
            $query->orderBy('id', 'asc');
            
            // Search by perusahaan atau kode cabang
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('perusahaan', 'LIKE', "%{$search}%")
                      ->orWhere('cabang_induk', 'LIKE', "%{$search}%")
                      ->orWhere('kode_cabang_induk', 'LIKE', "%{$search}%")
                      ->orWhere('estimasi_pekerja', 'LIKE', "%{$search}%");
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->get([
                                'id',
                                'perusahaan',
                                'kode_cabang_induk',
                                'cabang_induk',
                                'estimasi_pekerja'
                            ])
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => '',
                                    'no_rekening' => '',
                                    'nama_nasabah' => $item->perusahaan,
                                    'perusahaan' => $item->perusahaan,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => '',
                                    'unit_kerja' => '',
                                    'saldo_terupdate' => 0,
                                    'saldo_last_eom' => 0,
                                    'delta' => null,
                                    'estimasi_pekerja' => $item->estimasi_pekerja,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        }
        
        // Filter berdasarkan bulan dan tahun pada created_at (untuk strategi selain Existing Payroll & Potensi Payroll)
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        
        if ($isQris) {
            // Top 10 QRIS memiliki struktur field berbeda
            // Filter by KC first
            if ($kode_kc) {
                $query->where('mainbr', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('branch', $unitArray);
                } else {
                    // Single unit
                    $query->where('branch', $kode_uker);
                }
            }
            
            // Search by CIF or nama_merchant (hanya jika ada search term)
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cif', 'LIKE', "{$search}%")
                      ->orWhere('nama_merchant', 'LIKE', "%{$search}%");
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('cif', 'asc')
                            ->get()
                            ->map(function($item) {
                                // Clean and convert saldo string to integer
                                $saldo = $item->saldo_posisi ?? '0';
                                // Remove thousand separators (. or ,) before converting
                                $saldo = str_replace(['.', ','], '', $saldo);
                                
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cif,
                                    'no_rekening' => $item->no_rek,
                                    'nama_nasabah' => $item->nama_merchant,
                                    'kode_cabang_induk' => $item->mainbr,
                                    'cabang_induk' => $item->mbdesc,
                                    'kode_uker' => $item->branch,
                                    'unit_kerja' => $item->brdesc,
                                    'saldo_terupdate' => intval($saldo),
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($isOptimalisasiBC) {
            // Optimalisasi Business Cluster - struktur berbeda
            // Filter by branch code (mirip KC)
            if ($kode_kc) {
                $query->where('kode_cabang_induk', $kode_kc);
            }
            
            // Filter by kode_uker
            if ($kode_uker) {
                $query->where('kode_uker', $kode_uker);
            }
            
            // Search by rekening or nama usaha pusat bisnis
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('nomor_rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nama_usaha_pusat_bisnis', 'LIKE', "%{$search}%")
                      ->orWhere('nama_tenaga_pemasar', 'LIKE', "%{$search}%")
                      ->orWhere('cabang_induk', 'LIKE', "%{$search}%");
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'asc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->id, // Gunakan ID sebagai identifier
                                    'norek' => $item->nomor_rekening,
                                    'no_rekening' => $item->nomor_rekening,
                                    'nama_nasabah' => $item->nama_usaha_pusat_bisnis,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->unit_kerja,
                                    'saldo_terupdate' => 0,
                                    'saldo_last_eom' => 0,
                                    'delta' => null,
                                    'nama_tenaga_pemasar' => $item->nama_tenaga_pemasar,
                                    'tag_zona_unggulan' => $item->tag_zona_unggulan,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === Strategi8::class) {
            // Strategi 8 - struktur mirip penurunan lainnya
            // Filter by KC first
            if ($kode_kc) {
                $query->where('kode_cabang_induk', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Search by CIFNO, No Rekening atau nama_nasabah
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'LIKE', "%{$search}%")
                      ->orWhere('no_rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'Wingback Penguatan Produk & Fungsi RM')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('no_rekening', $usedNoreks)
                          ->whereNotIn('cif', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_nasabah', $usedNames);
                    }
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'no_rekening' => $item->no_rekening,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->unit_kerja,
                                    'product_type' => $item->product_type,
                                    'saldo_terupdate' => $item->saldo_terupdate,
                                    'saldo_last_eom' => $item->saldo_last_eom,
                                    'delta' => $item->delta,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === \App\Models\Layering::class) {
            // Layering - kategori wingback tersendiri
            // Filter by KC first
            if ($kode_kc) {
                $query->where('kode_cabang_induk', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Search by CIFNO, No Rekening atau nama_nasabah
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'LIKE', "%{$search}%")
                      ->orWhere('no_rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%")
                      ->orWhere('segmentasi', 'LIKE', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'Wingback')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('no_rekening', $usedNoreks)
                          ->whereNotIn('cifno', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_nasabah', $usedNames);
                    }
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'no_rekening' => $item->no_rekening,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->unit_kerja,
                                    'saldo_terupdate' => $item->saldo_terupdate,
                                    'saldo_last_eom' => $item->saldo_last_eom,
                                    'delta' => $item->delta,
                                    'segmentasi' => $item->segmentasi,
                                    'jenis_simpanan' => $item->jenis_simpanan,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === Nasabah::class) {
            // Untuk Strategi 1, 2, 4 - gunakan tabel nasabah biasa
            // Filter by KC first
            if ($kode_kc) {
                $query->where('kode_kc', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Search by norek or nama_nasabah
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('norek', 'LIKE', "%{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%");
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno ?? '-',
                                    'no_rekening' => $item->norek,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_kc,
                                    'cabang_induk' => $item->nama_kc,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->nama_uker,
                                    'saldo_terupdate' => 0,
                                    'saldo_last_eom' => 0,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === QlolaNonDebitur::class) {
            // Qlola Non Debitur - Strategi 1
            // Filter by KC first
            if ($kode_kc) {
                $query->where('kode_kanca', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Search by cifno, no_rekening or nama_nasabah
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'LIKE', "%{$search}%")
                      ->orWhere('no_rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'Qlola Non Debitur')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('no_rekening', $usedNoreks)
                          ->whereNotIn('cif', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_nasabah', $usedNames);
                    }
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'no_rekening' => $item->no_rekening,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_kanca,
                                    'kode_kanca' => $item->kode_kanca,
                                    'cabang_induk' => $item->kanca,
                                    'kanca' => $item->kanca,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->uker,
                                    'uker' => $item->uker,
                                    'segmentasi' => $item->segmentasi,
                                    'cek_qcash' => $item->cek_qcash,
                                    'cek_cms' => $item->cek_cms,
                                    'cek_ib' => $item->cek_ib,
                                    'keterangan' => $item->keterangan,
                                    'saldo_terupdate' => 0,
                                    'saldo_last_eom' => 0,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);

        } elseif ($model === NonDebiturVolBesar::class) {
            // Non Debitur Vol Besar CASA Kecil - Strategi 1
            if ($kode_kc) {
                $query->where('kode_kanca', $kode_kc);
            }

            if ($kode_uker) {
                if (strpos($kode_uker, ',') !== false) {
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    $query->where('kode_uker', $kode_uker);
                }
            }

            if ($year) {
                $query->whereYear('created_at', $year);
            }
            if ($month) {
                $query->whereMonth('created_at', $month);
            }

            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'LIKE', "%{$search}%")
                      ->orWhere('no_rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%")
                      ->orWhere('segmentasi', 'LIKE', "%{$search}%");
                });
            }

            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'Non Debitur Vol Besar CASA Kecil')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('no_rekening', $usedNoreks)
                          ->whereNotIn('cif', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_nasabah', $usedNames);
                    }
                });
            }

            $total = $query->count();
            $lastPage = ceil($total / $perPage);

            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                $saldo = $item->saldo ?? '0';
                                $saldo = str_replace(['.', ',', ' '], '', $saldo);

                                $volQcash = $item->vol_qcash ?? '0';
                                $volQcash = str_replace(['.', ',', ' '], '', $volQcash);

                                $volQib = $item->vol_qib ?? '0';
                                $volQib = str_replace(['.', ',', ' '], '', $volQib);

                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'no_rekening' => $item->no_rekening,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_kanca,
                                    'kode_kanca' => $item->kode_kanca,
                                    'cabang_induk' => $item->kanca,
                                    'kanca' => $item->kanca,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->uker,
                                    'uker' => $item->uker,
                                    'segmentasi' => $item->segmentasi,
                                    'vol_qcash' => $item->vol_qcash,
                                    'vol_qib' => $item->vol_qib,
                                    'saldo' => $item->saldo,
                                    'saldo_terupdate' => intval($saldo),
                                    'saldo_last_eom' => 0,
                                    'delta' => null,
                                ];
                            });

            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === QlolaNonaktif::class) {
            if ($kode_kc) {
                $query->where('kode_kanca', $kode_kc);
            }
            if ($kode_uker) {
                if (strpos($kode_uker, ',') !== false) {
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    $query->where('kode_uker', $kode_uker);
                }
            }
            if ($year) {
                $query->whereYear('created_at', $year);
            }
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'like', "%{$search}%")
                      ->orWhere('nama_debitur', 'like', "%{$search}%")
                      ->orWhere('norek_pinjaman', 'like', "%{$search}%")
                      ->orWhere('norek_simpanan', 'like', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $kategoriNames = ['Qlola (Belum ada Qlola / ada namun nonaktif)', 'Qlola Nonaktif'];
            $aktivitasUsed = \App\Models\Aktivitas::whereIn('kategori_strategi', $kategoriNames)
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('norek_pinjaman', $usedNoreks)
                          ->whereNotIn('cifno', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_debitur', $usedNames);
                    }
                });
            }
            
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                // Clean plafon value
                                $plafon = is_numeric($item->plafon) ? $item->plafon : (int)str_replace(['.', ',', ' '], '', $item->plafon);
                                
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'norek' => $item->norek_pinjaman,
                                    'no_rekening' => $item->norek_pinjaman,
                                    'norek_pinjaman' => $item->norek_pinjaman,
                                    'norek_simpanan' => $item->norek_simpanan,
                                    'nama_nasabah' => $item->nama_debitur,
                                    'nama_debitur' => $item->nama_debitur,
                                    'kode_cabang_induk' => $item->kode_kanca,
                                    'kode_kanca' => $item->kode_kanca,
                                    'cabang_induk' => $item->kanca,
                                    'kanca' => $item->kanca,
                                    'kode_unit' => $item->kode_uker,
                                    'kode_uker' => $item->kode_uker,
                                    'unit' => $item->uker,
                                    'unit_kerja' => $item->uker,
                                    'uker' => $item->uker,
                                    'plafon' => $item->plafon,
                                    'pn_pengelola' => $item->pn_pengelola,
                                    'keterangan' => $item->keterangan,
                                    'saldo_terupdate' => $plafon,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === UserAktifCasaKecil::class) {
            // User Aktif Casa Kecil - Strategi 2
            if ($kode_kc) {
                $query->where('kode_kanca', $kode_kc);
            }
            if ($kode_uker) {
                if (strpos($kode_uker, ',') !== false) {
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    $query->where('kode_uker', $kode_uker);
                }
            }
            if ($year) {
                $query->whereYear('created_at', $year);
            }
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'like', "%{$search}%")
                      ->orWhere('nama_nasabah', 'like', "%{$search}%")
                      ->orWhere('norek_pinjaman', 'like', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'User Aktif Casa Kecil')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('norek_pinjaman', $usedNoreks)
                          ->whereNotIn('cifno', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_nasabah', $usedNames);
                    }
                });
            }
            
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'norek' => $item->norek_pinjaman,
                                    'no_rekening' => $item->norek_pinjaman,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_kanca,
                                    'kode_kanca' => $item->kode_kanca,
                                    'cabang_induk' => $item->kanca,
                                    'kanca' => $item->kanca,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->uker,
                                    'uker' => $item->uker,
                                    'saldo_bulan_lalu' => $item->saldo_bulan_lalu,
                                    'saldo_bulan_berjalan' => $item->saldo_bulan_berjalan,
                                    'delta_saldo' => $item->delta_saldo,
                                    'nama_rm_pemrakarsa' => $item->nama_rm_pemrakarsa,
                                    'qcash' => $item->qcash,
                                    'qib' => $item->qib,
                                    'saldo_terupdate' => $item->saldo_bulan_berjalan,
                                    'saldo_last_eom' => $item->saldo_bulan_lalu,
                                    'delta' => $item->delta_saldo,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === AumDpk::class) {
            // AUM DPK - struktur khusus untuk Strategi 7
            // Filter by KC first
            if ($kode_kc) {
                $query->where('kode_cabang_induk', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Search by CIF, No Rekening atau nama_nasabah
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cif', 'LIKE', "%{$search}%")
                      ->orWhere('nomor_rekening', 'LIKE', "%{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'AUM>2M DPK<50 juta')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('nomor_rekening', $usedNoreks)
                          ->whereNotIn('cifno', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_nasabah', $usedNames);
                    }
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cif,
                                    'no_rekening' => $item->nomor_rekening,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->unit_kerja,
                                    'slp' => $item->slp,
                                    'pbo' => $item->pbo,
                                    'id_prioritas' => $item->id_prioritas,
                                    'aum' => $item->aum,
                                    'saldo_terupdate' => 0,
                                    'saldo_last_eom' => 0,
                                    'delta' => null,
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } elseif ($model === \App\Models\MerchantSavol::class) {
            // Merchant Savol - struktur khusus
            // Filter by KC first (paling penting untuk performance)
            if ($kode_kc) {
                $query->where('kode_kanca', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Filter by year and month if provided
            if ($year) {
                $query->whereYear('created_at', $year);
            }
            if ($month) {
                $query->whereMonth('created_at', $month);
            }
            
            // Search by CIF, nama merchant, TID/Store ID, atau rekening
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cif', 'LIKE', "{$search}%")
                      ->orWhere('nama_merchant', 'LIKE', "%{$search}%")
                      ->orWhere('tid_store_id', 'LIKE', "%{$search}%")
                      ->orWhere('norekening', 'LIKE', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)')
                ->select('norek', 'nama_nasabah')
                ->get();
            
            if ($aktivitasUsed->isNotEmpty()) {
                $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                
                $query->where(function($q) use ($usedNoreks, $usedNames) {
                    if (!empty($usedNoreks)) {
                        $q->whereNotIn('norekening', $usedNoreks)
                          ->whereNotIn('cif', $usedNoreks);
                    }
                    if (!empty($usedNames)) {
                        $q->whereNotIn('nama_merchant', $usedNames);
                    }
                });
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function($item) {
                                // Clean and convert saldo strings to integers
                                $savolBulanLalu = $item->savol_bulan_lalu ?? '0';
                                $casaAkhirBulan = $item->casa_akhir_bulan ?? '0';
                                
                                // Remove thousand separators and spaces
                                $savolBulanLalu = str_replace(['.', ',', ' '], '', $savolBulanLalu);
                                $casaAkhirBulan = str_replace(['.', ',', ' '], '', $casaAkhirBulan);
                                
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cif,
                                    'cif' => $item->cif,
                                    'no_rekening' => $item->norekening,
                                    'norekening' => $item->norekening,
                                    'nama_nasabah' => $item->nama_merchant,
                                    'nama_merchant' => $item->nama_merchant,
                                    'kode_cabang_induk' => $item->kode_kanca,
                                    'kode_kanca' => $item->kode_kanca,
                                    'cabang_induk' => $item->kanca,
                                    'kanca' => $item->kanca,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->uker,
                                    'uker' => $item->uker,
                                    'jenis_merchant' => $item->jenis_merchant,
                                    'tid_store_id' => $item->tid_store_id,
                                    'savol_bulan_lalu' => $item->savol_bulan_lalu,
                                    'casa_akhir_bulan' => $item->casa_akhir_bulan,
                                    'saldo_terupdate' => intval($casaAkhirBulan),
                                    'saldo_last_eom' => intval($savolBulanLalu),
                                    'delta' => intval($casaAkhirBulan) - intval($savolBulanLalu),
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
            
        } else {
            // Tabel penurunan lainnya
            // Filter by KC first (paling penting untuk performance)
            if ($kode_kc) {
                $query->where('kode_cabang_induk', $kode_kc);
            }
            
            if ($kode_uker) {
                // Check if multiple units (comma-separated)
                if (strpos($kode_uker, ',') !== false) {
                    // Multiple units
                    $unitArray = array_map('trim', explode(',', $kode_uker));
                    $query->whereIn('kode_uker', $unitArray);
                } else {
                    // Single unit
                    $query->where('kode_uker', $kode_uker);
                }
            }
            
            // Search by CIFNO or nama_nasabah (hanya jika ada search term)
            if ($search && strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    $q->where('cifno', 'LIKE', "{$search}%")  // Exact start match (lebih cepat)
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%");
                });
            }
            
            // Exclude data yang sudah digunakan di aktivitas dengan kategori yang sama
            if ($searchKey) {
                $aktivitasUsed = \App\Models\Aktivitas::where('kategori_strategi', $searchKey)
                    ->select('norek', 'nama_nasabah')
                    ->get();
                
                if ($aktivitasUsed->isNotEmpty()) {
                    $usedNoreks = $aktivitasUsed->pluck('norek')->filter()->toArray();
                    $usedNames = $aktivitasUsed->pluck('nama_nasabah')->filter()->toArray();
                    
                    $query->where(function($q) use ($usedNoreks, $usedNames) {
                        if (!empty($usedNoreks)) {
                            $q->whereNotIn('no_rekening', $usedNoreks)
                              ->whereNotIn('cif', $usedNoreks);
                        }
                        if (!empty($usedNames)) {
                            $q->whereNotIn('nama_nasabah', $usedNames);
                        }
                    });
                }
            }
            
            // Get total count for pagination
            $total = $query->count();
            $lastPage = ceil($total / $perPage);
            
            $results = $query->skip(($page - 1) * $perPage)
                            ->take($perPage)
                            ->orderBy('cifno', 'asc')
                            ->get()
                            ->map(function($item) {
                                // Clean and convert saldo strings to integers
                                $saldoTerupdate = $item->saldo_terupdate ?? $item->saldo_last_eom ?? '0';
                                $saldoLastEom = $item->saldo_last_eom ?? '0';
                                $delta = $item->delta ?? '0';
                                
                                // Remove thousand separators (. or ,) before converting
                                $saldoTerupdate = str_replace(['.', ','], '', $saldoTerupdate);
                                $saldoLastEom = str_replace(['.', ','], '', $saldoLastEom);
                                $delta = str_replace(['.', ','], '', $delta);
                                
                                return [
                                    'id' => $item->id,
                                    'cifno' => $item->cifno,
                                    'no_rekening' => $item->no_rekening,
                                    'nama_nasabah' => $item->nama_nasabah,
                                    'kode_cabang_induk' => $item->kode_cabang_induk,
                                    'cabang_induk' => $item->cabang_induk,
                                    'kode_uker' => $item->kode_uker,
                                    'unit_kerja' => $item->unit_kerja,
                                    'segmentasi' => $item->segmentasi ?? '-',
                                    'segmentasi_bpr' => $item->segmentasi_bpr ?? $item->segmentasi ?? '-',
                                    'jenis_simpanan' => $item->jenis_simpanan ?? '-',
                                    'saldo_terupdate' => $item->saldo_terupdate ?? '-',
                                    'saldo_last_eom' => $item->saldo_last_eom ?? '-',
                                    'delta' => intval($delta),
                                ];
                            });
            
            return response()->json([
                'data' => $results,
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'total' => $total,
                'per_page' => $perPage
            ]);
        }
    }

    /**
     * Search nasabah by CIFNO (for autocomplete)
     */
    public function searchByNorek(Request $request)
    {
        $search = $request->get('norek'); // Parameter name kept for compatibility, but searches CIFNO
        $kode_kc = $request->get('kode_kc');
        $kode_uker = $request->get('kode_uker');
        
        if (!$search) {
            return response()->json([]);
        }
        
        // Minimum 2 karakter untuk search
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        
        $query = Nasabah::query();
        
        // Filter by KC first (paling penting untuk performance)
        if ($kode_kc) {
            $query->where('kode_kc', $kode_kc);
        }
        
        if ($kode_uker) {
            // Check if multiple units (comma-separated)
            if (strpos($kode_uker, ',') !== false) {
                // Multiple units
                $unitArray = array_map('trim', explode(',', $kode_uker));
                $query->whereIn('kode_uker', $unitArray);
            } else {
                // Single unit
                $query->where('kode_uker', $kode_uker);
            }
        }
        
        // Search by CIFNO or nama_nasabah (setelah filter KC dan Unit)
        $query->where(function($q) use ($search) {
            $q->where('cifno', 'LIKE', "{$search}%")  // Exact start match (lebih cepat)
              ->orWhere('nama_nasabah', 'LIKE', "%{$search}%");
        });
        
        $nasabah = $query->limit(30)  // Batasi hanya 30 hasil
                        ->orderBy('cifno', 'asc')
                        ->get(['id', 'cifno', 'norek', 'nama_nasabah', 'segmen_nasabah', 'kode_kc', 'nama_kc', 'kode_uker', 'nama_uker']);
        
        return response()->json($nasabah);
    }

    /**
     * Get nasabah by exact CIFNO
     */
    public function getByNorek(Request $request)
    {
        $norek = $request->get('norek'); // Parameter kept for compatibility, but searches by CIFNO
        $kode_kc = $request->get('kode_kc');
        $kode_uker = $request->get('kode_uker');
        
        $query = Nasabah::where('cifno', $norek);
        
        // Filter by KC first (paling penting untuk performance)
        if ($kode_kc) {
            $query->where('kode_kc', $kode_kc);
        }
        
        if ($kode_uker) {
            // Check if multiple units (comma-separated)
            if (strpos($kode_uker, ',') !== false) {
                // Multiple units - use first match
                $unitArray = array_map('trim', explode(',', $kode_uker));
                $query->whereIn('kode_uker', $unitArray);
            } else {
                // Single unit
                $query->where('kode_uker', $kode_uker);
            }
        }
        
        $nasabah = $query->first();
        
        if ($nasabah) {
            return response()->json([
                'found' => true,
                'data' => $nasabah
            ]);
        }
        
        return response()->json([
            'found' => false
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // OPTIMASI: Select hanya kolom yang dibutuhkan untuk mengurangi memory usage
        $query = Nasabah::select([
            'id', 'norek', 'cifno', 'nama_nasabah', 'segmen_nasabah',
            'kode_kc', 'nama_kc', 'kode_uker', 'nama_uker', 'created_at'
        ]);
        
        // OPTIMASI: Filter by KC terlebih dahulu (paling selektif)
        if ($user->isManager() && $user->kode_kanca) {
            $query->where('kode_kc', $user->kode_kanca);
        } elseif ($request->has('kode_kc') && !empty($request->kode_kc)) {
            // Filter KC dari form filter
            $query->where('kode_kc', $request->kode_kc);
        }
        
        // Filter by Unit (jika ada)
        if ($request->has('kode_uker') && !empty($request->kode_uker)) {
            $query->where('kode_uker', $request->kode_uker);
        }
        
        // Search functionality (setelah filter KC/Unit)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            
            // OPTIMASI: Minimum 2 karakter untuk search
            if (strlen($search) >= 2) {
                $query->where(function($q) use ($search) {
                    // Gunakan exact start match untuk CIFNO dan norek (lebih cepat)
                    $q->where('norek', 'LIKE', "{$search}%")
                      ->orWhere('cifno', 'LIKE', "{$search}%")
                      ->orWhere('nama_nasabah', 'LIKE', "%{$search}%")
                      ->orWhere('nama_kc', 'LIKE', "%{$search}%")
                      ->orWhere('nama_uker', 'LIKE', "%{$search}%");
                });
            }
        }
        
        // OPTIMASI: Gunakan simplePaginate (TIDAK HITUNG TOTAL - lebih cepat!)
        $perPage = min((int)$request->get('per_page', 50), 200); // Max 200 per page
        
        // SimplePaginate tidak perlu COUNT(*) jadi jauh lebih cepat
        $nasabahs = $query->orderBy('created_at', 'desc')->simplePaginate($perPage);
        
        // OPTIMASI: Ambil KC list dari table uker (lebih kecil daripada nasabahs)
        $kcList = \DB::table('ukers')
            ->select('kode_kanca as kode_kc', 'kanca as nama_kc')
            ->whereNotNull('kode_kanca')
            ->distinct()
            ->orderBy('kanca')
            ->get();
        
        // Ambil Unit list berdasarkan KC yang dipilih (jika ada)
        $ukerList = collect();
        if ($request->has('kode_kc') && !empty($request->kode_kc)) {
            $ukerList = \DB::table('ukers')
                ->select('kode_sub_kanca as kode_uker', 'sub_kanca as nama_uker')
                ->where('kode_kanca', $request->kode_kc)
                ->whereNotNull('kode_sub_kanca')
                ->distinct()
                ->orderBy('sub_kanca')
                ->get();
        }
        
        return view('nasabah.index', compact('nasabahs', 'kcList', 'ukerList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('nasabah.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'norek' => 'required|string|max:255',
            'cifno' => 'nullable|string|max:255',
            'nama_nasabah' => 'required|string|max:255',
            'segmen_nasabah' => 'required|string|max:255',
            'kode_kc' => 'required|string|max:50',
            'nama_kc' => 'required|string|max:255',
            'kode_uker' => 'required|string|max:50',
            'nama_uker' => 'required|string|max:255',
        ]);

        Nasabah::create($validated);

        return redirect()->route('nasabah.index')
            ->with('success', 'Data nasabah berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        return view('nasabah.show', compact('nasabah'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        return view('nasabah.edit', compact('nasabah'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $nasabah = Nasabah::findOrFail($id);

        $validated = $request->validate([
            'norek' => 'required|string|max:255',
            'cifno' => 'nullable|string|max:255',
            'nama_nasabah' => 'required|string|max:255',
            'segmen_nasabah' => 'required|string|max:255',
            'kode_kc' => 'required|string|max:50',
            'nama_kc' => 'required|string|max:255',
            'kode_uker' => 'required|string|max:50',
            'nama_uker' => 'required|string|max:255',
        ]);

        $nasabah->update($validated);

        return redirect()->route('nasabah.index')
            ->with('success', 'Data nasabah berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        $nasabah->delete();

        return redirect()->route('nasabah.index')
            ->with('success', 'Data nasabah berhasil dihapus!');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('nasabah.import');
    }

    /**
     * Import CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:102400', // max 100MB
        ]);

        try {
            $file = $request->file('csv_file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle, 10000, ';');
            
            $imported = 0;
            $updated = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            try {
                while (($row = fgetcsv($handle, 10000, ';')) !== false) {
                    // Skip if row is empty
                    if (empty(array_filter($row))) {
                        continue;
                    }
                    
                    try {
                        // Check if record already exists based on norek
                        $existing = Nasabah::where('norek', $row[0])->first();
                        
                        $data = [
                            'norek' => $row[0] ?? null,
                            'cifno' => $row[1] ?? null,
                            'nama_nasabah' => $row[2] ?? null,
                            'segmen_nasabah' => $row[3] ?? null,
                            'kode_kc' => $row[4] ?? null,
                            'nama_kc' => $row[5] ?? null,
                            'kode_uker' => $row[6] ?? null,
                            'nama_uker' => $row[7] ?? null,
                        ];
                        
                        if ($existing) {
                            $existing->update($data);
                            $updated++;
                        } else {
                            Nasabah::create($data);
                            $imported++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Error on row: " . implode(', ', $row) . " - " . $e->getMessage();
                    }
                }
                
                DB::commit();
                fclose($handle);
                
                $message = "Berhasil import {$imported} data baru";
                if ($updated > 0) {
                    $message .= " dan update {$updated} data existing";
                }
                if (count($errors) > 0) {
                    $message .= ". " . count($errors) . " error terjadi.";
                }
                
                return redirect()->route('nasabah.index')->with('success', $message);
                
            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);
                throw $e;
            }
            
        } catch (\Exception $e) {
            return redirect()->route('nasabah.index')
                ->with('error', 'Gagal import CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all records
     */
    public function deleteAll()
    {
        try {
            Nasabah::truncate();
            return redirect()->route('nasabah.index')
                ->with('success', 'Semua data nasabah berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('nasabah.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
