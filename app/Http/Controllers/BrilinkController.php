<?php

namespace App\Http\Controllers;

use App\Models\Brilink;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrilinkController extends Controller
{
    public function index(Request $request)
    {
        $query = Brilink::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_agen', 'LIKE', "%{$search}%")
                  ->orWhere('id_agen', 'LIKE', "%{$search}%")
                  ->orWhere('norek', 'LIKE', "%{$search}%")
                  ->orWhere('cabang', 'LIKE', "%{$search}%");
            });
        }
        
        if ($request->filled('kd_cabang')) {
            $query->where('kd_cabang', $request->kd_cabang);
        }
        
        $brilinks = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $listCabang = Brilink::select('kd_cabang', 'cabang')
                            ->distinct()
                            ->orderBy('cabang')
                            ->get();
        
        return view('brilink.index', compact('brilinks', 'listCabang'));
    }

    public function create()
    {
        return view('brilink.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kd_cabang' => 'nullable|string',
            'cabang' => 'nullable|string',
            'kd_uker' => 'nullable|string',
            'uker' => 'nullable|string',
            'nama_agen' => 'required|string',
            'id_agen' => 'nullable|string',
            'kelas' => 'nullable|string',
            'no_telpon' => 'nullable|string',
            'bidang_usaha' => 'nullable|string',
            'norek' => 'nullable|string',
            'casa' => 'nullable|string',
        ]);

        $brilink = Brilink::create($validated);

        if ($request->has('create_aktivitas') && $request->create_aktivitas == '1') {
            Aktivitas::create([
                'tipe' => 'Brilink Saldo < 10 Juta',
                'nama_nasabah' => $brilink->nama_agen,
                'cif' => $brilink->id_agen,
                'nomor_rekening' => $brilink->norek,
                'kode_cabang_induk' => $brilink->kd_cabang,
                'cabang_induk' => $brilink->cabang,
                'kode_uker' => $brilink->kd_uker,
                'unit_kerja' => $brilink->uker,
                'status' => 'Belum Dikerjakan',
            ]);
            
            return redirect()->route('aktivitas.index')
                           ->with('success', 'Data Brilink dan Aktivitas berhasil ditambahkan!');
        }

        return redirect()->route('brilink.index')
                        ->with('success', 'Data Brilink berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $brilink = Brilink::findOrFail($id);
        return view('brilink.edit', compact('brilink'));
    }

    public function update(Request $request, $id)
    {
        $brilink = Brilink::findOrFail($id);
        
        $validated = $request->validate([
            'kd_cabang' => 'nullable|string',
            'cabang' => 'nullable|string',
            'kd_uker' => 'nullable|string',
            'uker' => 'nullable|string',
            'nama_agen' => 'required|string',
            'id_agen' => 'nullable|string',
            'kelas' => 'nullable|string',
            'no_telpon' => 'nullable|string',
            'bidang_usaha' => 'nullable|string',
            'norek' => 'nullable|string',
            'casa' => 'nullable|string',
        ]);

        $brilink->update($validated);

        return redirect()->route('brilink.index')
                        ->with('success', 'Data Brilink berhasil diupdate!');
    }

    public function destroy($id)
    {
        $brilink = Brilink::findOrFail($id);
        $brilink->delete();

        return redirect()->route('brilink.index')
                        ->with('success', 'Data Brilink berhasil dihapus!');
    }

    public function importForm()
    {
        return view('brilink.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'tanggal_posisi_data' => 'required|date',
            'tanggal_upload_data' => 'required|date',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $tanggalPosisiData = $request->tanggal_posisi_data;
            $tanggalUploadData = $request->tanggal_upload_data;
            
            $csv = array_map(function($line) {
                return str_getcsv($line, ';');
            }, file($path));
            
            $header = array_shift($csv);
            
            DB::beginTransaction();
            
            $imported = 0;
            foreach ($csv as $row) {
                if (count($row) < 11) continue;
                
                Brilink::create([
                    'kd_cabang' => trim($row[0] ?? ''),
                    'cabang' => trim($row[1] ?? ''),
                    'kd_uker' => trim($row[2] ?? ''),
                    'uker' => trim($row[3] ?? ''),
                    'nama_agen' => trim($row[4] ?? ''),
                    'id_agen' => trim($row[5] ?? ''),
                    'kelas' => trim($row[6] ?? ''),
                    'no_telpon' => trim($row[7] ?? ''),
                    'bidang_usaha' => trim($row[8] ?? ''),
                    'norek' => trim($row[9] ?? ''),
                    'casa' => trim($row[10] ?? ''),
                    'tanggal_posisi_data' => $tanggalPosisiData,
                    'tanggal_upload_data' => $tanggalUploadData,
                ]);
                
                $imported++;
            }
            
            DB::commit();
            
            return redirect()->route('brilink.index')
                           ->with('success', "Berhasil import {$imported} data Brilink dengan tanggal posisi data: {$tanggalPosisiData}!");
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('brilink.import.form')
                           ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = Brilink::count();
            Brilink::truncate();
            
            return redirect()->route('brilink.index')
                           ->with('success', "Berhasil menghapus semua data ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('brilink.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
