<?php

namespace App\Http\Controllers;

use App\Models\QlolaNonDebitur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QlolaNonDebiturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = QlolaNonDebitur::query();
        
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
                  ->orWhere('norek_simpanan', 'like', "%{$search}%")
                  ->orWhere('norek_pinjaman', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%")
                  ->orWhere('uker', 'like', "%{$search}%")
                  ->orWhere('kanca', 'like', "%{$search}%");
            });
        }

        $qlolaNonDebiturs = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = QlolaNonDebitur::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('qlola-non-debitur.index', compact('qlolaNonDebiturs', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlola-non-debitur.create');
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
            'cifno' => 'nullable|string',
            'norek_simpanan' => 'nullable|string',
            'norek_pinjaman' => 'nullable|string',
            'balance' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        QlolaNonDebitur::create($validated);

        return redirect()->route('qlola-non-debitur.index')
            ->with('success', 'Data Qlola Non Debitur berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(QlolaNonDebitur $qlolaNonDebitur)
    {
        return view('qlola-non-debitur.show', compact('qlolaNonDebitur'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(QlolaNonDebitur $qlolaNonDebitur)
    {
        return view('qlola-non-debitur.edit', compact('qlolaNonDebitur'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QlolaNonDebitur $qlolaNonDebitur)
    {
        $validated = $request->validate([
            'kode_kanca' => 'nullable|string',
            'kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'uker' => 'nullable|string',
            'cifno' => 'nullable|string',
            'norek_simpanan' => 'nullable|string',
            'norek_pinjaman' => 'nullable|string',
            'balance' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $qlolaNonDebitur->update($validated);

        return redirect()->route('qlola-non-debitur.index')
            ->with('success', 'Data Qlola Non Debitur berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(QlolaNonDebitur $qlolaNonDebitur)
    {
        $qlolaNonDebitur->delete();

        return redirect()->route('qlola-non-debitur.index')
            ->with('success', 'Data Qlola Non Debitur berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('qlola-non-debitur.import');
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
            
            // Read and skip header row
            $header = fgetcsv($handle, 0, ';');
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (count($row) >= 10 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_kanca' => trim($row[0]) ?: null,
                        'kanca' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'uker' => trim($row[3]) ?: null,
                        'cifno' => trim($row[4]) ?: null,
                        'norek_simpanan' => trim($row[5]) ?: null,
                        'norek_pinjaman' => trim($row[6]) ?: null,
                        'balance' => trim($row[7]) ?: null,
                        'nama_nasabah' => trim($row[8]) ?: null,
                        'keterangan' => trim($row[9]) ?: null,
                        'tanggal_posisi_data' => $tanggalPosisiData,
                        'tanggal_upload_data' => $tanggalUploadData,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('qlola_non_debiturs')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('qlola_non_debiturs')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('qlola-non-debitur.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all qlola non debitur records
     */
    public function deleteAll()
    {
        try {
            $count = QlolaNonDebitur::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('qlola_non_debiturs')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('qlola-non-debitur.index')
                                ->with('success', '✓ Berhasil menghapus semua data Qlola Non Debitur! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('qlola-non-debitur.index')
                            ->with('success', '✓ Tidak ada data Qlola Non Debitur untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
