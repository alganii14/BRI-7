<?php

namespace App\Http\Controllers;

use App\Models\PenurunanPrioritasRitelMikro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenurunanPrioritasRitelMikroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PenurunanPrioritasRitelMikro::query();
        
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
                  ->orWhere('cabang_induk', 'like', "%{$search}%");
            });
        }

        $penurunanPrioritasRitelMikros = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = PenurunanPrioritasRitelMikro::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('penurunan-prioritas-ritel-mikro.index', compact('penurunanPrioritasRitelMikros', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('penurunan-prioritas-ritel-mikro.create');
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
            'segmentasi_bpr' => 'nullable|string',
            'jenis_simpanan' => 'nullable|string',
            'saldo_last_eom' => 'nullable|numeric',
            'saldo_terupdate' => 'nullable|numeric',
            'delta' => 'nullable|numeric',
        ]);

        PenurunanPrioritasRitelMikro::create($validated);

        return redirect()->route('penurunan-prioritas-ritel-mikro.index')
            ->with('success', 'Data penurunan prioritas ritel & mikro berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PenurunanPrioritasRitelMikro $penurunanPrioritasRitelMikro)
    {
        return view('penurunan-prioritas-ritel-mikro.show', compact('penurunanPrioritasRitelMikro'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PenurunanPrioritasRitelMikro $penurunanPrioritasRitelMikro)
    {
        return view('penurunan-prioritas-ritel-mikro.edit', compact('penurunanPrioritasRitelMikro'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PenurunanPrioritasRitelMikro $penurunanPrioritasRitelMikro)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'cifno' => 'nullable|string',
            'no_rekening' => 'nullable|string',
            'nama_nasabah' => 'nullable|string',
            'segmentasi_bpr' => 'nullable|string',
            'jenis_simpanan' => 'nullable|string',
            'saldo_last_eom' => 'nullable|numeric',
            'saldo_terupdate' => 'nullable|numeric',
            'delta' => 'nullable|numeric',
        ]);

        $penurunanPrioritasRitelMikro->update($validated);

        return redirect()->route('penurunan-prioritas-ritel-mikro.index')
            ->with('success', 'Data penurunan prioritas ritel & mikro berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PenurunanPrioritasRitelMikro $penurunanPrioritasRitelMikro)
    {
        $penurunanPrioritasRitelMikro->delete();

        return redirect()->route('penurunan-prioritas-ritel-mikro.index')
            ->with('success', 'Data penurunan prioritas ritel & mikro berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('penurunan-prioritas-ritel-mikro.import');
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
            $batchSize = 1000;
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (count($row) >= 12 && !empty(array_filter($row))) {
                    // Parse numeric values with Indonesian format (. as thousands separator, , as decimal)
                    $saldoLastEom = $this->parseIndonesianNumber(trim($row[9]));
                    $saldoTerupdate = $this->parseIndonesianNumber(trim($row[10]));
                    $delta = $this->parseIndonesianNumber(trim($row[11]));
                    
                    $batch[] = [
                        'kode_cabang_induk' => trim($row[0]) ?: null,
                        'cabang_induk' => trim($row[1]) ?: null,
                        'kode_uker' => trim($row[2]) ?: null,
                        'unit_kerja' => trim($row[3]) ?: null,
                        'cifno' => trim($row[4]) ?: null,
                        'no_rekening' => trim($row[5]) ?: null,
                        'nama_nasabah' => trim($row[6]) ?: null,
                        'segmentasi_bpr' => trim($row[7]) ?: null,
                        'jenis_simpanan' => trim($row[8]) ?: null,
                        'saldo_last_eom' => $saldoLastEom,
                        'saldo_terupdate' => $saldoTerupdate,
                        'delta' => $delta,
                        'tanggal_posisi_data' => $tanggalPosisiData,
                        'tanggal_upload_data' => $tanggalUploadData,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('penurunan_prioritas_ritel_mikro')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('penurunan_prioritas_ritel_mikro')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('penurunan-prioritas-ritel-mikro.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Parse Indonesian number format to decimal
     */
    private function parseIndonesianNumber($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove dots (thousands separator) and replace comma with dot (decimal separator)
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        
        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Delete all records
     */
    public function deleteAll()
    {
        try {
            $count = PenurunanPrioritasRitelMikro::count();
            
            if ($count > 0) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('penurunan_prioritas_ritel_mikro')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('penurunan-prioritas-ritel-mikro.index')
                                ->with('success', '✓ Berhasil menghapus semua data! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('penurunan-prioritas-ritel-mikro.index')
                            ->with('success', '✓ Tidak ada data untuk dihapus.');
                            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
