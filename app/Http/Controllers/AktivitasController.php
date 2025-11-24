<?php

namespace App\Http\Controllers;

use App\Models\Aktivitas;
use App\Models\RMFT;
use App\Models\Nasabah;
use App\Models\User;
use App\Models\RencanaAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin bisa lihat semua aktivitas dengan filter
            $query = Aktivitas::with(['rmft', 'assignedBy', 'nasabah']);
            
            // Filter per KC
            if ($request->filled('kode_kc')) {
                $query->where('kode_kc', $request->kode_kc);
            }
            
            // Filter per RMFT
            if ($request->filled('rmft_id')) {
                $query->where('rmft_id', $request->rmft_id);
            }
            
            // Filter per Unit
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
            
            // Filter per Range Tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
            
            $aktivitas = $query->orderBy('tanggal', 'desc')->paginate(20);
            
            // Get list KC dan Unit untuk dropdown filter
            $listKC = Aktivitas::select('kode_kc', 'nama_kc')
                               ->distinct()
                               ->orderBy('nama_kc')
                               ->get();
            
            $listRMFT = RMFT::with('ukerRelation')
                            ->orderBy('completename')
                            ->get()
                            ->map(function($rmft) {
                                // Set kode_kc from uker relation
                                $rmft->kode_kc = $rmft->ukerRelation ? $rmft->ukerRelation->kode_kanca : $rmft->kanca;
                                return $rmft;
                            });
            
            $listUnit = Aktivitas::select('kode_uker', 'nama_uker', 'kode_kc')
                                 ->distinct()
                                 ->orderBy('nama_uker')
                                 ->get();
            
            return view('aktivitas.index', compact('aktivitas', 'listKC', 'listRMFT', 'listUnit'));
            
        } elseif ($user->isManager()) {
            // Manager hanya lihat aktivitas di Kanca mereka dengan filter unit
            $query = Aktivitas::with(['rmft', 'assignedBy', 'nasabah'])
                              ->where('kode_kc', $user->kode_kanca);
            
            // Filter per RMFT
            if ($request->filled('rmft_id')) {
                $query->where('rmft_id', $request->rmft_id);
            }
            
            // Filter per Unit
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
            
            // Filter per Range Tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
            
            $aktivitas = $query->orderBy('tanggal', 'desc')->paginate(20);
            
            // Get list RMFT untuk dropdown filter (hanya RMFT di KC manager)
            $listRMFT = RMFT::whereHas('ukerRelation', function($q) use ($user) {
                            $q->where('kode_kanca', $user->kode_kanca);
                        })
                        ->orderBy('completename')
                        ->get();
            
            // Get list Unit untuk dropdown filter (hanya unit di KC manager)
            $listUnit = Aktivitas::select('kode_uker', 'nama_uker')
                                 ->where('kode_kc', $user->kode_kanca)
                                 ->distinct()
                                 ->orderBy('nama_uker')
                                 ->get();
            
            return view('aktivitas.index', compact('aktivitas', 'listRMFT', 'listUnit'));
        } else {
            // RMFT lihat aktivitas mereka sendiri
            $query = Aktivitas::with(['assignedBy', 'nasabah'])
                              ->where('rmft_id', $user->rmft_id);
            
            // Filter per Range Tanggal
            if ($request->filled('tanggal_dari')) {
                $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            }
            
            if ($request->filled('tanggal_sampai')) {
                $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
            }
            
            $aktivitas = $query->orderBy('tanggal', 'desc')->paginate(20);
            
            return view('aktivitas.index', compact('aktivitas'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $rmftData = null;
        $rmftList = [];
        
        // Get RMFT data if user is RMFT
        if ($user->isRMFT() && $user->rmft_id) {
            $rmftData = RMFT::with('ukerRelation')->find($user->rmft_id);
        }
        
        // Get RMFT list if user is Manager (only from their Kanca)
        if ($user->isManager()) {
            // Method 1: Try to match by nama_kanca first
            $rmftList = collect();
            
            if ($user->nama_kanca) {
                // Get all RMFT users who have rmft_id set
                $rmftList = User::where('role', 'rmft')
                               ->whereNotNull('rmft_id')
                               ->whereHas('rmftData', function($query) use ($user) {
                                   // Match kanca field in rmfts table with user's nama_kanca
                                   $query->where('kanca', 'LIKE', '%' . $user->nama_kanca . '%');
                               })
                               ->with('rmftData.ukerRelation')
                               ->orderBy('name', 'asc')
                               ->get();
            }
            
            // Method 2: If no results, try matching by kode_kanca via Uker relation
            if ($rmftList->isEmpty() && $user->kode_kanca) {
                // Get all RMFTs in this kanca by checking their uker's kode_kanca
                $rmftIdsInKanca = \App\Models\Uker::where('kode_kanca', $user->kode_kanca)
                                                 ->pluck('id');
                
                $rmftList = User::where('role', 'rmft')
                               ->whereNotNull('rmft_id')
                               ->whereHas('rmftData', function($query) use ($rmftIdsInKanca) {
                                   $query->whereIn('uker_id', $rmftIdsInKanca);
                               })
                               ->with('rmftData.ukerRelation')
                               ->orderBy('name', 'asc')
                               ->get();
            }
            
            // Method 3: Fallback - get all RMFT and filter in PHP (not recommended for large datasets)
            if ($rmftList->isEmpty()) {
                $rmftList = User::where('role', 'rmft')
                               ->whereNotNull('rmft_id')
                               ->where('kode_kanca', $user->kode_kanca)
                               ->with('rmftData.ukerRelation')
                               ->orderBy('name', 'asc')
                               ->get();
            }
        }
        
        // Get RMFT list if user is Admin (all RMFT)
        if ($user->isAdmin()) {
            $rmftList = User::where('role', 'rmft')
                           ->with('rmftData.ukerRelation')
                           ->orderBy('name', 'asc')
                           ->get();
        }
        
        // Get list rencana aktivitas yang aktif
        $rencanaAktivitas = RencanaAktivitas::where('is_active', true)
                                            ->orderBy('nama_rencana', 'asc')
                                            ->get();
        
        return view('aktivitas.create', compact('rmftData', 'rmftList', 'rencanaAktivitas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Daftar kategori yang tidak memerlukan CIFNO/norek
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
                         $request->kategori_strategi === 'Wingback' ||
                         $request->strategy_pipeline === 'Optimalisasi Business Cluster';
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'rmft_id' => 'required|exists:rmfts,id',
            'nama_rmft' => 'required|string',
            'pn' => 'required|string',
            'kode_kc' => 'required|string',
            'nama_kc' => 'required|string',
            'kode_uker' => 'required|string',
            'nama_uker' => 'required|string',
            'kode_uker_list' => 'nullable|string',
            'nama_uker_list' => 'nullable|string',
            'kelompok' => 'required|string',
            'strategy_pipeline' => 'required|string',
            'kategori_strategi' => 'nullable|string',
            'rencana_aktivitas' => 'nullable|string',
            'rencana_aktivitas_id' => 'nullable|exists:rencana_aktivitas,id',
            'segmen_nasabah' => 'required|string',
            'nama_nasabah' => 'required|string',
            'norek' => $isPipelineData ? 'nullable|string' : 'required|string',
            'rp_jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Check if multiple units are selected
        $multipleUnits = !empty($validated['kode_uker_list']) && 
                        !empty($validated['nama_uker_list']) &&
                        trim($validated['kode_uker_list']) !== '' &&
                        trim($validated['nama_uker_list']) !== '';
        
        if ($multipleUnits) {
            // Split units
            $kodeUkerArray = array_filter(array_map('trim', explode(',', $validated['kode_uker_list'])));
            $namaUkerArray = array_filter(array_map('trim', explode(',', $validated['nama_uker_list'])));
            
            // Pastikan ada lebih dari 1 unit, kalau cuma 1 unit gunakan logic single
            if (count($kodeUkerArray) <= 1) {
                $multipleUnits = false;
            }
        }
        
        if ($multipleUnits) {
            // Split units
            $kodeUkerArray = array_filter(array_map('trim', explode(',', $validated['kode_uker_list'])));
            $namaUkerArray = array_filter(array_map('trim', explode(',', $validated['nama_uker_list'])));
            
            $createdCount = 0;
            
            foreach ($kodeUkerArray as $index => $kodeUker) {
                $namaUker = $namaUkerArray[$index] ?? '';
                
                if (empty($kodeUker) || empty($namaUker)) {
                    continue;
                }
                
                // Check if nasabah exists dengan KC dan Unit yang sama (hanya jika ada norek)
                $nasabahId = null;
                if (!empty($validated['norek'])) {
                    $nasabah = Nasabah::where('norek', $validated['norek'])
                                      ->where('kode_kc', $validated['kode_kc'])
                                      ->where('kode_uker', trim($kodeUker))
                                      ->first();
                    
                    if (!$nasabah) {
                        // Buat nasabah baru dengan KC dan Unit
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
                
                // Create activity for this unit
                $activityData = [
                    'tanggal' => $validated['tanggal'],
                    'rmft_id' => $validated['rmft_id'],
                    'nama_rmft' => $validated['nama_rmft'],
                    'pn' => $validated['pn'],
                    'kode_kc' => $validated['kode_kc'],
                    'nama_kc' => $validated['nama_kc'],
                    'kode_uker' => trim($kodeUker),
                    'nama_uker' => trim($namaUker),
                    'kelompok' => $validated['kelompok'],
                    'strategy_pipeline' => $validated['strategy_pipeline'],
                    'kategori_strategi' => $validated['kategori_strategi'] ?? null,
                    'rencana_aktivitas' => $validated['rencana_aktivitas'],
                    'rencana_aktivitas_id' => $validated['rencana_aktivitas_id'],
                    'segmen_nasabah' => $validated['segmen_nasabah'],
                    'nama_nasabah' => $validated['nama_nasabah'],
                    'norek' => $validated['norek'],
                    'rp_jumlah' => $validated['rp_jumlah'],
                    'keterangan' => $validated['keterangan'],
                    'nasabah_id' => $nasabahId,
                ];
                
                // Jika Manager atau Admin yang membuat, ini adalah assignment
                if ($user->isManager() || $user->isAdmin()) {
                    $activityData['assigned_by'] = $user->id;
                    $activityData['tipe'] = 'assigned';
                } else {
                    $activityData['tipe'] = 'self';
                }
                
                Aktivitas::create($activityData);
                $createdCount++;
            }
            
            return redirect()->route('aktivitas.index')->with('success', "Berhasil membuat {$createdCount} aktivitas untuk {$createdCount} unit berbeda!");
            
        } else {
            // Single unit - existing logic
            // Untuk kategori pipeline tanpa norek, tidak perlu buat entry di tabel nasabah
            if (!empty($validated['norek'])) {
                $nasabah = Nasabah::where('norek', $validated['norek'])
                                  ->where('kode_kc', $validated['kode_kc'])
                                  ->where('kode_uker', $validated['kode_uker'])
                                  ->first();
                
                if (!$nasabah) {
                    // Buat nasabah baru dengan KC dan Unit
                    // Norek bisa sama selama KC dan Unit berbeda
                    $nasabah = Nasabah::create([
                        'norek' => $validated['norek'],
                        'nama_nasabah' => $validated['nama_nasabah'],
                        'segmen_nasabah' => $validated['segmen_nasabah'],
                        'kode_kc' => $validated['kode_kc'],
                        'nama_kc' => $validated['nama_kc'],
                        'kode_uker' => $validated['kode_uker'],
                        'nama_uker' => $validated['nama_uker'],
                    ]);
                }
                
                $validated['nasabah_id'] = $nasabah->id;
            } else {
                // Kategori pipeline tanpa norek, set nasabah_id ke null
                $validated['nasabah_id'] = null;
            }
            
            // Jika Manager atau Admin yang membuat, ini adalah assignment
            if ($user->isManager() || $user->isAdmin()) {
                $validated['assigned_by'] = $user->id;
                $validated['tipe'] = 'assigned';
            } else {
                $validated['tipe'] = 'self';
            }

            Aktivitas::create($validated);

            return redirect()->route('aktivitas.index')->with('success', 'Aktivitas berhasil ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aktivitas = Aktivitas::with(['rmft', 'nasabah'])->findOrFail($id);
        return view('aktivitas.show', compact('aktivitas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        // RMFT tidak bisa edit
        if ($user->isRMFT()) {
            abort(403, 'RMFT tidak memiliki akses untuk mengedit aktivitas.');
        }
        
        $aktivitas = Aktivitas::findOrFail($id);
        
        // Manager hanya bisa edit aktivitas di KC mereka (Admin bisa edit semua)
        if ($user->isManager() && $aktivitas->kode_kc != $user->kode_kanca) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get list rencana aktivitas yang aktif
        $rencanaAktivitas = RencanaAktivitas::where('is_active', true)
                                            ->orderBy('nama_rencana', 'asc')
                                            ->get();
        
        return view('aktivitas.edit', compact('aktivitas', 'rencanaAktivitas'));
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
        $user = Auth::user();
        
        // RMFT tidak bisa update
        if ($user->isRMFT()) {
            abort(403, 'RMFT tidak memiliki akses untuk mengupdate aktivitas.');
        }
        
        $aktivitas = Aktivitas::findOrFail($id);
        
        // Manager hanya bisa update aktivitas di KC mereka (Admin bisa update semua)
        if ($user->isManager() && $aktivitas->kode_kc != $user->kode_kanca) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'strategy_pipeline' => 'nullable|string',
            'kategori_strategi' => 'nullable|string',
            'rencana_aktivitas' => 'nullable|string',
            'rencana_aktivitas_id' => 'nullable|exists:rencana_aktivitas,id',
            'segmen_nasabah' => 'required|string',
            'nama_nasabah' => 'required|string',
            'norek' => 'nullable|string',
            'rp_jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $aktivitas->update($validated);

        return redirect()->route('aktivitas.index')->with('success', 'Aktivitas berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // RMFT tidak bisa delete
        if ($user->isRMFT()) {
            abort(403, 'RMFT tidak memiliki akses untuk menghapus aktivitas.');
        }
        
        $aktivitas = Aktivitas::findOrFail($id);
        
        // Manager hanya bisa delete aktivitas di KC mereka (Admin bisa delete semua)
        if ($user->isManager() && $aktivitas->kode_kc != $user->kode_kanca) {
            abort(403, 'Unauthorized action.');
        }
        
        $aktivitas->delete();

        return redirect()->route('aktivitas.index')->with('success', 'Aktivitas berhasil dihapus!');
    }
    
    /**
     * Show feedback form
     */
    public function feedback($id)
    {
        $user = Auth::user();
        
        // Hanya RMFT yang bisa memberikan feedback
        if (!$user->isRMFT()) {
            abort(403, 'Hanya RMFT yang bisa memberikan feedback.');
        }
        
        $aktivitas = Aktivitas::findOrFail($id);
        
        // RMFT hanya bisa feedback aktivitas mereka sendiri
        if ($aktivitas->rmft_id != $user->rmft_id) {
            abort(403, 'Anda hanya bisa memberikan feedback untuk aktivitas Anda sendiri.');
        }
        
        return view('aktivitas.feedback', compact('aktivitas'));
    }
    
    /**
     * Store feedback
     */
    public function storeFeedback(Request $request, $id)
    {
        $user = Auth::user();
        
        // Hanya RMFT yang bisa memberikan feedback
        if (!$user->isRMFT()) {
            abort(403, 'Hanya RMFT yang bisa memberikan feedback.');
        }
        
        $aktivitas = Aktivitas::findOrFail($id);
        
        // RMFT hanya bisa feedback aktivitas mereka sendiri
        if ($aktivitas->rmft_id != $user->rmft_id) {
            abort(403, 'Anda hanya bisa memberikan feedback untuk aktivitas Anda sendiri.');
        }
        
        $validated = $request->validate([
            'status_realisasi' => 'required|in:tercapai,tidak_tercapai,lebih',
            'nominal_realisasi' => 'required_if:status_realisasi,tidak_tercapai,lebih|nullable|string',
            'keterangan_realisasi' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ], [
            'nominal_realisasi.required_if' => 'Nominal realisasi harus diisi.',
        ]);
        
        // Validasi custom berdasarkan status realisasi
        if (isset($validated['nominal_realisasi'])) {
            // Hapus format Rupiah untuk perbandingan (hapus "Rp", ".", dll)
            $nominalRealisasi = (int) preg_replace('/[^0-9]/', '', $validated['nominal_realisasi']);
            $targetNominal = (int) preg_replace('/[^0-9]/', '', $aktivitas->rp_jumlah);
            
            // Jika status "tidak_tercapai", nominal tidak boleh melebihi target
            if ($validated['status_realisasi'] === 'tidak_tercapai' && $nominalRealisasi > $targetNominal) {
                return back()->withErrors([
                    'nominal_realisasi' => 'Realisasi tidak boleh melebihi target karena status "Tidak Tercapai"! Target: Rp ' . number_format($targetNominal, 0, ',', '.')
                ])->withInput();
            }
            
            // Jika status "lebih", nominal harus lebih besar dari target
            if ($validated['status_realisasi'] === 'lebih' && $nominalRealisasi <= $targetNominal) {
                return back()->withErrors([
                    'nominal_realisasi' => 'Realisasi harus lebih besar dari target karena status "Melebihi Target"! Target: Rp ' . number_format($targetNominal, 0, ',', '.')
                ])->withInput();
            }
        }
        
        // Jika tercapai, gunakan rp_jumlah sebagai nominal_realisasi
        if ($validated['status_realisasi'] === 'tercapai') {
            $validated['nominal_realisasi'] = $aktivitas->rp_jumlah;
        }
        
        $validated['tanggal_feedback'] = now();
        
        $aktivitas->update($validated);
        
        return redirect()->route('aktivitas.index')->with('success', 'Feedback berhasil disimpan!');
    }

    /**
     * Delete all aktivitas (Admin only)
     */
    public function deleteAll()
    {
        $user = Auth::user();
        
        // Hanya Admin yang bisa delete all
        if (!$user->isAdmin()) {
            abort(403, 'Hanya Admin yang bisa menghapus semua data aktivitas.');
        }
        
        try {
            $count = Aktivitas::count();
            Aktivitas::truncate();
            
            return redirect()->route('aktivitas.index')
                           ->with('success', "Berhasil menghapus semua data aktivitas ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('aktivitas.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    /**
     * Show bulk sick/leave form (RMFT only)
     */
    public function showSickLeaveForm()
    {
        $user = Auth::user();
        
        // Hanya RMFT yang bisa akses
        if (!$user->isRMFT()) {
            abort(403, 'Hanya RMFT yang bisa mengakses fitur ini.');
        }
        
        return view('aktivitas.sick-leave');
    }
    
    /**
     * Process bulk sick/leave update (RMFT only)
     */
    public function processSickLeave(Request $request)
    {
        $user = Auth::user();
        
        // Hanya RMFT yang bisa akses
        if (!$user->isRMFT()) {
            abort(403, 'Hanya RMFT yang bisa mengakses fitur ini.');
        }
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|in:Sakit,Izin',
        ]);
        
        // Ambil semua aktivitas RMFT di tanggal tersebut yang belum ada feedback
        // Status 'belum' atau null dianggap belum ada feedback
        $aktivitas = Aktivitas::where('rmft_id', $user->rmft_id)
                              ->whereDate('tanggal', $validated['tanggal'])
                              ->where(function($query) {
                                  $query->whereNull('status_realisasi')
                                        ->orWhere('status_realisasi', 'belum');
                              })
                              ->get();
        
        if ($aktivitas->isEmpty()) {
            return redirect()->route('aktivitas.sick-leave.form')
                           ->with('error', 'Tidak ada aktivitas yang ditemukan pada tanggal tersebut atau semua aktivitas sudah memiliki feedback.');
        }
        
        $count = 0;
        foreach ($aktivitas as $item) {
            $item->update([
                'status_realisasi' => 'tidak_tercapai',
                'nominal_realisasi' => '0',
                'keterangan_realisasi' => $validated['keterangan'],
                'tanggal_feedback' => now(),
            ]);
            $count++;
        }
        
        return redirect()->route('aktivitas.index')
                       ->with('success', "Berhasil menandai {$count} aktivitas pada tanggal {$validated['tanggal']} dengan keterangan: {$validated['keterangan']}");
    }

    /**
     * Get rekap data for pipeline summary
     */
    public function rekap(Request $request)
    {
        $user = Auth::user();
        
        // Build query berdasarkan role user
        $query = Aktivitas::select(
            'pn',
            'nama_kc',
            'nama_rmft',
            'nama_nasabah as nama_pemilik',
            'norek as no_rekening',
            \DB::raw('SUM(rp_jumlah) as pipeline'),
            \DB::raw('SUM(COALESCE(nominal_realisasi, 0)) as realisasi'),
            \DB::raw('GROUP_CONCAT(DISTINCT COALESCE(keterangan_realisasi, keterangan) SEPARATOR ", ") as keterangan'),
            \DB::raw('NULL as validasi')
        );

        if ($user->isAdmin()) {
            // Admin bisa lihat semua dengan filter
            if ($request->filled('kode_kc')) {
                $query->where('kode_kc', $request->kode_kc);
            }
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
        } elseif ($user->isManager()) {
            // Manager hanya lihat data KC-nya
            $query->where('kode_kc', $user->kode_kanca);
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
        } else {
            // RMFT hanya lihat data sendiri
            $query->where('rmft_id', $user->rmft_id);
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Group by RMFT and Nasabah
        $rekap = $query->groupBy('pn', 'nama_kc', 'nama_rmft', 'nama_nasabah', 'norek')
                      ->orderBy('pn')
                      ->get();

        return response()->json($rekap);
    }

    /**
     * Export rekap data to Excel or CSV
     */
    public function exportRekap(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'excel');
        
        // Build query berdasarkan role user
        $query = Aktivitas::select(
            'pn',
            'nama_kc',
            'nama_rmft',
            'nama_nasabah as nama_pemilik',
            'norek as no_rekening',
            \DB::raw('SUM(rp_jumlah) as pipeline'),
            \DB::raw('SUM(COALESCE(nominal_realisasi, 0)) as realisasi'),
            \DB::raw('GROUP_CONCAT(DISTINCT COALESCE(keterangan_realisasi, keterangan) SEPARATOR ", ") as keterangan'),
            \DB::raw('NULL as validasi')
        );

        if ($user->isAdmin()) {
            if ($request->filled('kode_kc')) {
                $query->where('kode_kc', $request->kode_kc);
            }
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
        } elseif ($user->isManager()) {
            $query->where('kode_kc', $user->kode_kanca);
            if ($request->filled('kode_uker')) {
                $query->where('kode_uker', $request->kode_uker);
            }
        } else {
            $query->where('rmft_id', $user->rmft_id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $rekap = $query->groupBy('pn', 'nama_kc', 'nama_rmft', 'nama_nasabah', 'norek')
                      ->orderBy('pn')
                      ->get();

        $timestamp = now()->format('Y-m-d_His');
        $filename = "rekap_pipeline_{$timestamp}";

        if ($format === 'csv') {
            return $this->exportToCsv($rekap, $filename);
        } else {
            return $this->exportToExcel($rekap, $filename);
        }
    }

    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No', 'Nama KC', 'PN', 'Nama RMFT', 'Nama Pemilik', 'No Rekening', 'Pipeline (Rp)', 'Realisasi (Rp)', 'Keterangan', 'Validasi']);
            
            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->nama_kc ?? '-',
                    $row->pn ?? '-',
                    $row->nama_rmft ?? '-',
                    $row->nama_pemilik ?? '-',
                    $row->no_rekening ?? '-',
                    number_format($row->pipeline, 0, ',', '.'),
                    number_format($row->realisasi, 0, ',', '.'),
                    $row->keterangan ?? '-',
                    $row->validasi ?? '-'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToExcel($data, $filename)
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style>';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th { background-color: #28a745; color: white; font-weight: bold; padding: 10px; border: 1px solid #ddd; text-align: center; }';
        $html .= 'td { padding: 8px; border: 1px solid #ddd; }';
        $html .= '.number { text-align: right; } .text { text-align: left; } .center { text-align: center; }';
        $html .= '</style></head><body><table><thead><tr>';
        $html .= '<th>No</th><th>Nama KC</th><th>PN</th><th>Nama RMFT</th><th>Nama Pemilik</th><th>No Rekening</th><th>Pipeline (Rp)</th><th>Realisasi (Rp)</th><th>Keterangan</th><th>Validasi</th>';
        $html .= '</tr></thead><tbody>';
        
        foreach ($data as $index => $row) {
            $html .= '<tr>';
            $html .= '<td class="center">' . ($index + 1) . '</td>';
            $html .= '<td class="text">' . ($row->nama_kc ?? '-') . '</td>';
            $html .= '<td class="text">' . ($row->pn ?? '-') . '</td>';
            $html .= '<td class="text">' . ($row->nama_rmft ?? '-') . '</td>';
            $html .= '<td class="text">' . ($row->nama_pemilik ?? '-') . '</td>';
            $html .= '<td class="text">' . ($row->no_rekening ?? '-') . '</td>';
            $html .= '<td class="number">' . number_format($row->pipeline, 0, ',', '.') . '</td>';
            $html .= '<td class="number">' . number_format($row->realisasi, 0, ',', '.') . '</td>';
            $html .= '<td class="text">' . ($row->keterangan ?? '-') . '</td>';
            $html .= '<td class="center">' . ($row->validasi ?? '-') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table></body></html>';

        return response($html, 200, $headers);
    }
}
