<?php

namespace App\Http\Controllers;

use App\Models\MerchantSavol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantSavolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = MerchantSavol::query();
        
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
                $q->where('nama_merchant', 'like', "%{$search}%")
                  ->orWhere('tid_store_id', 'like', "%{$search}%")
                  ->orWhere('norekening', 'like', "%{$search}%")
                  ->orWhere('cif', 'like', "%{$search}%")
                  ->orWhere('uker', 'like', "%{$search}%")
                  ->orWhere('kanca', 'like', "%{$search}%");
            });
        }

        $merchantSavols = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = MerchantSavol::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('merchant-savol.index', compact('merchantSavols', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('merchant-savol.create');
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
            'kode_kanca' => 'nullable|string',
            'kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'uker' => 'nullable|string',
            'jenis_merchant' => 'nullable|string',
            'tid_store_id' => 'nullable|string',
            'nama_merchant' => 'nullable|string',
            'alamat_merchant' => 'nullable|string',
            'norekening' => 'nullable|string',
            'cif' => 'nullable|string',
            'savol_bulan_lalu' => 'nullable|string',
            'casa_akhir_bulan' => 'nullable|string',
        ]);

        MerchantSavol::create($validated);

        return redirect()->route('merchant-savol.index')
            ->with('success', 'Data merchant savol berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(MerchantSavol $merchantSavol)
    {
        return view('merchant-savol.show', compact('merchantSavol'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MerchantSavol $merchantSavol)
    {
        return view('merchant-savol.edit', compact('merchantSavol'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MerchantSavol $merchantSavol)
    {
        $validated = $request->validate([
            'kode_kanca' => 'nullable|string',
            'kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'uker' => 'nullable|string',
            'jenis_merchant' => 'nullable|string',
            'tid_store_id' => 'nullable|string',
            'nama_merchant' => 'nullable|string',
            'alamat_merchant' => 'nullable|string',
            'norekening' => 'nullable|string',
            'cif' => 'nullable|string',
            'savol_bulan_lalu' => 'nullable|string',
            'casa_akhir_bulan' => 'nullable|string',
        ]);

        $merchantSavol->update($validated);

        return redirect()->route('merchant-savol.index')
            ->with('success', 'Data merchant savol berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MerchantSavol $merchantSavol)
    {
        $merchantSavol->delete();

        return redirect()->route('merchant-savol.index')
            ->with('success', 'Data merchant savol berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('merchant-savol.import');
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

        try {
            DB::beginTransaction();

            // Auto-detect delimiter
            $delimiter = \App\Helpers\CsvHelper::detectDelimiter($path);

            $handle = fopen($path, 'r');
            $header = fgetcsv($handle, 0, $delimiter); // Skip header row
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;
            $skippedRows = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) >= 12 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_kanca' => trim($row[0]) ?: null,
                        'kanca' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'uker' => trim($row[3]) ?: null,
                        'jenis_merchant' => trim($row[4]) ?: null,
                        'tid_store_id' => trim($row[5]) ?: null,
                        'nama_merchant' => trim($row[6]) ?: null,
                        'alamat_merchant' => trim($row[7]) ?: null,
                        'norekening' => trim($row[8]) ?: null,
                        'cif' => trim($row[9]) ?: null,
                        'savol_bulan_lalu' => trim($row[10]) ?: null,
                        'casa_akhir_bulan' => trim($row[11]) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('merchant_savols')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                } else {
                    $skippedRows++;
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('merchant_savols')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            $message = '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris';
            if ($skippedRows > 0) {
                $message .= ' (Dilewati: ' . $skippedRows . ' baris)';
            }
            $message .= ' | Delimiter: ' . \App\Helpers\CsvHelper::getDelimiterName($delimiter);

            return redirect()->route('merchant-savol.index')
                            ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all merchant savol records
     */
    public function deleteAll()
    {
        try {
            $count = MerchantSavol::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('merchant_savols')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('merchant-savol.index')
                                ->with('success', '✓ Berhasil menghapus semua data merchant savol! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('merchant-savol.index')
                            ->with('success', '✓ Tidak ada data merchant savol untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
