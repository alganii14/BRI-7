<?php

namespace App\Http\Controllers;

use App\Models\Uker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $user = auth()->user();
        
        $ukers = Uker::query()
            ->when($user->isManager() && $user->kode_kanca, function($query) use ($user) {
                // Manager hanya lihat uker di KC mereka
                return $query->where('kode_kanca', $user->kode_kanca);
            })
            // Admin melihat semua uker
            ->when($search, function($query, $search) {
                return $query->where('sub_kanca', 'like', "%{$search}%")
                    ->orWhere('kanca', 'like', "%{$search}%")
                    ->orWhere('kanwil', 'like', "%{$search}%")
                    ->orWhere('segment', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('uker.index', compact('ukers', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil daftar Kanca yang unique
        $kancaList = Uker::select('kode_kanca', 'kanca', 'kanwil', 'kode_kanwil')
            ->whereNotNull('kode_kanca')
            ->where('kode_kanca', '!=', '')
            ->groupBy('kode_kanca', 'kanca', 'kanwil', 'kode_kanwil')
            ->orderBy('kanca', 'asc')
            ->get();
        
        return view('uker.create', compact('kancaList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_sub_kanca' => 'required',
            'sub_kanca' => 'required',
            'segment' => 'nullable',
            'kode_kanca' => 'nullable',
            'kanca' => 'nullable',
            'kanwil' => 'nullable',
            'kode_kanwil' => 'nullable',
        ]);

        Uker::create($validated);

        return redirect()->route('uker.index')
            ->with('success', 'Data Uker berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Uker $uker)
    {
        return view('uker.show', compact('uker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Uker $uker)
    {
        return view('uker.edit', compact('uker'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Uker $uker)
    {
        $validated = $request->validate([
            'kode_sub_kanca' => 'required',
            'sub_kanca' => 'required',
            'segment' => 'nullable',
            'kode_kanca' => 'nullable',
            'kanca' => 'nullable',
            'kanwil' => 'nullable',
            'kode_kanwil' => 'nullable',
        ]);

        $uker->update($validated);

        return redirect()->route('uker.index')
            ->with('success', 'Data Uker berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Uker $uker)
    {
        $uker->delete();

        return redirect()->route('uker.index')
            ->with('success', 'Data Uker berhasil dihapus!');
    }

    /**
     * Import CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // max 10MB
        ]);

        try {
            $file = $request->file('csv_file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle, 1000, ';');
            
            $imported = 0;
            $errors = [];
            
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                // Skip if row is empty
                if (empty(array_filter($row))) {
                    continue;
                }
                
                try {
                    // Check if record already exists based on kode_sub_kanca
                    $exists = Uker::where('kode_sub_kanca', $row[0])
                        ->where('sub_kanca', $row[1])
                        ->first();
                    
                    if (!$exists) {
                        Uker::create([
                            'kode_sub_kanca' => $row[0] ?? null,
                            'sub_kanca' => $row[1] ?? null,
                            'segment' => $row[2] ?? null,
                            'kode_kanca' => $row[3] ?? null,
                            'kanca' => $row[4] ?? null,
                            'kanwil' => $row[5] ?? null,
                            'kode_kanwil' => $row[6] ?? null,
                        ]);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error on row: " . implode(', ', $row) . " - " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            if ($imported > 0) {
                $message = "Berhasil import {$imported} data Uker!";
                if (count($errors) > 0) {
                    $message .= " Dengan " . count($errors) . " error.";
                }
                return redirect()->route('uker.index')->with('success', $message);
            } else {
                return redirect()->route('uker.index')->with('warning', 'Tidak ada data baru yang diimport. Semua data sudah ada atau terjadi error.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('uker.index')
                ->with('error', 'Gagal import CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all records
     */
    public function deleteAll()
    {
        try {
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Uker::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            return redirect()->route('uker.index')
                ->with('success', 'Semua data Uker berhasil dihapus!');
        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Pastikan di-enable kembali
            return redirect()->route('uker.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    /**
     * Get units by Kanca (for API)
     */
    public function getByKC(Request $request)
    {
        // If 'all' parameter is set, return all ukers for kanca list
        if ($request->get('all')) {
            $ukers = Uker::select('kode_kanca', 'kanca')
                ->whereNotNull('kode_kanca')
                ->where('kode_kanca', '!=', '')
                ->orderBy('kode_kanca', 'asc')
                ->get();
            
            return response()->json($ukers);
        }
        
        $kodeKc = $request->get('kode_kc');
        
        if (!$kodeKc) {
            return response()->json([]);
        }
        
        $ukers = Uker::where('kode_kanca', $kodeKc)
            ->orderBy('sub_kanca', 'asc')
            ->get(['id', 'kode_sub_kanca', 'sub_kanca', 'kode_kanca', 'kanca']);
        
        return response()->json($ukers);
    }
}
