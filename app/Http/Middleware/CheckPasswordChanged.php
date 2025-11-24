<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Jika user adalah manager atau rmft dan belum ganti password
        if ($user && $user->needsPasswordChange()) {
            // Izinkan akses ke route profil, logout, dan API notifications
            $allowedRoutes = [
                'profile.index',
                'profile.update',
                'profile.password',
                'logout',
                'api.notifications.count',
                'api.notifications'
            ];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('profile.index')
                    ->with('warning', 'Anda harus mengubah password default terlebih dahulu sebelum dapat mengakses fitur lainnya.');
            }
        }
        
        return $next($request);
    }
}
