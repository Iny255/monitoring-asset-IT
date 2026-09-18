<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSettingAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cek apakah user memiliki izin untuk mengelola pengaturan sistem
        if (method_exists($user, 'canManageSettings') && $user->canManageSettings()) {
            return $next($request);
        }

        // Fallback jika role super_admin (slug atau id 1)
        if ($user->role === 'super_admin' || $user->role === '1' || $user->role === 1) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses untuk mengelola pengaturan sistem.');
    }
}
