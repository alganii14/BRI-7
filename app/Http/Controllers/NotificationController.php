<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getUnreadCount()
    {
        $user = auth()->user();
        
        // Hanya untuk manager dan rmft
        if (!in_array($user->role, ['manager', 'rmft'])) {
            return response()->json(['count' => 0]);
        }
        
        $count = $user->needsPasswordChange() ? 1 : 0;
        
        return response()->json(['count' => $count]);
    }
    
    public function getNotifications()
    {
        $user = auth()->user();
        $notifications = [];
        
        // Hanya untuk manager dan rmft
        if (in_array($user->role, ['manager', 'rmft']) && $user->needsPasswordChange()) {
            $notifications[] = [
                'id' => 'password-change',
                'type' => 'warning',
                'title' => 'Ubah Password Default',
                'message' => 'Untuk keamanan akun Anda, silakan ubah password default Anda.',
                'link' => route('profile.index'),
                'link_text' => 'Ubah Password'
            ];
        }
        
        return response()->json(['notifications' => $notifications]);
    }
}
