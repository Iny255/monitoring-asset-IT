<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string  ...$roles
   * @return mixed
   */
  public function handle(Request $request, Closure $next, ...$roles)
  {
    if (!Auth::check()) {
      abort(403, 'User belum login');
    }

    $userRole = strtolower(trim(Auth::user()->role));

    // normalisasi semua role dari route
    $roles = array_map(function ($role) {
      return strtolower(trim($role));
    }, $roles);

    // 🔥 bypass super admin
    if ($userRole === 'super_admin') {
      return $next($request);
    }

    if (!in_array($userRole, $roles)) {
      abort(403, 'Role tidak diizinkan: ' . $userRole);
    }

    return $next($request);
  }
}
