<?php

namespace App\Http\Controllers;

use App\Models\Rekap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapController extends Controller
{
    public function index()
    {
        $rekaps = Rekap::orderBy('created_at', 'desc')->paginate(20);
        return view('rekap.index', compact('rekaps'));
    }

    public function importForm()
    {
        return view('rekap.import');
    }

    public function downloadTemplate()
    {
        $filename = 'template_import_rekap.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['Nama KC', 'PN', 'Nama RMFT', 'Nama Pemilik', 'No Rekening', 'Pipeline', 'Realisasi', 'Keterangan', 'Validasi']);
            
            // Sample data
            fputcsv($file, ['KC Kuningan', '282247', 'Adam Nugraha', 'Andi Prasetyo', '1234567890', '150000000', '120000000', 'Realisasi sesuai target', 'Approved']);
            fputcsv($file, ['KC Sumedang', '254416', 'ANDRI PURNAMA', 'Budi Santoso', '0987654321', '200000000', '180000000', 'Cross selling produk', 'Pending']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'dari_tanggal' => 'required|date',
            'sampai_tanggal' => 'required|date|after_or_equal:dari_tanggal',
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $tanggal = $request->input('dari_tanggal'); // Gunakan dari_tanggal sebagai tanggal default
            $extension = $file->getClientOriginalExtension();

            if ($extension === 'csv') {
                $this->importCsv($file, $tanggal);
            } else {
                $this->importExcel($file, $tanggal);
            }

            return redirect()->route('rekap.index')
                           ->with('success', 'Data rekap berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    private function importCsv($file, $tanggal)
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        // Detect delimiter - read first line to check
        $firstLine = fgets($handle);
        rewind($handle);
        
        // Check if semicolon is used as delimiter
        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        }
        
        // Skip header
        $header = fgetcsv($handle, 0, $delimiter);
        
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) continue;
            
            if (count($row) < 9) continue;
            
            // Check if first column is a number (NO column), skip it
            $offset = 0;
            if (is_numeric($row[0]) && strlen($row[0]) <= 5) {
                $offset = 1; // Skip NO column
            }
            
            Rekap::create([
                'nama_kc' => trim($row[$offset + 0] ?? ''),
                'tanggal' => $tanggal,
                'pn' => trim($row[$offset + 1] ?? ''),
                'nama_rmft' => trim($row[$offset + 2] ?? ''),
                'nama_pemilik' => trim($row[$offset + 3] ?? ''),
                'no_rekening' => trim($row[$offset + 4] ?? ''),
                'pipeline' => $this->parseNumber($row[$offset + 5] ?? 0),
                'realisasi' => $this->parseNumber($row[$offset + 6] ?? 0),
                'keterangan' => trim($row[$offset + 7] ?? ''),
                'validasi' => $row[$offset + 8] ?? null,
            ]);
        }
        
        fclose($handle);
    }

    private function importExcel($file, $tanggal)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        // Skip header
        array_shift($rows);
        
        foreach ($rows as $row) {
            if (empty($row[0]) && empty($row[1])) continue;
            
            // Check if first column is a number (NO column), skip it
            $offset = 0;
            if (is_numeric($row[0]) && strlen($row[0]) <= 5) {
                $offset = 1; // Skip NO column
            }
            
            Rekap::create([
                'nama_kc' => $row[$offset + 0] ?? null,
                'tanggal' => $tanggal,
                'pn' => $row[$offset + 1] ?? null,
                'nama_rmft' => $row[$offset + 2] ?? null,
                'nama_pemilik' => $row[$offset + 3] ?? null,
                'no_rekening' => $row[$offset + 4] ?? null,
                'pipeline' => $this->parseNumber($row[$offset + 5] ?? 0),
                'realisasi' => $this->parseNumber($row[$offset + 6] ?? 0),
                'keterangan' => $row[$offset + 7] ?? null,
                'validasi' => $row[$offset + 8] ?? null,
            ]);
        }
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float)$value;
        }
        
        // Remove Rp, dots (thousand separator), and spaces
        // Keep comma as decimal separator
        $value = str_replace(['Rp', ' '], '', $value);
        $value = str_replace('.', '', $value); // Remove thousand separator
        $value = str_replace(',', '.', $value); // Replace comma with dot for decimal
        
        return is_numeric($value) ? (float)$value : 0;
    }

    public function deleteAll()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Hanya Admin yang bisa menghapus semua data rekap.');
        }
        
        try {
            $count = Rekap::count();
            Rekap::truncate();
            
            return redirect()->route('rekap.index')
                           ->with('success', "Berhasil menghapus semua data rekap ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('rekap.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
