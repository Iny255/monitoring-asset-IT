<?php

namespace App\Providers;

use App\Models\Perusahaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    //
  }

  public function boot(): void
  {
    Paginator::useBootstrapFive();

    View::composer('*', function ($view) {
      // =====================================
      // MENU
      // =====================================
      $json = file_get_contents(resource_path('menu/verticalMenu.json'));
      $menuData = json_decode($json);

      if (Auth::check() && isset($menuData->menu)) {
        $user = Auth::user();
        $rawRole = (string) ($user->role ?? '');
        $userRole = is_numeric($rawRole) ? ($user->roleDefinition?->name ?? $rawRole) : $rawRole;
        $normalizedRole = strtolower(str_replace([' ', '-'], '_', trim($userRole)));
        $isSuperAdmin = in_array($normalizedRole, ['super_admin', '1', 'superadmin'])
          || in_array($rawRole, ['super_admin', '1', 1, 'superadmin'])
          || ($user->roleDefinition && $user->roleDefinition->name === 'super_admin');
        $canManageSettings = method_exists($user, 'canManageSettings') ? $user->canManageSettings() : $isSuperAdmin;

        try {
          $activeModules = \App\Models\Module::all();
          $allowedModuleUrls = [];
          $inactiveUrls = [];

          if ($isSuperAdmin) {
            foreach ($activeModules as $m) {
              if ($m->url) {
                $cleanUrl = ltrim($m->url, '/');
                $allowedModuleUrls[] = $cleanUrl;
              }
            }
          } else {
            $roleRecord = \App\Models\Role::where('name', $userRole)->first();
            $roleModules = $roleRecord ? $roleRecord->modules()->where('is_active', true)->get() : collect();

            foreach ($activeModules as $m) {
              if ($m->url && !$m->is_active) {
                $inactiveUrls[] = ltrim($m->url, '/');
              }
            }

            foreach ($roleModules as $m) {
              if ($m->url) {
                $allowedModuleUrls[] = ltrim($m->url, '/');
              }
            }
          }

          $filterMenu = function ($items) use (&$filterMenu, $userRole, $rawRole, $isSuperAdmin, $canManageSettings, $allowedModuleUrls, $inactiveUrls) {
            $filtered = [];
            foreach ($items as $item) {
              $itemUrl = isset($item->url) ? ltrim($item->url, '/') : null;

              // Dashboard khusus role masing-masing
              if ($itemUrl === 'dashboard/petugas' && $userRole !== 'petugas') {
                continue;
              }
              if ($itemUrl === 'dashboard/aset-saya' && !in_array($userRole, ['user', 'karyawan'])) {
                continue;
              }
              if ($itemUrl === 'dashboard/superadmin' && !$isSuperAdmin) {
                continue;
              }

              // JIKA SUPER ADMIN: TAMPILKAN SEMUA MENU & SUBMENU TANPA KECUALI
              if ($isSuperAdmin) {
                $item->roles = array_unique(array_merge($item->roles ?? [], [$userRole, $rawRole, 'super_admin']));
                if (isset($item->submenu) && count($item->submenu) > 0) {
                  $item->submenu = $filterMenu($item->submenu);
                }
                $filtered[] = $item;
                continue;
              }

              // Jika ini menu pengaturan sistem
              if (isset($item->slug) && $item->slug === 'settings') {
                if ($canManageSettings) {
                  $item->roles = array_unique(array_merge($item->roles ?? [], [$userRole, $rawRole]));
                  if (isset($item->submenu)) {
                    foreach ($item->submenu as $sub) {
                      $sub->roles = array_unique(array_merge($sub->roles ?? [], [$userRole, $rawRole]));
                    }
                  }
                  $filtered[] = $item;
                }
                continue;
              }

              // Cek jika modul dinonaktifkan
              if ($itemUrl && in_array($itemUrl, $inactiveUrls)) {
                continue;
              }

              // Jika punya submenu, filter submenunya
              if (isset($item->submenu) && count($item->submenu) > 0) {
                $item->submenu = $filterMenu($item->submenu);
                if (count($item->submenu) > 0) {
                  $item->roles = array_unique(array_merge($item->roles ?? [], [$userRole, $rawRole]));
                  $filtered[] = $item;
                }
              } else {
                // Item tunggal
                $hasAccess = false;
                if ($itemUrl && in_array($itemUrl, $allowedModuleUrls)) {
                  $hasAccess = true;
                } elseif (isset($item->roles) && (in_array($userRole, $item->roles) || in_array($rawRole, $item->roles))) {
                  $hasAccess = true;
                }

                if ($hasAccess) {
                  $item->roles = array_unique(array_merge($item->roles ?? [], [$userRole, $rawRole]));
                  $filtered[] = $item;
                }
              }
            }
            return $filtered;
          };

          $menuData->menu = $filterMenu($menuData->menu);
        } catch (\Throwable $e) {
          // Fallback tanpa crash jika database belum siap
        }
      }

      $view->with('menuData', $menuData);

      // =====================================
      // DEFAULT THEME
      // SUPER ADMIN = PT SEMBILAN
      // =====================================

      $theme = [
        'company_name' => 'PT Sembilan Matahari Sakti',

        'primary_color' => '#0b2f57',

        'secondary_color' => '#154b87',

        'logo' => asset('assets/img/logo_sembilan.png'),
      ];

      // =====================================
      // USER LOGIN
      // =====================================

      if (Auth::check()) {
        $user = Auth::user();

        // =====================================
        // JIKA USER MEMILIKI PERUSAHAAN
        // =====================================

        $perusahaan = $user->perusahaan;

        if ($perusahaan && ($user->role !== 'super_admin' || $user->id_perusahaan)) {
          $primaryColor = $perusahaan->primary_color ?? ($perusahaan->parent?->primary_color ?? '#0b2f57');
          $secondaryColor = $perusahaan->secondary_color ?? ($perusahaan->parent?->secondary_color ?? '#154b87');

          $theme = [
            'company_name' => $perusahaan->nama_perusahaan,

            'primary_color' => $primaryColor,

            'secondary_color' => $secondaryColor,

            'logo' => $perusahaan->logo_url,
          ];
        }
      }

      // =====================================
      // SHARE GLOBAL
      // =====================================

      $view->with('theme', $theme);
    });
  }
}
