<?php

namespace App\Http\Controllers;

use App\Models\PotensiPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PotensiPayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PotensiPayroll::query();
        
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
                $q->where('perusahaan', 'like', "%{$search}%")
                  ->orWhere('cabang_induk', 'like', "%{$search}%")
                  ->orWhere('kode_cabang_induk', 'like', "%{$search}%")
                  ->orWhere('estimasi_pekerja', 'like', "%{$search}%");
            });
        }

        $potensiPayrolls = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = PotensiPayroll::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('potensi-payroll.index', compact('potensiPayrolls', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('potensi-payroll.create');
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
            'perusahaan' => 'nullable|string',
            'estimasi_pekerja' => 'nullable|string',
        ]);

        PotensiPayroll::create($validated);

        return redirect()->route('potensi-payroll.index')
            ->with('success', 'Data potensi payroll berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PotensiPayroll $potensiPayroll)
    {
        return view('potensi-payroll.show', compact('potensiPayroll'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PotensiPayroll $potensiPayroll)
    {
        return view('potensi-payroll.edit', compact('potensiPayroll'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PotensiPayroll $potensiPayroll)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'perusahaan' => 'nullable|string',
            'estimasi_pekerja' => 'nullable|string',
        ]);

        $potensiPayroll->update($validated);

        return redirect()->route('potensi-payroll.index')
            ->with('success', 'Data potensi payroll berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PotensiPayroll $potensiPayroll)
    {
        $potensiPayroll->delete();

        return redirect()->route('potensi-payroll.index')
            ->with('success', 'Data potensi payroll berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('potensi-payroll.import');
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

            $handle = fopen($path, 'r');
            $header = fgetcsv($handle, 1000, ';'); // Skip header row, using semicolon delimiter
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                // Skip rows with empty data or only containing semicolons
                if (count($row) >= 4 && !empty(trim($row[2]))) { // Check if perusahaan is not empty
                    $batch[] = [
                        'kode_cabang_induk' => trim($row[0]) ?: null,
                        'cabang_induk' => trim($row[1]) ?: null,
                        'perusahaan' => trim($row[2]) ?: null,
                        'estimasi_pekerja' => trim($row[3]) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('potensi_payrolls')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('potensi_payrolls')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('potensi-payroll.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all potensi payroll records
     */
    public function deleteAll()
    {
        try {
            $count = PotensiPayroll::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('potensi_payrolls')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('potensi-payroll.index')
                                ->with('success', '✓ Berhasil menghapus semua data potensi payroll! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('potensi-payroll.index')
                            ->with('success', '✓ Tidak ada data potensi payroll untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
