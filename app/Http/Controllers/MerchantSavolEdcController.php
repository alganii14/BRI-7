<?php

namespace App\Http\Controllers;

use App\Models\MerchantSavolEdc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantSavolEdcController extends Controller
{
    public function index(Request $request)
    {
        $query = MerchantSavolEdc::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_merchant', 'LIKE', "%{$search}%")
                  ->orWhere('cifno', 'LIKE', "%{$search}%")
                  ->orWhere('norek', 'LIKE', "%{$search}%")
                  ->orWhere('nama_uker', 'LIKE', "%{$search}%");
            });
        }
        
        $merchants = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('merchant-savol-edc.index', compact('merchants'));
    }

    public function create()
    {
        return view('merchant-savol-edc.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kanca' => 'nullable|string',
            'nama_kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
            'nama_merchant' => 'nullable|string',
            'norek' => 'nullable|string',
            'cifno' => 'nullable|string',
            'jumlah_tid' => 'nullable|string',
            'jumlah_trx' => 'nullable|string',
            'sales_volume' => 'nullable|string',
            'saldo_posisi' => 'nullable|string',
        ]);

        MerchantSavolEdc::create($validated);

        return redirect()->route('merchant-savol-edc.index')
                        ->with('success', 'Data Merchant EDC berhasil ditambahkan!');
    }

    public function show($id)
    {
        $merchant = MerchantSavolEdc::findOrFail($id);
        return view('merchant-savol-edc.show', compact('merchant'));
    }

    public function edit($id)
    {
        $merchant = MerchantSavolEdc::findOrFail($id);
        return view('merchant-savol-edc.edit', compact('merchant'));
    }

    public function update(Request $request, $id)
    {
        $merchant = MerchantSavolEdc::findOrFail($id);
        
        $validated = $request->validate([
            'kode_kanca' => 'nullable|string',
            'nama_kanca' => 'nullable|string',
            'kode_uker' => 'nullable|string',
            'nama_uker' => 'nullable|string',
            'nama_merchant' => 'nullable|string',
            'norek' => 'nullable|string',
            'cifno' => 'nullable|string',
            'jumlah_tid' => 'nullable|string',
            'jumlah_trx' => 'nullable|string',
            'sales_volume' => 'nullable|string',
            'saldo_posisi' => 'nullable|string',
        ]);

        $merchant->update($validated);

        return redirect()->route('merchant-savol-edc.index')
                        ->with('success', 'Data Merchant EDC berhasil diupdate!');
    }

    public function destroy($id)
    {
        $merchant = MerchantSavolEdc::findOrFail($id);
        $merchant->delete();

        return redirect()->route('merchant-savol-edc.index')
                        ->with('success', 'Data Merchant EDC berhasil dihapus!');
    }

    public function importForm()
    {
        return view('merchant-savol-edc.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            $csv = array_map(fn($line) => str_getcsv($line, ';'), file($path));
            
            $header = array_shift($csv);
            
            DB::beginTransaction();
            
            $imported = 0;
            foreach ($csv as $row) {
                if (count($row) < 11) continue;
                
                MerchantSavolEdc::create([
                    'kode_kanca' => trim($row[0] ?? ''),
                    'nama_kanca' => trim($row[1] ?? ''),
                    'kode_uker' => trim($row[2] ?? ''),
                    'nama_uker' => trim($row[3] ?? ''),
                    'nama_merchant' => trim($row[4] ?? ''),
                    'norek' => trim($row[5] ?? ''),
                    'cifno' => trim($row[6] ?? ''),
                    'jumlah_tid' => trim($row[7] ?? ''),
                    'jumlah_trx' => trim($row[8] ?? ''),
                    'sales_volume' => trim($row[9] ?? ''),
                    'saldo_posisi' => trim($row[10] ?? ''),
                ]);
                
                $imported++;
            }
            
            DB::commit();
            
            return redirect()->route('merchant-savol-edc.index')
                           ->with('success', "Berhasil import {$imported} data Merchant EDC!");
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('merchant-savol-edc.import.form')
                           ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function deleteAll()
    {
        try {
            $count = MerchantSavolEdc::count();
            MerchantSavolEdc::truncate();
            
            return redirect()->route('merchant-savol-edc.index')
                           ->with('success', "Berhasil menghapus semua data ({$count} record)!");
        } catch (\Exception $e) {
            return redirect()->route('merchant-savol-edc.index')
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
