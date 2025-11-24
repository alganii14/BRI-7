<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show profile page
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && file_exists(public_path('storage/photos/' . $user->photo))) {
                unlink(public_path('storage/photos/' . $user->photo));
            }
            
            $photo = $request->file('photo');
            $photoName = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            
            // Create directory if not exists
            if (!file_exists(public_path('storage/photos'))) {
                mkdir(public_path('storage/photos'), 0777, true);
            }
            
            $photo->move(public_path('storage/photos'), $photoName);
            $validated['photo'] = $photoName;
        }

        $user->update($validated);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diupdate!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
            'password_changed_at' => now()
        ]);

        return redirect()->route('profile.index')->with('success', 'Password berhasil diubah!');
    }
}
