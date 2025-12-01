<?php

namespace App\Http\Controllers;

use App\Models\MerchantSavolQris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantSavolQrisController extends Controller
{
    public function index(Request $request)
    {
        $query = MerchantSavolQris::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_merchant', 'LIKE', "%{$search}%")
                  ->orWhere('storeid', 'LIKE', "%{$search}%")
                  ->orWhere('cif', 'LIKE', "%{$search}%")
                  ->orWhere('no_rek', 'LIKE', "%{$search}%")
                  ->orWhere('nama_uker', 'LIKE', "%{$search}%");
            });
        }
        
        $merchants = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('merchant-savol-qris.index', compact('merchants'));
    }

    public function create()
    {
        return view('merchant-savol-qris.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kanca' => 'nullable|string',
            'nama_kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
            'storeid' => 'nullable|string',
            'nama_merchant' => 'nullable|string',
            'alamat' => 'nullable|string',
            'no_rek' => 'nullable|string',
            'cif' => 'nullable|string',
            'akumulasi_sv_total' => 'nullable|string',
            'posisi_sv_total' => 'nullable|string',
            'saldo_posisi' => 'nullable|string',
        ]);

        MerchantSavolQris::create($validated);

        return redirect()->route('merchant-savol-qris.index')
                        ->with('success', 'Data Merchant QRIS berhasil ditambahkan!');
    }

    public function show($id)
    {
        $merchant = MerchantSavolQris::findOrFail($id);
        return view('merchant-savol-qris.show', compact('merchant'));
    }

    public function edit($id)
    {
        $merchant = MerchantSavolQris::findOrFail($id);
        return view('merchant-savol-qris.edit', compact('merchant'));
    }

    public function update(Request $request, $id)
    {
        $merchant = MerchantSavolQris::findOrFail($id);
        
        $validated = $request->validate([
            'kode_kanca' => 'nullable|string',
            'nama_kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
            'storeid' => 'nullable|string',
            'nama_merchant' => 'nullable|string',
            'alamat' => 'nullable|string',
            'no_rek' => 'nullable|string',
            'cif' => 'nullable|string',
            'akumulasi_sv_total' => 'nullable|string',
            'posisi_sv_total' => 'nullable|string',
            'saldo_posisi' => 'nullable|string',
        ]);

        $merchant->update($validated);

        return redirect()->route('merchant-savol-qris.index')
                        ->with('success', 'Data Merchant QRIS berhasil diupdate!');
    }

    public function destroy($id)
    {
        $merchant = MerchantSavolQris::findOrFail($id);
        $merchant->delete();

        return redirect()->route('merchant-savol-qris.index')
                        ->with('success', 'Data Merchant QRIS berhasil dihapus!');
    }

    public function importForm()
    {
        return view('merchant-savol-qris.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            $tanggalPosisiData = $request->input('tanggal_posisi_data');
            $tanggalUploadData = $request->input('tanggal_upload_data');
            
            $csv = array_map(fn($line) => str_getcsv($line, ';'), file($path));
            
            $header = array_shift($csv);
            
            DB::beginTransaction();
            
            $imported = 0;
            foreach ($csv as $row) {
                if (count($row) < 12) continue;
                
                MerchantSavolQris::create([
                    'kode_kanca' => trim($row[0] ?? ''),
                    'nama_kanca' => trim($row[1] ?? ''),
                    'kode_uker' => trim($row[2] ?? ''),
                    'nama_uker' => trim($row[3] ?? ''),
                    'storeid' => trim($row[4] ?? ''),
                    'nama_merchant' => trim($row[5] ?? ''),
                    'alamat' => trim($row[6] ?? ''),
                    'no_rek' => trim($row[7] ?? ''),
                    'cif' => trim($row[8] ?? ''),
                    'akumulasi_sv_total' => trim($row[9] ?? ''),
                    'posisi_sv_total' => trim($row[10] ?? ''),
                    'saldo_posisi' => trim($row[11] ?? ''),
                    'tanggal_posisi_data' => $tanggalPosisiData,
                    'tanggal_upload_data' => $tanggalUploadData,
                ]);
                
                $imported++;
            }
            
            DB::commit();
            
            return redirect()->route('merchant-savol-qris.index')
                           ->with('success', "Berhasil import {$imported} data Merchant QRIS!");
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('merchant-savol-qris.import.form')
                           ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = MerchantSavolQris::count();
            MerchantSavolQris::truncate();
            
            return redirect()->route('merchant-savol-qris.index')
                           ->with('success', "Berhasil menghapus semua data ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('merchant-savol-qris.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
