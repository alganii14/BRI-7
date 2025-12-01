<?php

namespace App\Http\Controllers;

use App\Models\NasabahDowngrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NasabahDowngradeController extends Controller
{
    public function index(Request $request)
    {
        $query = NasabahDowngrade::query();
        
        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_nasabah', 'LIKE', "%{$search}%")
                  ->orWhere('cif', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_rekening', 'LIKE', "%{$search}%")
                  ->orWhere('cabang_induk', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by cabang
        if ($request->filled('kode_cabang_induk')) {
            $query->where('kode_cabang_induk', $request->kode_cabang_induk);
        }
        
        $nasabahDowngrades = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get list cabang untuk filter
        $listCabang = NasabahDowngrade::select('kode_cabang_induk', 'cabang_induk')
                                      ->distinct()
                                      ->orderBy('cabang_induk')
                                      ->get();
        
        return view('nasabah-downgrade.index', compact('nasabahDowngrades', 'listCabang'));
    }

    public function create()
    {
        return view('nasabah-downgrade.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'slp' => 'nullable|string',
            'pbo' => 'nullable|string',
            'cif' => 'nullable|string',
            'id_prioritas' => 'nullable|string',
            'nama_nasabah' => 'required|string',
            'nomor_rekening' => 'nullable|string',
            'aum' => 'nullable|string',
        ]);

        NasabahDowngrade::create($validated);

        return redirect()->route('nasabah-downgrade.index')
                        ->with('success', 'Data Nasabah Downgrade berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $nasabahDowngrade = NasabahDowngrade::findOrFail($id);
        return view('nasabah-downgrade.edit', compact('nasabahDowngrade'));
    }

    public function update(Request $request, $id)
    {
        $nasabahDowngrade = NasabahDowngrade::findOrFail($id);
        
        $validated = $request->validate([
            'kode_cabang_induk' => 'nullable|string',
            'cabang_induk' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'unit_kerja' => 'nullable|string',
            'slp' => 'nullable|string',
            'pbo' => 'nullable|string',
            'cif' => 'nullable|string',
            'id_prioritas' => 'nullable|string',
            'nama_nasabah' => 'required|string',
            'nomor_rekening' => 'nullable|string',
            'aum' => 'nullable|string',
        ]);

        $nasabahDowngrade->update($validated);

        return redirect()->route('nasabah-downgrade.index')
                        ->with('success', 'Data Nasabah Downgrade berhasil diupdate!');
    }

    public function destroy($id)
    {
        $nasabahDowngrade = NasabahDowngrade::findOrFail($id);
        $nasabahDowngrade->delete();

        return redirect()->route('nasabah-downgrade.index')
                        ->with('success', 'Data Nasabah Downgrade berhasil dihapus!');
    }

    public function importForm()
    {
        return view('nasabah-downgrade.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'tanggal_posisi_data' => 'required|date'
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $tanggalPosisiData = $request->input('tanggal_posisi_data');
            $tanggalUploadData = $request->input('tanggal_upload_data');
            
            // Read CSV file
            $csv = array_map(function($line) {
                return str_getcsv($line, ';');
            }, file($path));
            
            // Remove header
            $header = array_shift($csv);
            
            DB::beginTransaction();
            
            $imported = 0;
            foreach ($csv as $row) {
                if (count($row) < 11) continue;
                
                NasabahDowngrade::create([
                    'kode_cabang_induk' => trim($row[0] ?? ''),
                    'cabang_induk' => trim($row[1] ?? ''),
                    'kode_uker' => trim($row[2] ?? ''),
                    'unit_kerja' => trim($row[3] ?? ''),
                    'slp' => trim($row[4] ?? ''),
                    'pbo' => trim($row[5] ?? ''),
                    'cif' => trim($row[6] ?? ''),
                    'id_prioritas' => trim($row[7] ?? ''),
                    'nama_nasabah' => trim($row[8] ?? ''),
                    'nomor_rekening' => trim($row[9] ?? ''),
                    'aum' => trim($row[10] ?? ''),
                    'tanggal_posisi_data' => $tanggalPosisiData,
                    'tanggal_upload_data' => $tanggalUploadData,
                ]);
                
                $imported++;
            }
            
            DB::commit();
            
            return redirect()->route('nasabah-downgrade.index')
                           ->with('success', "Berhasil import {$imported} data Nasabah Downgrade dengan tanggal posisi data: {$tanggalPosisiData}!");
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('nasabah-downgrade.import.form')
                           ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = NasabahDowngrade::count();
            NasabahDowngrade::truncate();
            
            return redirect()->route('nasabah-downgrade.index')
                           ->with('success', "Berhasil menghapus semua data ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('nasabah-downgrade.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
