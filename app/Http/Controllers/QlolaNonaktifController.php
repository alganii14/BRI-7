<?php

namespace App\Http\Controllers;

use App\Models\QlolaNonaktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QlolaNonaktifController extends Controller
{
    public function index(Request $request)
    {
        $query = QlolaNonaktif::query();

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

        $qlolaNonaktifs = $query->orderBy('id', 'desc')->paginate(20);

        return view('qlola-nonaktif.index', compact('qlolaNonaktifs'));
    }

    public function create()
    {
        return view('qlola-nonaktif.create');
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

        QlolaNonaktif::create($validated);

        return redirect()->route('qlola-nonaktif.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function show(QlolaNonaktif $qlolaNonaktif)
    {
        return view('qlola-nonaktif.show', compact('qlolaNonaktif'));
    }

    public function edit(QlolaNonaktif $qlolaNonaktif)
    {
        return view('qlola-nonaktif.edit', compact('qlolaNonaktif'));
    }

    public function update(Request $request, QlolaNonaktif $qlolaNonaktif)
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

        $qlolaNonaktif->update($validated);

        return redirect()->route('qlola-nonaktif.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy(QlolaNonaktif $qlolaNonaktif)
    {
        $qlolaNonaktif->delete();

        return redirect()->route('qlola-nonaktif.index')
            ->with('success', 'Data berhasil dihapus');
    }

    public function importForm()
    {
        return view('qlola-nonaktif.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $tanggalPosisiData = $request->input('tanggal_posisi_data');
        $tanggalUploadData = $request->input('tanggal_upload_data');
        
        DB::beginTransaction();
        try {
            // Auto-detect delimiter using CsvHelper
            $delimiter = \App\Helpers\CsvHelper::detectDelimiter($path);
            
            $handle = fopen($path, 'r');
            $header = fgetcsv($handle, 0, $delimiter); // Read header
            
            // Log header for debugging
            \Log::info('CSV Import - Header:', ['header' => $header, 'delimiter' => $delimiter, 'count' => count($header)]);
            
            // Read first data row for debugging
            $firstRow = fgetcsv($handle, 0, $delimiter);
            if ($firstRow) {
                \Log::info('CSV Import - First Row:', ['row' => $firstRow, 'count' => count($firstRow)]);
                // Reset file pointer to after header
                fseek($handle, 0);
                fgetcsv($handle, 0, $delimiter); // Skip header again
            }
            
            $batchData = [];
            $batchSize = 1000;
            $totalInserted = 0;
            $skippedRows = 0;
            
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    $skippedRows++;
                    continue;
                }
                
                if (count($row) < 12) {
                    $skippedRows++;
                    continue;
                }
                
                // Clean and validate data
                $cleanedRow = array_map(function($value) {
                    return trim($value ?? '');
                }, $row);
                
                // Skip if all important fields are empty
                if (empty($cleanedRow[4]) && empty($cleanedRow[8])) { // cifno and nama_debitur
                    $skippedRows++;
                    continue;
                }
                
                $batchData[] = [
                    'kode_kanca' => $cleanedRow[0] ?: null,
                    'kanca' => $cleanedRow[1] ?: null,
                    'kode_uker' => $cleanedRow[2] ?: null,
                    'uker' => $cleanedRow[3] ?: null,
                    'cifno' => $cleanedRow[4] ?: null,
                    'norek_pinjaman' => $cleanedRow[5] ?: null,
                    'norek_simpanan' => $cleanedRow[6] ?: null,
                    'balance' => $cleanedRow[7] ?: null,
                    'nama_debitur' => $cleanedRow[8] ?: null,
                    'plafon' => $cleanedRow[9] ?: null,
                    'pn_pengelola_1' => $cleanedRow[10] ?: null,
                    'keterangan' => $cleanedRow[11] ?? null,
                    'tanggal_posisi_data' => $tanggalPosisiData,
                    'tanggal_upload_data' => $tanggalUploadData,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if (count($batchData) >= $batchSize) {
                    QlolaNonaktif::insert($batchData);
                    $totalInserted += count($batchData);
                    $batchData = [];
                }
            }
            
            if (!empty($batchData)) {
                QlolaNonaktif::insert($batchData);
                $totalInserted += count($batchData);
            }
            
            fclose($handle);
            DB::commit();
            
            $message = '✓ Import berhasil! Total data: ' . number_format($totalInserted, 0, ',', '.') . ' baris';
            if ($skippedRows > 0) {
                $message .= ' (Dilewati: ' . $skippedRows . ' baris)';
            }
            $message .= ' | Delimiter: ' . \App\Helpers\CsvHelper::getDelimiterName($delimiter);
            
            return redirect()->route('qlola-nonaktif.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '✗ Gagal mengimport CSV: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            QlolaNonaktif::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->route('qlola-nonaktif.index')
                ->with('success', 'Semua data berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
