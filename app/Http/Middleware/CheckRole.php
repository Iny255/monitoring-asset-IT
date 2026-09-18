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

    $user = Auth::user();
    $rawRole = strtolower(trim((string) ($user->role ?? '')));

    // Normalisasi jika role berupa angka ID dari tabel roles
    if (is_numeric($rawRole)) {
      $roleModel = \App\Models\Role::find((int) $rawRole);
      $userRole = $roleModel ? strtolower(trim($roleModel->name)) : $rawRole;
    } else {
      $userRole = $rawRole;
    }

    // normalisasi semua role dari route
    $roles = array_map(function ($role) {
      return strtolower(trim((string) $role));
    }, $roles);

    // 🔥 bypass super admin (baik slug 'super_admin' maupun ID 1)
    if ($userRole === 'super_admin' || $rawRole === '1' || $userRole === '1') {
      return $next($request);
    }

    if (in_array($userRole, $roles) || in_array($rawRole, $roles)) {
      return $next($request);
    }

    // 🌟 Cek izin dinamis dari modul database jika role kustom/berbeda
    $path = trim($request->path(), '/');
    $roleDef = $user->roleDefinition ?: (is_numeric($rawRole) ? \App\Models\Role::find((int) $rawRole) : \App\Models\Role::where('name', $userRole)->first());
    if ($roleDef) {
      // Jika role punya hak can_manage_settings dan route menuju dashboard/settings
      if ($roleDef->can_manage_settings && str_starts_with($path, 'dashboard/settings')) {
        return $next($request);
      }

      $hasDbPermission = $roleDef->modules()
        ->where('is_active', true)
        ->get()
        ->contains(function ($mod) use ($path) {
          if (!$mod->url) return false;
          $modUrl = trim($mod->url, '/');
          return $path === $modUrl || str_starts_with($path, $modUrl . '/') || str_starts_with($path, $modUrl . '?');
        });

      if ($hasDbPermission) {
        return $next($request);
      }
    }

    abort(403, 'Role tidak diizinkan: ' . ($userRole ?: $rawRole));
  }
}
