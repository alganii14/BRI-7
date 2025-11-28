<?php

namespace App\Http\Controllers;

use App\Models\Pipeline;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PipelineController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $query = Pipeline::with(['assignedBy', 'nasabah']);
            
            if ($request->filled('kode_kc')) {
                $query->where('kode_kc', $request->kode_kc);
            }
            
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
            
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
            
            $pipelines = $query->orderBy('tanggal', 'desc')->paginate(20);
            
            $listKC = Pipeline::select('kode_kc', 'nama_kc')
                             ->distinct()
                             ->orderBy('nama_kc')
                             ->get();
            
            $listUnit = Pipeline::select('kode_uker', 'nama_uker', 'kode_kc')
                               ->distinct()
                               ->orderBy('nama_uker')
                               ->get();
            
            return view('pipeline.index', compact('pipelines', 'listKC', 'listUnit'));
            
        } elseif ($user->isManager()) {
            $query = Pipeline::with(['assignedBy', 'nasabah'])
                            ->where('kode_kc', $user->kode_kanca);
            
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
            
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
            
            $pipelines = $query->orderBy('tanggal', 'desc')->paginate(20);
            
            $listUnit = Pipeline::select('kode_uker', 'nama_uker')
                               ->where('kode_kc', $user->kode_kanca)
                               ->distinct()
                               ->orderBy('nama_uker')
                               ->get();
            
            return view('pipeline.index', compact('pipelines', 'listUnit'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get list KC untuk Admin
        $listKC = [];
        if ($user->isAdmin()) {
            $listKC = \App\Models\Uker::select('kode_kanca', 'kanca')
                                      ->distinct()
                                      ->orderBy('kanca')
                                      ->get();
        }
        
        // Get list Uker berdasarkan KC
        $listUker = [];
        if ($user->isManager()) {
            // Manager hanya lihat uker di KC mereka
            $listUker = \App\Models\Uker::where('kode_kanca', $user->kode_kanca)
                                        ->orderBy('sub_kanca')
                                        ->get();
        }
        
        return view('pipeline.create', compact('listKC', 'listUker'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $kategoriBebas = [
            'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)',
            'Qlola (Belum ada Qlola / ada namun nonaktif)',
            'Qlola Non Debitur',
            'Non Debitur Vol Besar CASA Kecil',
            'AUM>2M DPK<50 juta',
            'User Aktif Casa Kecil',
            'PENURUNAN CASA BRILINK',
            'PENURUNAN CASA MERCHANT (QRIS & EDC)',
            'Existing Payroll',
            'Potensi Payroll',
            'List Perusahaan Anak'
        ];
        
        $isPipelineData = in_array($request->kategori_strategi, $kategoriBebas) || 
                         $request->strategy_pipeline === 'Wingback Penguatan Produk & Fungsi RM' ||
                         $request->strategy_pipeline === 'Layering' ||
                         $request->kategori_strategi === 'Winback' ||
                         $request->strategy_pipeline === 'Optimalisasi Business Cluster';
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kode_kc' => 'required|string',
            'nama_kc' => 'required|string',
            'kode_uker' => 'required|string',
            'nama_uker' => 'required|string',
            'kode_uker_list' => 'nullable|string',
            'nama_uker_list' => 'nullable|string',
            'strategy_pipeline' => 'required|string',
            'kategori_strategi' => 'nullable|string',
            'tipe_nasabah' => 'required|in:lama,baru',
            'nama_nasabah' => 'required|string',
            'norek' => $isPipelineData ? 'nullable|string' : 'required|string',
            'keterangan' => 'nullable|string',
            'nasabah_data_json' => 'nullable|string',
        ]);
        
        $tipePipeline = $validated['tipe_nasabah'];
        
        // Check if multiple nasabah selected (from JSON data)
        $multipleNasabah = false;
        $nasabahList = [];
        
        if (!empty($validated['nasabah_data_json'])) {
            $nasabahList = json_decode($validated['nasabah_data_json'], true);
            if (is_array($nasabahList) && count($nasabahList) > 1) {
                $multipleNasabah = true;
            }
        }
        
        // If multiple nasabah selected, create pipeline for each
        if ($multipleNasabah) {
            $createdCount = 0;
            
            foreach ($nasabahList as $nasabahData) {
                $nasabahId = null;
                if (!empty($nasabahData['norek']) && $nasabahData['norek'] !== '-') {
                    $nasabah = Nasabah::where('norek', $nasabahData['norek'])
                                      ->where('kode_kc', $validated['kode_kc'])
                                      ->where('kode_uker', $validated['kode_uker'])
                                      ->first();
                    
                    if (!$nasabah) {
                        $nasabah = Nasabah::create([
                            'norek' => $nasabahData['norek'],
                            'nama_nasabah' => $nasabahData['nama'],
                            'segmen_nasabah' => $nasabahData['segmen'] !== '-' ? $nasabahData['segmen'] : 'Ritel',
                            'kode_kc' => $validated['kode_kc'],
                            'nama_kc' => $validated['nama_kc'],
                            'kode_uker' => $validated['kode_uker'],
                            'nama_uker' => $validated['nama_uker'],
                        ]);
                    }
                    
                    $nasabahId = $nasabah->id;
                }
                
                $pipelineData = [
                    'tanggal' => $validated['tanggal'],
                    'kode_kc' => $validated['kode_kc'],
                    'nama_kc' => $validated['nama_kc'],
                    'kode_uker' => $validated['kode_uker'],
                    'nama_uker' => $validated['nama_uker'],
                    'strategy_pipeline' => $validated['strategy_pipeline'],
                    'kategori_strategi' => $validated['kategori_strategi'] ?? null,
                    'nama_nasabah' => $nasabahData['nama'],
                    'norek' => $nasabahData['norek'] !== '-' ? $nasabahData['norek'] : null,
                    'keterangan' => $validated['keterangan'],
                    'nasabah_id' => $nasabahId,
                    'tipe' => $tipePipeline,
                    'assigned_by' => $user->id,
                ];
                
                Pipeline::create($pipelineData);
                $createdCount++;
            }
            
            return redirect()->route('pipeline.index')->with('success', "Berhasil membuat {$createdCount} pipeline untuk {$createdCount} nasabah!");
        }
        
        $multipleUnits = !empty($validated['kode_uker_list']) && 
                        !empty($validated['nama_uker_list']) &&
                        trim($validated['kode_uker_list']) !== '' &&
                        trim($validated['nama_uker_list']) !== '';
        
        if ($multipleUnits) {
            $kodeUkerArray = array_filter(array_map('trim', explode(',', $validated['kode_uker_list'])));
            $namaUkerArray = array_filter(array_map('trim', explode(',', $validated['nama_uker_list'])));
            
            if (count($kodeUkerArray) <= 1) {
                $multipleUnits = false;
            }
        }
        
        if ($multipleUnits) {
            $kodeUkerArray = array_filter(array_map('trim', explode(',', $validated['kode_uker_list'])));
            $namaUkerArray = array_filter(array_map('trim', explode(',', $validated['nama_uker_list'])));
            
            $createdCount = 0;
            
            foreach ($kodeUkerArray as $index => $kodeUker) {
                $namaUker = $namaUkerArray[$index] ?? '';
                
                if (empty($kodeUker) || empty($namaUker)) {
                    continue;
                }
                
                $nasabahId = null;
                if (!empty($validated['norek'])) {
                    $nasabah = Nasabah::where('norek', $validated['norek'])
                                      ->where('kode_kc', $validated['kode_kc'])
                                      ->where('kode_uker', trim($kodeUker))
                                      ->first();
                    
                    if (!$nasabah) {
                        $nasabah = Nasabah::create([
                            'norek' => $validated['norek'],
                            'nama_nasabah' => $validated['nama_nasabah'],
                            'segmen_nasabah' => $validated['segmen_nasabah'],
                            'kode_kc' => $validated['kode_kc'],
                            'nama_kc' => $validated['nama_kc'],
                            'kode_uker' => trim($kodeUker),
                            'nama_uker' => trim($namaUker),
                        ]);
                    }
                    
                    $nasabahId = $nasabah->id;
                }
                
                $pipelineData = [
                    'tanggal' => $validated['tanggal'],
                    'kode_kc' => $validated['kode_kc'],
                    'nama_kc' => $validated['nama_kc'],
                    'kode_uker' => trim($kodeUker),
                    'nama_uker' => trim($namaUker),
                    'strategy_pipeline' => $validated['strategy_pipeline'],
                    'kategori_strategi' => $validated['kategori_strategi'] ?? null,
                    'nama_nasabah' => $validated['nama_nasabah'],
                    'norek' => $validated['norek'],
                    'keterangan' => $validated['keterangan'],
                    'nasabah_id' => $nasabahId,
                    'tipe' => $tipePipeline,
                    'assigned_by' => $user->id,
                ];
                
                Pipeline::create($pipelineData);
                $createdCount++;
            }
            
            return redirect()->route('pipeline.index')->with('success', "Berhasil membuat {$createdCount} pipeline untuk {$createdCount} unit berbeda!");
            
        } else {
            if (!empty($validated['norek'])) {
                $nasabah = Nasabah::where('norek', $validated['norek'])
                                  ->where('kode_kc', $validated['kode_kc'])
                                  ->where('kode_uker', $validated['kode_uker'])
                                  ->first();
                
                if (!$nasabah) {
                    $nasabah = Nasabah::create([
                        'norek' => $validated['norek'],
                        'nama_nasabah' => $validated['nama_nasabah'],
                        'segmen_nasabah' => 'Ritel',
                        'kode_kc' => $validated['kode_kc'],
                        'nama_kc' => $validated['nama_kc'],
                        'kode_uker' => $validated['kode_uker'],
                        'nama_uker' => $validated['nama_uker'],
                    ]);
                }
                
                $validated['nasabah_id'] = $nasabah->id;
            } else {
                $validated['nasabah_id'] = null;
            }
            
            $validated['tipe'] = $tipePipeline;
            $validated['assigned_by'] = $user->id;
            
            unset($validated['tipe_nasabah']);

            Pipeline::create($validated);

            return redirect()->route('pipeline.index')->with('success', 'Pipeline berhasil ditambahkan!');
        }
    }

    public function show($id)
    {
        $pipeline = Pipeline::with(['nasabah'])->findOrFail($id);
        return view('pipeline.show', compact('pipeline'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $pipeline = Pipeline::findOrFail($id);
        
        if ($user->isManager() && $pipeline->kode_kc != $user->kode_kanca) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get list KC untuk Admin
        $listKC = [];
        if ($user->isAdmin()) {
            $listKC = \App\Models\Uker::select('kode_kanca', 'kanca')
                                      ->distinct()
                                      ->orderBy('kanca')
                                      ->get();
        }
        
        // Get list Uker berdasarkan KC pipeline
        $listUker = \App\Models\Uker::where('kode_kanca', $pipeline->kode_kc)
                                    ->orderBy('sub_kanca')
                                    ->get();
        
        return view('pipeline.edit', compact('pipeline', 'listKC', 'listUker'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $pipeline = Pipeline::findOrFail($id);
        
        if ($user->isManager() && $pipeline->kode_kc != $user->kode_kanca) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'strategy_pipeline' => 'nullable|string',
            'kategori_strategi' => 'nullable|string',
            'tipe_nasabah' => 'required|in:lama,baru',
            'nama_nasabah' => 'required|string',
            'norek' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
        ]);
        
        $validated['tipe'] = $validated['tipe_nasabah'];
        unset($validated['tipe_nasabah']);

        $pipeline->update($validated);

        return redirect()->route('pipeline.index')->with('success', 'Pipeline berhasil diupdate!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $pipeline = Pipeline::findOrFail($id);
        
        if ($user->isManager() && $pipeline->kode_kc != $user->kode_kanca) {
            abort(403, 'Unauthorized action.');
        }
        
        $pipeline->delete();

        return redirect()->route('pipeline.index')->with('success', 'Pipeline berhasil dihapus!');
    }
    
    public function deleteAll()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Hanya Admin yang bisa menghapus semua data pipeline.');
        }
        
        try {
            $count = Pipeline::count();
            Pipeline::truncate();
            
            return redirect()->route('pipeline.index')
                           ->with('success', "Berhasil menghapus semua data pipeline ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('pipeline.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    /**
     * Search nasabah from pipelines table for aktivitas form
     * JOIN dengan tabel pull of pipeline untuk mendapatkan data lengkap
     */
    public function searchForAktivitas(Request $request)
    {
        $search = $request->get('search', '');
        $kodeKc = $request->get('kode_kc');
        $kodeUker = $request->get('kode_uker');
        $strategy = $request->get('strategy');
        $kategori = $request->get('kategori');
        
        // Get list nasabah dari pipelines
        $query = Pipeline::query();
        
        if ($kodeKc) {
            $query->where('kode_kc', $kodeKc);
        }
        
        if ($kodeUker) {
            $kodeUkerArray = explode(',', $kodeUker);
            $query->whereIn('kode_uker', $kodeUkerArray);
        }
        
        if ($strategy) {
            $query->where('strategy_pipeline', $strategy);
        }
        
        if ($kategori) {
            $query->where('kategori_strategi', $kategori);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_nasabah', 'like', '%' . $search . '%')
                  ->orWhere('norek', 'like', '%' . $search . '%');
            });
        }
        
        // EXCLUDE nasabah yang sudah digunakan di aktivitas
        $usedInAktivitas = \App\Models\Aktivitas::where('strategy_pipeline', $strategy)
            ->where('kategori_strategi', $kategori)
            ->pluck('nama_nasabah')
            ->filter()
            ->unique()
            ->toArray();
        
        if (!empty($usedInAktivitas)) {
            $query->whereNotIn('nama_nasabah', $usedInAktivitas);
        }
        
        $pipelines = $query->select('nama_nasabah', 'norek', 'strategy_pipeline', 'kategori_strategi', 'kode_kc', 'kode_uker')
                          ->distinct()
                          ->orderBy('nama_nasabah')
                          ->paginate(50);
        
        // Untuk setiap nasabah di pipelines, ambil data lengkap dari tabel pull of pipeline
        $enrichedData = $pipelines->getCollection()->map(function($pipeline) use ($kodeKc, $kategori) {
            // Tentukan model berdasarkan kategori
            $sourceData = $this->getSourceData($pipeline->nama_nasabah, $kategori, $kodeKc);
            
            // Jika data ditemukan di source, gunakan data lengkap
            // Jika tidak, gunakan data minimal dari pipelines
            if ($sourceData) {
                return $sourceData;
            }
            
            // Fallback ke data minimal dari pipelines
            return [
                'nama_nasabah' => $pipeline->nama_nasabah,
                'norek' => $pipeline->norek,
                'cifno' => $pipeline->norek,
                'segmen_nasabah' => '-',
            ];
        });
        
        return response()->json([
            'data' => $enrichedData,
            'current_page' => $pipelines->currentPage(),
            'last_page' => $pipelines->lastPage(),
            'total' => $pipelines->total(),
        ]);
    }
    
    /**
     * Helper method untuk mengambil data lengkap dari tabel pull of pipeline
     */
    private function getSourceData($namaNasabah, $kategori, $kodeKc)
    {
        $model = null;
        $nameField = 'nama_nasabah';
        
        // Tentukan model dan field name berdasarkan kategori
        switch ($kategori) {
            case 'Winback':
                $model = \App\Models\Layering::class;
                break;
            case 'List Perusahaan Anak':
                $model = \App\Models\PerusahaanAnak::class;
                $nameField = 'nama_partner_vendor';
                break;
            case 'Existing Payroll':
                $model = \App\Models\ExistingPayroll::class;
                $nameField = 'nama_perusahaan';
                break;
            case 'Potensi Payroll':
                $model = \App\Models\PotensiPayroll::class;
                $nameField = 'perusahaan';
                break;
            case 'Nasabah Downgrade':
                $model = \App\Models\NasabahDowngrade::class;
                break;
            case 'BRILINK SALDO < 10 JUTA':
                $model = \App\Models\Brilink::class;
                $nameField = 'nama_agen';
                break;
            case 'Optimalisasi Business Cluster':
                $model = \App\Models\OptimalisasiBusinessCluster::class;
                $nameField = 'nama_usaha_pusat_bisnis';
                break;
            case 'Wingback Penguatan Produk & Fungsi RM':
                $model = \App\Models\Strategi8::class;
                break;
            case 'MERCHANT SAVOL BESAR CASA KECIL (QRIS & EDC)':
                $model = \App\Models\MerchantSavol::class;
                $nameField = 'nama_merchant';
                break;
            case 'PENURUNAN CASA MERCHANT (QRIS & EDC)':
                $model = \App\Models\PenurunanMerchant::class;
                break;
            case 'PENURUNAN CASA BRILINK':
                $model = \App\Models\PenurunanCasaBrilink::class;
                $nameField = 'nama_agen';
                break;
            case 'Qlola Non Debitur':
                $model = \App\Models\QlolaNonDebitur::class;
                break;
            case 'Non Debitur Vol Besar CASA Kecil':
                $model = \App\Models\NonDebiturVolBesar::class;
                break;
            case 'Qlola (Belum ada Qlola / ada namun nonaktif)':
                $model = \App\Models\QlolaNonaktif::class;
                $nameField = 'nama_debitur';
                break;
            case 'User Aktif Casa Kecil':
                $model = \App\Models\UserAktifCasaKecil::class;
                break;
            case 'AUM>2M DPK<50 juta':
                $model = \App\Models\AumDpk::class;
                break;
            case 'Penurunan Prioritas Ritel & Mikro':
                $model = \App\Models\PenurunanPrioritasRitelMikro::class;
                break;
            
            // Strategi 1 - Kategori lainnya
            case 'PENURUNAN CASA MERCHANT (QRIS & EDC)':
                $model = \App\Models\PenurunanMerchant::class;
                break;
            
            // Strategi 2
            case 'Qlola Nonaktif':
                $model = \App\Models\QlolaNonaktif::class;
                $nameField = 'nama_debitur';
                break;
            
            // Strategi 3
            case 'Optimalisasi Business Cluster':
                $model = \App\Models\OptimalisasiBusinessCluster::class;
                $nameField = 'nama_usaha_pusat_bisnis';
                break;
            
            // Strategi 6
            case 'Perusahaan Anak':
                $model = \App\Models\PerusahaanAnak::class;
                $nameField = 'nama_partner_vendor';
                break;
            
            // Strategi 7
            case 'AUM DPK':
                $model = \App\Models\AumDpk::class;
                break;
            
            // Strategi 8
            case 'Strategi 8':
            case 'Wingback':
                $model = \App\Models\Strategi8::class;
                break;
            
            // Layering
            case 'Layering':
                $model = \App\Models\Layering::class;
                break;
                
            default:
                return null;
        }
        
        if (!$model) {
            return null;
        }
        
        // Query data dari tabel source
        $data = $model::where($nameField, $namaNasabah)->first();
        
        if ($data) {
            // Return all attributes
            return $data->toArray();
        }
        
        return null;
    }
}
