<?php

namespace App\Http\Controllers;

use App\Models\RencanaAktivitas;
use Illuminate\Http\Request;

class RencanaAktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rencanaAktivitas = RencanaAktivitas::latest()->paginate(10);
        return view('rencana-aktivitas.index', compact('rencanaAktivitas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rencana-aktivitas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_rencana' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        RencanaAktivitas::create($validated);

        return redirect()->route('rencana-aktivitas.index')
            ->with('success', 'Rencana Aktivitas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rencanaAktivitas = RencanaAktivitas::findOrFail($id);
        return view('rencana-aktivitas.show', compact('rencanaAktivitas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rencanaAktivitas = RencanaAktivitas::findOrFail($id);
        return view('rencana-aktivitas.edit', compact('rencanaAktivitas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rencanaAktivitas = RencanaAktivitas::findOrFail($id);
        
        $validated = $request->validate([
            'nama_rencana' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $rencanaAktivitas->update($validated);

        return redirect()->route('rencana-aktivitas.index')
            ->with('success', 'Rencana Aktivitas berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Manual find by ID
            $rencanaAktivitas = RencanaAktivitas::findOrFail($id);

            // Log untuk debugging
            \Log::info('Attempting to delete Rencana Aktivitas', [
                'id' => $rencanaAktivitas->id,
                'nama_rencana' => $rencanaAktivitas->nama_rencana,
                'related_aktivitas_count' => $rencanaAktivitas->aktivitas()->count()
            ]);

            // Cek apakah ada aktivitas yang terkait
            if ($rencanaAktivitas->aktivitas()->count() > 0) {
                \Log::warning('Cannot delete Rencana Aktivitas - has related aktivitas', [
                    'id' => $rencanaAktivitas->id
                ]);
                return redirect()->route('rencana-aktivitas.index')
                    ->with('error', 'Rencana Aktivitas tidak dapat dihapus karena masih digunakan di ' . $rencanaAktivitas->aktivitas()->count() . ' aktivitas!');
            }

            $nama = $rencanaAktivitas->nama_rencana;
            $rencanaAktivitas->delete();

            \Log::info('Successfully deleted Rencana Aktivitas', [
                'id' => $id,
                'nama_rencana' => $nama
            ]);

            return redirect()->route('rencana-aktivitas.index')
                ->with('success', 'Rencana Aktivitas "' . $nama . '" berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Error deleting Rencana Aktivitas', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('rencana-aktivitas.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get rencana aktivitas by RMFT ID (for Manager/Admin)
     *
     * @param  int  $rmftId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByRMFT($rmftId)
    {
        // Get all active rencana aktivitas for the selected RMFT
        // Since rencana aktivitas is not tied to specific RMFT, return all active ones
        $rencanaAktivitas = RencanaAktivitas::where('is_active', true)
            ->orderBy('nama_rencana')
            ->get(['id', 'nama_rencana']);

        return response()->json($rencanaAktivitas);
    }
}
