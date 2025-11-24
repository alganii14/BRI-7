<?php

namespace App\Http\Controllers;

use App\Models\Strategi8;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Strategi8Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Strategi8::query();
        
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
                  ->orWhere('no_rekening', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%")
                  ->orWhere('cabang_induk', 'like', "%{$search}%")
                  ->orWhere('unit_kerja', 'like', "%{$search}%");
            });
        }

        $strategi8 = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = Strategi8::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('strategi8.index', compact('strategi8', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('strategi8.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'cifno' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'segmentasi' => 'nullable|string',
            'jenis_simpanan' => 'nullable|string',
            'saldo_last_eom' => 'nullable|string',
            'saldo_terupdate' => 'nullable|string',
            'delta' => 'nullable|string',
        ]);

        Strategi8::create($validated);

        return redirect()->route('strategi8.index')
            ->with('success', 'Data Strategi 8 berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Strategi8 $strategi8)
    {
        return view('strategi8.show', compact('strategi8'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Strategi8 $strategi8)
    {
        return view('strategi8.edit', compact('strategi8'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Strategi8 $strategi8)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'cifno' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'segmentasi' => 'nullable|string',
            'jenis_simpanan' => 'nullable|string',
            'saldo_last_eom' => 'nullable|string',
            'saldo_terupdate' => 'nullable|string',
            'delta' => 'nullable|string',
        ]);

        $strategi8->update($validated);

        return redirect()->route('strategi8.index')
            ->with('success', 'Data Strategi 8 berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Strategi8 $strategi8)
    {
        $strategi8->delete();

        return redirect()->route('strategi8.index')
            ->with('success', 'Data Strategi 8 berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('strategi8.import');
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
            $header = fgetcsv($handle, 0, ';'); // Skip header row with semicolon delimiter
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (count($row) >= 12 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_cabang_induk' => trim($row[0]) ?: null,
                        'cabang_induk' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'unit_kerja' => trim($row[3]) ?: null,
                        'cifno' => trim($row[4]) ?: null,
                        'no_rekening' => trim($row[5]) ?: null,
                        'nama_nasabah' => trim($row[6]) ?: null,
                        'segmentasi' => trim($row[7]) ?: null,
                        'jenis_simpanan' => trim($row[8]) ?: null,
                        'saldo_last_eom' => trim($row[9]) ?: null,
                        'saldo_terupdate' => trim($row[10]) ?: null,
                        'delta' => trim($row[11]) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('strategi8')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (count($batch) > 0) {
                DB::table('strategi8')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('strategi8.index')
                ->with('success', "Berhasil mengimport {$totalInserted} data Strategi 8.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Delete all records
     */
    public function deleteAll()
    {
        try {
            $count = Strategi8::count();
            Strategi8::truncate();

            return redirect()->route('strategi8.index')
                ->with('success', "Berhasil menghapus {$count} data Strategi 8.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
