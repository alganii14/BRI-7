<?php

namespace App\Http\Controllers;

use App\Models\DebiturBelumMemilikiQlola;
use App\Helpers\CsvHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebiturBelumMemilikiQlolaController extends Controller
{
    public function index(Request $request)
    {
        $query = DebiturBelumMemilikiQlola::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_debitur', 'like', "%{$search}%")
                  ->orWhere('cifno', 'like', "%{$search}%")
                  ->orWhere('norek_pinjaman', 'like', "%{$search}%")
                  ->orWhere('norek_simpanan', 'like', "%{$search}%")
                  ->orWhere('kanca', 'like', "%{$search}%")
                  ->orWhere('uker', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan tahun
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter berdasarkan bulan
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $debiturQlolas = $query->orderBy('id', 'desc')->paginate(20);

        return view('debitur-belum-memiliki-qlola.index', compact('debiturQlolas'));
    }

    public function create()
    {
        return view('debitur-belum-memiliki-qlola.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kanca' => 'required',
            'kanca' => 'required',
            'kode_uker' => 'required',
            'uker' => 'required',
            'cifno' => 'required',
            'norek_pinjaman' => 'nullable',
            'norek_simpanan' => 'nullable',
            'balance' => 'nullable',
            'nama_debitur' => 'required',
            'plafon' => 'nullable',
            'pn_pengelola_1' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        DebiturBelumMemilikiQlola::create($validated);

        return redirect()->route('debitur-belum-memiliki-qlola.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(DebiturBelumMemilikiQlola $debiturBelumMemilikiQlola)
    {
        return view('debitur-belum-memiliki-qlola.show', compact('debiturBelumMemilikiQlola'));
    }

    public function edit(DebiturBelumMemilikiQlola $debiturBelumMemilikiQlola)
    {
        return view('debitur-belum-memiliki-qlola.edit', compact('debiturBelumMemilikiQlola'));
    }

    public function update(Request $request, DebiturBelumMemilikiQlola $debiturBelumMemilikiQlola)
    {
        $validated = $request->validate([
            'kode_kanca' => 'required',
            'kanca' => 'required',
            'kode_uker' => 'required',
            'uker' => 'required',
            'cifno' => 'required',
            'norek_pinjaman' => 'nullable',
            'norek_simpanan' => 'nullable',
            'balance' => 'nullable',
            'nama_debitur' => 'required',
            'plafon' => 'nullable',
            'pn_pengelola_1' => 'nullable',
            'keterangan' => 'nullable',
        ]);

        $debiturBelumMemilikiQlola->update($validated);

        return redirect()->route('debitur-belum-memiliki-qlola.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy(DebiturBelumMemilikiQlola $debiturBelumMemilikiQlola)
    {
        $debiturBelumMemilikiQlola->delete();

        return redirect()->route('debitur-belum-memiliki-qlola.index')
            ->with('success', 'Data berhasil dihapus');
    }

    public function importForm()
    {
        return view('debitur-belum-memiliki-qlola.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        DB::beginTransaction();
        try {
            // Auto-detect delimiter (comma or semicolon)
            $delimiter = CsvHelper::detectDelimiter($path);
            
            $handle = fopen($path, 'r');
            $header = fgetcsv($handle, 0, $delimiter); // Skip header
            
            $batchData = [];
            $batchSize = 1000;
            $totalInserted = 0;
            $skippedRows = 0;
            
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) < 12) {
                    $skippedRows++;
                    continue;
                }
                
                $batchData[] = [
                    'kode_kanca' => trim($row[0]),
                    'kanca' => trim($row[1]),
                    'kode_uker' => trim($row[2]),
                    'uker' => trim($row[3]),
                    'cifno' => trim($row[4]),
                    'norek_pinjaman' => trim($row[5]),
                    'norek_simpanan' => trim($row[6]),
                    'balance' => trim($row[7]),
                    'nama_debitur' => trim($row[8]),
                    'plafon' => trim($row[9]),
                    'pn_pengelola_1' => trim($row[10]),
                    'keterangan' => trim($row[11]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($batchData) >= $batchSize) {
                    DebiturBelumMemilikiQlola::insert($batchData);
                    $totalInserted += count($batchData);
                    $batchData = [];
                }
            }
            
            if (!empty($batchData)) {
                DebiturBelumMemilikiQlola::insert($batchData);
                $totalInserted += count($batchData);
            }
            
            fclose($handle);
            DB::commit();
            
            $message = '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris';
            if ($skippedRows > 0) {
                $message .= ' (Dilewati: ' . $skippedRows . ' baris)';
            }
            $message .= ' | Delimiter: ' . CsvHelper::getDelimiterName($delimiter);
            
            return redirect()->route('debitur-belum-memiliki-qlola.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = DebiturBelumMemilikiQlola::count();
            
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DebiturBelumMemilikiQlola::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->route('debitur-belum-memiliki-qlola.index')
                ->with('success', '✓ Berhasil menghapus semua data! Total: ' . number_format($count, 0, ',', '.') . ' baris');
        } catch (\Exception $e) {
            return back()->with('error', '✗ Error: ' . $e->getMessage());
        }
    }
}
