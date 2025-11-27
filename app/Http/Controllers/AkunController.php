<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RMFT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Admin melihat semua akun
        // Manager hanya lihat akun di KC mereka
        if ($user->isAdmin()) {
            $managers = User::where('role', 'manager')
                            ->orderBy('name', 'asc')
                            ->paginate(20, ['*'], 'managers_page');
            
            $rmfts = User::where('role', 'rmft')
                         ->with('rmftData')
                         ->orderBy('name', 'asc')
                         ->paginate(20, ['*'], 'rmfts_page');
        } elseif ($user->isManager() && $user->kode_kanca) {
            $managers = User::where('role', 'manager')
                            ->where('kode_kanca', $user->kode_kanca)
                            ->orderBy('name', 'asc')
                            ->paginate(20, ['*'], 'managers_page');
            
            $rmfts = User::where('role', 'rmft')
                         ->where('kode_kanca', $user->kode_kanca)
                         ->with('rmftData')
                         ->orderBy('name', 'asc')
                         ->paginate(20, ['*'], 'rmfts_page');
        } else {
            // Fallback jika bukan admin atau manager
            $managers = collect();
            $rmfts = collect();
        }
        
        return view('akun.index', compact('managers', 'rmfts'));
    }
    
    public function create()
    {
        // Hanya admin yang bisa akses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $rmftList = RMFT::with('ukerRelation')->orderBy('completename', 'asc')->get();
        
        return view('akun.create', compact('rmftList'));
    }
    
    public function store(Request $request)
    {
        // Hanya admin yang bisa akses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,manager,rmft',
            'pernr' => 'nullable|string|max:50',
            'rmft_id' => 'nullable|exists:rmfts,id',
            'kode_kanca' => 'required_if:role,manager|nullable|string|max:10',
            'nama_kanca' => 'required_if:role,manager|nullable|string|max:255',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        // Set password_changed_at to null untuk akun baru (password default)
        $validated['password_changed_at'] = null;
        
        User::create($validated);
        
        return redirect()->route('akun.index')->with('success', 'Akun berhasil ditambahkan!');
    }
    
    public function edit($id)
    {
        // Hanya admin yang bisa akses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $akun = User::findOrFail($id);
        $rmftList = RMFT::orderBy('completename', 'asc')->get();
        
        return view('akun.edit', compact('akun', 'rmftList'));
    }
    
    public function update(Request $request, $id)
    {
        // Hanya admin yang bisa akses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $akun = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($akun->id),
            ],
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,manager,rmft',
            'pernr' => 'nullable|string|max:50',
            'rmft_id' => 'nullable|exists:rmfts,id',
            'kode_kanca' => 'nullable|string|max:10',
            'nama_kanca' => 'nullable|string|max:255',
        ]);
        
        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
            // Reset password_changed_at jika admin mengubah password
            $validated['password_changed_at'] = null;
        } else {
            unset($validated['password']);
        }
        
        $akun->update($validated);
        
        return redirect()->route('akun.index')->with('success', 'Akun berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        // Hanya admin yang bisa akses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $akun = User::findOrFail($id);
        
        // Tidak boleh menghapus akun sendiri
        if ($akun->id === auth()->id()) {
            return redirect()->route('akun.index')->with('error', 'Tidak dapat menghapus akun sendiri!');
        }
        
        $akun->delete();
        
        return redirect()->route('akun.index')->with('success', 'Akun berhasil dihapus!');
    }
}
