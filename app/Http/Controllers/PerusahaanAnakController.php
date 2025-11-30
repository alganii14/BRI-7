<?php

namespace App\Http\Controllers;

use App\Models\PerusahaanAnak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerusahaanAnakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PerusahaanAnak::query();
        
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
                $q->where('nama_partner_vendor', 'like', "%{$search}%")
                  ->orWhere('jenis_usaha', 'like', "%{$search}%")
                  ->orWhere('nama_pic_partner', 'like', "%{$search}%")
                  ->orWhere('nama_perusahaan_anak', 'like', "%{$search}%")
                  ->orWhere('cabang_induk_terdekat', 'like', "%{$search}%");
            });
        }

        $perusahaanAnaks = $query->latest()->paginate(20);
        
        // Get available years
        $availableYears = PerusahaanAnak::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('perusahaan-anak.index', compact('perusahaanAnaks', 'month', 'year', 'availableYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('perusahaan-anak.create');
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
            'nama_partner_vendor' => 'nullable|string',
            'jenis_usaha' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk_terdekat' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
            'nama_pic_partner' => 'nullable|string',
            'posisi_pic_partner' => 'nullable|string',
            'hp_pic_partner' => 'nullable|string',
            'nama_perusahaan_anak' => 'nullable|string',
            'status_pipeline' => 'nullable|string',
            'rekening_terbentuk' => 'nullable|string',
            'cif_terbentuk' => 'nullable|string',
        ]);

        PerusahaanAnak::create($validated);

        return redirect()->route('perusahaan-anak.index')
            ->with('success', 'Data perusahaan anak berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PerusahaanAnak $perusahaanAnak)
    {
        return view('perusahaan-anak.show', compact('perusahaanAnak'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PerusahaanAnak $perusahaanAnak)
    {
        return view('perusahaan-anak.edit', compact('perusahaanAnak'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PerusahaanAnak $perusahaanAnak)
    {
        $validated = $request->validate([
            'nama_partner_vendor' => 'nullable|string',
            'jenis_usaha' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk_terdekat' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
            'nama_pic_partner' => 'nullable|string',
            'posisi_pic_partner' => 'nullable|string',
            'hp_pic_partner' => 'nullable|string',
            'nama_perusahaan_anak' => 'nullable|string',
            'status_pipeline' => 'nullable|string',
            'rekening_terbentuk' => 'nullable|string',
            'cif_terbentuk' => 'nullable|string',
        ]);

        $perusahaanAnak->update($validated);

        return redirect()->route('perusahaan-anak.index')
            ->with('success', 'Data perusahaan anak berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PerusahaanAnak $perusahaanAnak)
    {
        $perusahaanAnak->delete();

        return redirect()->route('perusahaan-anak.index')
            ->with('success', 'Data perusahaan anak berhasil dihapus.');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        $totalPerusahaanAnak = PerusahaanAnak::count();
        return view('perusahaan-anak.import', compact('totalPerusahaanAnak'));
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
            $header = fgetcsv($handle, 0, ';'); // Skip header row, using semicolon delimiter
            
            $batch = [];
            $batchSize = 1000; // Process 1000 rows at a time
            $totalInserted = 0;

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                if (count($row) >= 14 && !empty(array_filter($row))) {
                    $batch[] = [
                        'nama_partner_vendor' => trim($row[0]) ?: null,
                        'jenis_usaha' => trim($row[1]) ?: null,
                        'alamat' => trim($row[2]) ?: null,
                        'kode_cabang_induk' => trim($row[3]) ?: null,
                        'cabang_induk_terdekat' => trim($row[4]) ?: null,
                        'kode_uker' => trim($row[5]) ?: null,
                        'nama_uker' => trim($row[6]) ?: null,
                        'nama_pic_partner' => trim($row[7]) ?: null,
                        'posisi_pic_partner' => trim($row[8]) ?: null,
                        'hp_pic_partner' => trim($row[9]) ?: null,
                        'nama_perusahaan_anak' => trim($row[10]) ?: null,
                        'status_pipeline' => trim($row[11]) ?: null,
                        'rekening_terbentuk' => trim($row[12]) ?: null,
                        'cif_terbentuk' => trim($row[13]) ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('perusahaan_anaks')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                DB::table('perusahaan_anaks')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('perusahaan-anak.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all perusahaan anak records
     */
    public function deleteAll()
    {
        try {
            $count = PerusahaanAnak::count();
            
            if ($count > 0) {
                // Disable foreign key checks temporarily
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                
                // Truncate the table
                DB::table('perusahaan_anaks')->truncate();
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('perusahaan-anak.index')
                                ->with('success', '✓ Berhasil menghapus semua data perusahaan anak! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('perusahaan-anak.index')
                            ->with('success', '✓ Tidak ada data perusahaan anak untuk dihapus.');
                            
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Get list perusahaan anak for AJAX (for dropdown in aktivitas)
     */
    public function getListPerusahaanAnak(Request $request)
    {
        $search = $request->get('search', '');
        
        $perusahaanAnaks = PerusahaanAnak::when($search, function($query, $search) {
                return $query->where('nama_partner_vendor', 'like', "%{$search}%")
                             ->orWhere('nama_perusahaan_anak', 'like', "%{$search}%");
            })
            ->select('id', 'nama_partner_vendor', 'nama_perusahaan_anak', 'cabang_induk_terdekat')
            ->limit(50)
            ->get();
        
        return response()->json($perusahaanAnaks);
    }
}
