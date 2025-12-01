<?php

namespace App\Http\Controllers;

use App\Models\Layering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayeringController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Layering::query();
        
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
                  ->orWhere('unit_kerja', 'like', "%{$search}%")
                  ->orWhere('cabang_induk', 'like', "%{$search}%")
                  ->orWhere('kode_cabang_induk', 'like', "%{$search}%");
            });
        }

        $layerings = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = Layering::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('layering.index', compact('layerings', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layering.create');
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
            'cifno' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'segmentasi' => 'nullable|string',
            'jenis_simpanan' => 'nullable|string',
            'saldo_last_eom' => 'nullable|string',
            'saldo_terupdate' => 'nullable|string',
            'delta' => 'nullable|string',
        ]);

        Layering::create($validated);

        return redirect()->route('layering.index')
            ->with('success', 'Data layering berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Layering $layering)
    {
        return view('layering.show', compact('layering'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Layering $layering)
    {
        return view('layering.edit', compact('layering'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Layering $layering)
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

        $layering->update($validated);

        return redirect()->route('layering.index')
            ->with('success', 'Data layering berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Layering $layering)
    {
        $layering->delete();

        return redirect()->route('layering.index')
            ->with('success', 'Data layering berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('layering.import');
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
            
            // Set locale untuk parsing angka Indonesia
            setlocale(LC_NUMERIC, 'id_ID');
            
            // Skip header row - baca dan buang baris pertama
            $header = fgets($handle);
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($line = fgets($handle)) !== false) {
                // Parse CSV dengan delimiter semicolon
                $row = str_getcsv($line, ';');
                
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
                        'tanggal_posisi_data' => $tanggalPosisiData,
                        'tanggal_upload_data' => $tanggalUploadData,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('layerings')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('layerings')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('layering.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all layering records
     */
    public function deleteAll()
    {
        try {
            $count = Layering::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('layerings')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('layering.index')
                                ->with('success', '✓ Berhasil menghapus semua data layering! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('layering.index')
                            ->with('success', '✓ Tidak ada data layering untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
