<?php

namespace App\Http\Controllers;

use App\Models\QlolaUserTidakAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QlolaUserTidakAktifController extends Controller
{
    public function index(Request $request)
    {
        $query = QlolaUserTidakAktif::query();
        
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
                $q->where('nama_nasabah', 'like', "%{$search}%")
                  ->orWhere('norek_simpanan', 'like', "%{$search}%")
                  ->orWhere('norek_pinjaman', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%")
                  ->orWhere('uker', 'like', "%{$search}%")
                  ->orWhere('kanca', 'like', "%{$search}%");
            });
        }

        $qlolaUserTidakAktifs = $query->latest()->paginate(20);
        
        $availableYears = QlolaUserTidakAktif::selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year');
        
        return view('qlola-user-tidak-aktif.index', compact('qlolaUserTidakAktifs', 'month', 'year', 'availableYears'));
    }

    public function create()
    {
        return view('qlola-user-tidak-aktif.create');
    }

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

        QlolaUserTidakAktif::create($validated);

        return redirect()->route('qlola-user-tidak-aktif.index')
            ->with('success', 'Data Qlola User Tidak Aktif berhasil ditambahkan.');
    }

    public function show(QlolaUserTidakAktif $qlolaUserTidakAktif)
    {
        return view('qlola-user-tidak-aktif.show', compact('qlolaUserTidakAktif'));
    }

    public function edit(QlolaUserTidakAktif $qlolaUserTidakAktif)
    {
        return view('qlola-user-tidak-aktif.edit', compact('qlolaUserTidakAktif'));
    }

    public function update(Request $request, QlolaUserTidakAktif $qlolaUserTidakAktif)
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

        $qlolaUserTidakAktif->update($validated);

        return redirect()->route('qlola-user-tidak-aktif.index')
            ->with('success', 'Data Qlola User Tidak Aktif berhasil diupdate.');
    }

    public function destroy(QlolaUserTidakAktif $qlolaUserTidakAktif)
    {
        $qlolaUserTidakAktif->delete();

        return redirect()->route('qlola-user-tidak-aktif.index')
            ->with('success', 'Data Qlola User Tidak Aktif berhasil dihapus.');
    }

    public function importForm()
    {
        return view('qlola-user-tidak-aktif.import');
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

            $handle = fopen($path, 'r');
            
            fgetcsv($handle, 0, ';');
            
            $batch = [];
            $batchSize = 1000;
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('qlola_user_tidak_aktifs')->insert($batch);
                        $totalInserted += count($batch);
                        $batch = [];
                    }
                }
            }

            if (!empty($batch)) {
                DB::table('qlola_user_tidak_aktifs')->insert($batch);
                $totalInserted += count($batch);
            }

            fclose($handle);
            DB::commit();

            return redirect()->route('qlola-user-tidak-aktif.index')
                            ->with('success', '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = QlolaUserTidakAktif::count();
            
            if ($count > 0) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::table('qlola_user_tidak_aktifs')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                
                return redirect()->route('qlola-user-tidak-aktif.index')
                                ->with('success', '✓ Berhasil menghapus semua data! Total data terhapus: ' . number_format($count, 0, ',', '.') . ' baris');
            }
            
            return redirect()->route('qlola-user-tidak-aktif.index')
                            ->with('success', '✓ Tidak ada data untuk dihapus.');
                            
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->back()->with('error', '✗ Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
