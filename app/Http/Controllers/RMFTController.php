<?php

namespace App\Http\Controllers;

use App\Models\RMFT;
use App\Models\Uker;
use Illuminate\Http\Request;

class RMFTController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $user = auth()->user();
        
        $rmfts = RMFT::query()
            ->with('ukerRelation')
            ->when($user->isManager() && $user->nama_kanca, function($query) use ($user) {
                // Manager hanya lihat RMFT di KC mereka
                return $query->where('kanca', $user->nama_kanca);
            })
            // Admin melihat semua RMFT
            ->when($search, function($query, $search) {
                return $query->where('completename', 'like', "%{$search}%")
                    ->orWhere('pernr', 'like', "%{$search}%")
                    ->orWhere('kanca', 'like', "%{$search}%")
                    ->orWhere('kelompok_jabatan', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('rmft.index', compact('rmfts', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique KC list
        $kcList = Uker::select('kode_kanca', 'kanca')
            ->distinct()
            ->orderBy('kanca')
            ->get()
            ->unique('kode_kanca');
        
        return view('rmft.create', compact('kcList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pernr' => 'nullable',
            'completename' => 'required',
            'jg' => 'nullable',
            'esgdesc' => 'nullable',
            'kanca' => 'nullable',
            'uker_id' => 'nullable|exists:ukers,id',
            'uker' => 'nullable',
            'uker_tujuan' => 'nullable',
            'keterangan' => 'nullable',
            'kelompok_jabatan' => 'nullable',
        ]);

        RMFT::create($validated);

        return redirect()->route('rmft.index')
            ->with('success', 'Data RMFT berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(RMFT $rmft)
    {
        return view('rmft.show', compact('rmft'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RMFT $rmft)
    {
        // Get unique KC list
        $kcList = Uker::select('kode_kanca', 'kanca')
            ->distinct()
            ->orderBy('kanca')
            ->get()
            ->unique('kode_kanca');
        
        // Find current kode_kanca based on kanca name
        $currentKodeKanca = null;
        if ($rmft->kanca) {
            $ukerData = Uker::where('kanca', $rmft->kanca)->first();
            $currentKodeKanca = $ukerData ? $ukerData->kode_kanca : null;
        }
        
        return view('rmft.edit', compact('rmft', 'kcList', 'currentKodeKanca'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RMFT $rmft)
    {
        $validated = $request->validate([
            'pernr' => 'nullable',
            'completename' => 'required',
            'jg' => 'nullable',
            'esgdesc' => 'nullable',
            'kanca' => 'nullable',
            'uker_id' => 'nullable|exists:ukers,id',
            'uker' => 'nullable',
            'uker_tujuan' => 'nullable',
            'keterangan' => 'nullable',
            'kelompok_jabatan' => 'nullable',
        ]);

        $rmft->update($validated);

        return redirect()->route('rmft.index')
            ->with('success', 'Data RMFT berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RMFT $rmft)
    {
        $rmft->delete();

        return redirect()->route('rmft.index')
            ->with('success', 'Data RMFT berhasil dihapus!');
    }

    /**
     * Import CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Skip header row
            $header = fgetcsv($handle, 10000, ';');
            
            $imported = 0;
            $errors = [];
            
            while (($row = fgetcsv($handle, 10000, ';')) !== false) {
                // Skip if row is empty
                if (empty(array_filter($row))) {
                    continue;
                }
                
                try {
                    // Extract data from CSV
                    $pernr = $row[1] ?? null;
                    $completename = $row[2] ?? null;
                    $jg = $row[3] ?? null;
                    $esgdesc = $row[4] ?? null;
                    $kanca = $row[5] ?? null;
                    $uker = $row[6] ?? null;
                    $ukerTujuan = $row[7] ?? null;
                    $keterangan = $row[8] ?? null;
                    $kelompokJabatan = $row[9] ?? null;
                    
                    // Find matching Uker by Kanca name
                    $ukerRecord = null;
                    if ($kanca) {
                        // Clean up kanca name (remove extra spaces)
                        $kancaClean = trim($kanca);
                        $ukerRecord = Uker::where('kanca', 'like', "%{$kancaClean}%")->first();
                    }
                    
                    // Check if record already exists
                    $exists = RMFT::where('pernr', $pernr)
                        ->where('completename', $completename)
                        ->first();
                    
                    if (!$exists && $completename) {
                        RMFT::create([
                            'pernr' => $pernr,
                            'completename' => $completename,
                            'jg' => $jg,
                            'esgdesc' => $esgdesc,
                            'kanca' => $kanca,
                            'uker_id' => $ukerRecord ? $ukerRecord->id : null,
                            'uker' => $uker,
                            'uker_tujuan' => $ukerTujuan,
                            'keterangan' => $keterangan,
                            'kelompok_jabatan' => $kelompokJabatan,
                        ]);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error on row: " . implode(', ', array_slice($row, 0, 5)) . " - " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            if ($imported > 0) {
                $message = "Berhasil import {$imported} data RMFT!";
                if (count($errors) > 0) {
                    $message .= " Dengan " . count($errors) . " error.";
                }
                return redirect()->route('rmft.index')->with('success', $message);
            } else {
                return redirect()->route('rmft.index')->with('warning', 'Tidak ada data baru yang diimport. Semua data sudah ada atau terjadi error.');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('rmft.index')
                ->with('error', 'Gagal import CSV: ' . $e->getMessage());
        }
    }

    /**
     * Delete all records
     */
    public function deleteAll()
    {
        try {
            RMFT::truncate();
            return redirect()->route('rmft.index')
                ->with('success', 'Semua data RMFT berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('rmft.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Get RMFT by KC code
     */
    public function getByKC(Request $request)
    {
        $kodeKc = $request->get('kode_kc');
        
        if (!$kodeKc) {
            return response()->json([]);
        }
        
        // Convert kode_kc to nama_kc
        $ukerData = Uker::where('kode_kanca', $kodeKc)->first();
        if (!$ukerData) {
            return response()->json([]);
        }
        
        $rmfts = RMFT::where('kanca', $ukerData->kanca)
            ->orderBy('completename')
            ->get(['id', 'completename', 'pernr']);
        
        return response()->json($rmfts);
    }
}
