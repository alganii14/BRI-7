<?php

namespace App\Http\Controllers;

use App\Models\ExistingPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExistingPayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = ExistingPayroll::query();
        
        $month = $request->get('month');
        $year = $request->get('year');
        
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        
        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('corporate_code', 'like', "%{$search}%")
                  ->orWhere('cabang_induk', 'like', "%{$search}%")
                  ->orWhere('kode_cabang_induk', 'like', "%{$search}%");
            });
        }

        $existingPayrolls = $query->latest()->paginate(20);
        
        $availableYears = ExistingPayroll::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('existing-payroll.index', compact('existingPayrolls', 'month', 'year', 'availableYears'));
    }

    public function create()
    {
        return view('existing-payroll.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'corporate_code' => 'nullable|string',
            'nama_perusahaan' => 'nullable|string',
            'jumlah_rekening' => 'nullable|string',
            'saldo_rekening' => 'nullable|string',
        ]);

        ExistingPayroll::create($validated);

        return redirect()->route('existing-payroll.index')
            ->with('success', 'Data existing payroll berhasil ditambahkan.');
    }

    public function show(ExistingPayroll $existingPayroll)
    {
        return view('existing-payroll.show', compact('existingPayroll'));
    }

    public function edit(ExistingPayroll $existingPayroll)
    {
        return view('existing-payroll.edit', compact('existingPayroll'));
    }

    public function update(Request $request, ExistingPayroll $existingPayroll)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'corporate_code' => 'nullable|string',
            'nama_perusahaan' => 'nullable|string',
            'jumlah_rekening' => 'nullable|string',
            'saldo_rekening' => 'nullable|string',
        ]);

        $existingPayroll->update($validated);

        return redirect()->route('existing-payroll.index')
            ->with('success', 'Data existing payroll berhasil diupdate.');
    }

    public function destroy(ExistingPayroll $existingPayroll)
    {
        $existingPayroll->delete();

        return redirect()->route('existing-payroll.index')
            ->with('success', 'Data existing payroll berhasil dihapus.');
    }

    public function importForm()
    {
        return view('existing-payroll.import');
    }

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
            $header = fgetcsv($handle, 0, $delimiter); // Skip header
            
            $batch = [];
            $batchSize = 1000;
            $totalInserted = 0;
            $skippedRows = 0;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) >= 6 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_cabang_induk' => trim($row[0]) ?: null,
                        'cabang_induk' => trim($row[1]) ?: null,
                        'corporate_code' => trim($row[2]) ?: null,
                        'nama_perusahaan' => trim($row[3]) ?: null,
                        'jumlah_rekening' => trim($row[4]) ?: null,
                        'saldo_rekening' => trim($row[5]) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('existing_payroll')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                } else {
                    $skippedRows++;
                }
            }

            if (!empty($batch)) {
                DB::table('existing_payroll')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            $message = '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris';
            if ($skippedRows > 0) {
                $message .= ' (Dilewati: ' . $skippedRows . ' baris)';
            }
            $message .= ' | Delimiter: ' . \App\Helpers\CsvHelper::getDelimiterName($delimiter);

            return redirect()->route('existing-payroll.index')
                            ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = ExistingPayroll::count();
            
            if ($count > 0) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('existing_payroll')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('existing-payroll.index')
                                ->with('success', '✓ Berhasil menghapus semua data existing payroll! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('existing-payroll.index')
                            ->with('success', '✓ Tidak ada data existing payroll untuk dihapus.');
                            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
