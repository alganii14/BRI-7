<?php

namespace App\Http\Controllers;

use App\Models\OptimalisasiBusinessCluster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptimalisasiBusinessClusterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = OptimalisasiBusinessCluster::query();
        
        $month = $request->get('month');
        $year = $request->get('year');
        
        // Filter by year
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        // Filter by month
        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_usaha_pusat_bisnis', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('cabang_induk', 'like', "%{$search}%")
                  ->orWhere('unit_kerja', 'like', "%{$search}%")
                  ->orWhere('nama_tenaga_pemasar', 'like', "%{$search}%")
                  ->orWhere('kode_cabang_induk', 'like', "%{$search}%")
                  ->orWhere('kode_uker', 'like', "%{$search}%");
            });
        }

        $optimalisasiBusinessClusters = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = OptimalisasiBusinessCluster::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('optimalisasi-business-cluster.index', compact('optimalisasiBusinessClusters', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('optimalisasi-business-cluster.create');
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
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'tag_zona_unggulan' => 'nullable|string',
            'nomor_rekening' => 'nullable|string',
            'nama_usaha_pusat_bisnis' => 'nullable|string',
            'nama_tenaga_pemasar' => 'nullable|string',
        ]);

        OptimalisasiBusinessCluster::create($validated);

        return redirect()->route('optimalisasi-business-cluster.index')
            ->with('success', 'Data optimalisasi business cluster berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(OptimalisasiBusinessCluster $optimalisasiBusinessCluster)
    {
        return view('optimalisasi-business-cluster.show', compact('optimalisasiBusinessCluster'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(OptimalisasiBusinessCluster $optimalisasiBusinessCluster)
    {
        return view('optimalisasi-business-cluster.edit', compact('optimalisasiBusinessCluster'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OptimalisasiBusinessCluster $optimalisasiBusinessCluster)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'tag_zona_unggulan' => 'nullable|string',
            'nomor_rekening' => 'nullable|string',
            'nama_usaha_pusat_bisnis' => 'nullable|string',
            'nama_tenaga_pemasar' => 'nullable|string',
        ]);

        $optimalisasiBusinessCluster->update($validated);

        return redirect()->route('optimalisasi-business-cluster.index')
            ->with('success', 'Data optimalisasi business cluster berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OptimalisasiBusinessCluster $optimalisasiBusinessCluster)
    {
        $optimalisasiBusinessCluster->delete();

        return redirect()->route('optimalisasi-business-cluster.index')
            ->with('success', 'Data optimalisasi business cluster berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('optimalisasi-business-cluster.import');
    }

    /**
     * Import CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $tanggalPosisiData = $request->input('tanggal_posisi_data');
        $tanggalUploadData = $request->input('tanggal_upload_data');

        try {
            DB::beginTransaction();

            $handle = fopen($path, 'r');
            $header = fgetcsv($handle, 0, ';'); // Skip header row with semicolon delimiter
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (count($row) >= 8 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_cabang_induk' => trim($row[0]) ?: null,
                        'cabang_induk' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'unit_kerja' => trim($row[3]) ?: null,
                        'tag_zona_unggulan' => trim($row[4]) ?: null,
                        'nomor_rekening' => trim($row[5]) ?: null,
                        'nama_usaha_pusat_bisnis' => trim($row[6]) ?: null,
                        'nama_tenaga_pemasar' => trim($row[7]) ?: null,
                        'tanggal_posisi_data' => $tanggalPosisiData,
                        'tanggal_upload_data' => $tanggalUploadData,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('optimalisasi_business_clusters')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('optimalisasi_business_clusters')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('optimalisasi-business-cluster.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all optimalisasi business cluster records
     */
    public function deleteAll()
    {
        try {
            $count = OptimalisasiBusinessCluster::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('optimalisasi_business_clusters')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('optimalisasi-business-cluster.index')
                                ->with('success', '✓ Berhasil menghapus semua data optimalisasi business cluster! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('optimalisasi-business-cluster.index')
                            ->with('success', '✓ Tidak ada data optimalisasi business cluster untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
