<?php

namespace App\Http\Controllers;

use App\Models\AumDpk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AumDpkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AumDpk::query();
        
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
                $q->where('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%")
                  ->orWhere('unit_kerja', 'like', "%{$search}%");
            });
        }

        $aumDpks = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = AumDpk::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('aum-dpk.index', compact('aumDpks', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('aum-dpk.create');
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
            'slp' => 'nullable|string',
            'pbo' => 'nullable|string',
            'cif' => 'nullable|string',
            'id_prioritas' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'nomor_rekening' => 'nullable|string',
            'aum' => 'nullable|string',
        ]);

        AumDpk::create($validated);

        return redirect()->route('aum-dpk.index')
            ->with('success', 'Data AUM DPK berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(AumDpk $aumDpk)
    {
        return view('aum-dpk.show', compact('aumDpk'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AumDpk $aumDpk)
    {
        return view('aum-dpk.edit', compact('aumDpk'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AumDpk $aumDpk)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'slp' => 'nullable|string',
            'pbo' => 'nullable|string',
            'cif' => 'nullable|string',
            'id_prioritas' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'nomor_rekening' => 'nullable|string',
            'aum' => 'nullable|string',
        ]);

        $aumDpk->update($validated);

        return redirect()->route('aum-dpk.index')
            ->with('success', 'Data AUM DPK berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AumDpk $aumDpk)
    {
        $aumDpk->delete();

        return redirect()->route('aum-dpk.index')
            ->with('success', 'Data AUM DPK berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('aum-dpk.import');
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
            
            // Read and detect delimiter
            $firstLine = fgets($handle);
            $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
            rewind($handle);
            
            // Skip header row
            $header = fgetcsv($handle, 0, $delimiter);
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) >= 11 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_cabang_induk' => trim($row[0]) ?: null,
                        'cabang_induk' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'unit_kerja' => trim($row[3]) ?: null,
                        'slp' => trim($row[4]) ?: null,
                        'pbo' => trim($row[5]) ?: null,
                        'cif' => trim($row[6]) ?: null,
                        'id_prioritas' => trim($row[7]) ?: null,
                        'nama_nasabah' => trim($row[8]) ?: null,
                        'nomor_rekening' => trim($row[9]) ?: null,
                        'aum' => trim($row[10]) ?: null,
                        'tanggal_posisi_data' => $tanggalPosisiData,
                        'tanggal_upload_data' => $tanggalUploadData,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('aum_dpks')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('aum_dpks')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('aum-dpk.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all aum dpk records
     */
    public function deleteAll()
    {
        try {
            $count = AumDpk::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('aum_dpks')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('aum-dpk.index')
                                ->with('success', '✓ Berhasil menghapus semua data AUM DPK! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('aum-dpk.index')
                            ->with('success', '✓ Tidak ada data AUM DPK untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
