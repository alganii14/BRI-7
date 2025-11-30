<?php

namespace App\Http\Controllers;

use App\Models\UserAktifCasaKecil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAktifCasaKecilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = UserAktifCasaKecil::query();
        
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
                $q->where('nama_debitur', 'like', "%{$search}%")
                  ->orWhere('norek_pinjaman', 'like', "%{$search}%")
                  ->orWhere('norek_simpanan', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%")
                  ->orWhere('uker', 'like', "%{$search}%")
                  ->orWhere('kanca', 'like', "%{$search}%");
            });
        }

        $userAktifCasaKecils = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = UserAktifCasaKecil::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('user-aktif-casa-kecil.index', compact('userAktifCasaKecils', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user-aktif-casa-kecil.create');
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
            'kode_kanca' => 'required|string',
            'kanca' => 'required|string',
            'kode_uker' => 'required|string',
            'uker' => 'required|string',
            'cifno' => 'required|string',
            'norek_pinjaman' => 'nullable|string',
            'norek_simpanan' => 'nullable|string',
            'balance' => 'nullable|string',
            'volume' => 'nullable|string',
            'nama_debitur' => 'required|string',
            'plafon' => 'nullable|string',
            'pn_pengelola_1' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        UserAktifCasaKecil::create($validated);

        return redirect()->route('user-aktif-casa-kecil.index')
            ->with('success', 'Data user aktif casa kecil berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserAktifCasaKecil $userAktifCasaKecil)
    {
        return view('user-aktif-casa-kecil.show', compact('userAktifCasaKecil'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UserAktifCasaKecil $userAktifCasaKecil)
    {
        return view('user-aktif-casa-kecil.edit', compact('userAktifCasaKecil'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserAktifCasaKecil $userAktifCasaKecil)
    {
        $validated = $request->validate([
            'kode_kanca' => 'required|string',
            'kanca' => 'required|string',
            'kode_uker' => 'required|string',
            'uker' => 'required|string',
            'cifno' => 'required|string',
            'norek_pinjaman' => 'nullable|string',
            'norek_simpanan' => 'nullable|string',
            'balance' => 'nullable|string',
            'volume' => 'nullable|string',
            'nama_debitur' => 'required|string',
            'plafon' => 'nullable|string',
            'pn_pengelola_1' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $userAktifCasaKecil->update($validated);

        return redirect()->route('user-aktif-casa-kecil.index')
            ->with('success', 'Data user aktif casa kecil berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserAktifCasaKecil $userAktifCasaKecil)
    {
        $userAktifCasaKecil->delete();

        return redirect()->route('user-aktif-casa-kecil.index')
            ->with('success', 'Data user aktif casa kecil berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('user-aktif-casa-kecil.import');
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
                // Skip empty rows and rows with no meaningful data
                if (count($row) >= 13 && !empty(array_filter($row))) {
                    $batch[] = [
                        'kode_kanca' => trim($row[0]) ?: null,
                        'kanca' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'uker' => trim($row[3]) ?: null,
                        'cifno' => trim($row[4]) ?: null,
                        'norek_pinjaman' => trim($row[5]) ?: null,
                        'norek_simpanan' => trim($row[6]) ?: null,
                        'balance' => trim($row[7]) ?: null,
                        'volume' => trim($row[8]) ?: null,
                        'nama_debitur' => trim($row[9]) ?: null,
                        'plafon' => trim($row[10]) ?: null,
                        'pn_pengelola_1' => trim($row[11]) ?: null,
                        'keterangan' => trim($row[12]) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('user_aktif_casa_kecils')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                } else {
                    $skippedRows++;
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('user_aktif_casa_kecils')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            $message = '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris';
            if ($skippedRows > 0) {
                $message .= ' (Dilewati: ' . $skippedRows . ' baris)';
            }
            $message .= ' | Delimiter: ' . \App\Helpers\CsvHelper::getDelimiterName($delimiter);

            return redirect()->route('user-aktif-casa-kecil.index')
                            ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all user aktif casa kecil records
     */
    public function deleteAll()
    {
        try {
            $count = UserAktifCasaKecil::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('user_aktif_casa_kecils')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('user-aktif-casa-kecil.index')
                                ->with('success', '✓ Berhasil menghapus semua data user aktif casa kecil! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('user-aktif-casa-kecil.index')
                            ->with('success', '✓ Tidak ada data user aktif casa kecil untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
