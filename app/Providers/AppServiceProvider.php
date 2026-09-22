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
                $cleanUrl = trim($m->url, '/');
                $allowedModuleUrls[] = $cleanUrl;
              }
            }
          } else {
            $roleRecord = $user->roleDefinition
              ?: (\App\Models\Role::where('name', $userRole)->first()
                ?: (\App\Models\Role::whereRaw('LOWER(name) = ?', [strtolower($userRole)])->first()
                  ?: (is_numeric($rawRole) ? \App\Models\Role::find((int) $rawRole) : null)));
            $roleModules = $roleRecord ? $roleRecord->modules()->where('is_active', true)->get() : collect();

            foreach ($activeModules as $m) {
              if ($m->url && !$m->is_active) {
                $inactiveUrls[] = trim($m->url, '/');
              }
            }

            foreach ($roleModules as $m) {
              if ($m->url) {
                $allowedModuleUrls[] = trim($m->url, '/');
              }
            }
          }

          $filterMenu = function ($items) use (&$filterMenu, $userRole, $rawRole, $isSuperAdmin, $canManageSettings, $allowedModuleUrls, $inactiveUrls) {
            $filtered = [];
            foreach ($items as $item) {
              $itemUrl = isset($item->url) ? trim($item->url, '/') : null;

              // Dashboard khusus role masing-masing
              if ($isSuperAdmin) {
                if ($itemUrl === 'dashboard/petugas' || $itemUrl === 'dashboard/aset-saya') {
                  continue;
                }
              } else {
                if ($itemUrl === 'dashboard/superadmin' && !in_array('dashboard/superadmin', $allowedModuleUrls)) {
                  continue;
                }
                if ($itemUrl === 'dashboard/petugas' && !in_array('dashboard/petugas', $allowedModuleUrls) && $userRole !== 'petugas') {
                  continue;
                }
                if ($itemUrl === 'dashboard/aset-saya' && !in_array('dashboard/aset-saya', $allowedModuleUrls) && !in_array($userRole, ['user', 'karyawan'])) {
                  continue;
                }
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

      $hexToRgb = function ($hex) {
        $hex = ltrim($hex ?? '#0b2f57', '#');
        if (strlen($hex) == 3) {
          $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) >= 6) {
          return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
        }
        return '11, 47, 87';
      };

      $theme = [
        'company_id' => null,
        'company_name' => 'Monitoring Aset Divisi IT',
        'primary_color' => '#0b2f57',
        'secondary_color' => '#154b87',
        'primary_rgb' => '11, 47, 87',
        'logo' => asset('assets/img/logo_aset.png'),
        'is_custom' => false,
      ];

      // =====================================
      // DETEKSI PERUSAHAAN AKTIF
      // 1. Dari filter request di index (misal Super Admin memfilter perusahaan)
      // 2. Dari parameter rute detail (misal checklist ruangan / jadwal)
      // 3. Dari user login
      // =====================================
      $activePerusahaan = null;

      $filterCompanyId = request('perusahaan_id') ?? request('perusahaan') ?? request('id_perusahaan');
      if ($filterCompanyId && !in_array($filterCompanyId, ['all', 'semua', 'global', ''])) {
        $filterCompany = Perusahaan::find($filterCompanyId);
        if ($filterCompany) {
          $activePerusahaan = $filterCompany;
        }
      }

      // Deteksi entitas perusahaan dari parameter rute (Checklist Ruangan & Jadwal)
      if (!$activePerusahaan) {
        $route = request()->route();
        if ($route) {
          $routeName = $route->getName();
          if (str_starts_with($routeName ?? '', 'checklist.pemeriksaan.')) {
            $ruanganId = $route->parameter('id') ?? $route->parameter('ruanganId') ?? $route->parameter('ruangan');
            if ($ruanganId) {
              $ruangan = is_object($ruanganId) ? $ruanganId : \App\Models\ChecklistRuangan::with('perusahaan', 'lokasi.perusahaan')->find($ruanganId);
              if ($ruangan) {
                $activePerusahaan = $ruangan->perusahaan ?? $ruangan->lokasi?->perusahaan;
              }
            }
          } elseif (str_starts_with($routeName ?? '', 'checklist.jadwal.')) {
            $jadwalId = $route->parameter('jadwal') ?? $route->parameter('id');
            if ($jadwalId) {
              $jadwal = is_object($jadwalId) ? $jadwalId : \App\Models\ChecklistJadwalRutin::with('perusahaan', 'lokasi.perusahaan')->find($jadwalId);
              if ($jadwal) {
                $activePerusahaan = $jadwal->perusahaan ?? $jadwal->lokasi?->perusahaan;
              }
            }
          }
        }
      }

      if (!$activePerusahaan && Auth::check()) {
        $user = Auth::user();
        if ($user->perusahaan && ($user->role !== 'super_admin' || $user->id_perusahaan)) {
          $activePerusahaan = $user->perusahaan;
        }
      }

      if ($activePerusahaan) {
        $primaryColor = $activePerusahaan->primary_color ?? ($activePerusahaan->parent?->primary_color ?? '#0b2f57');
        $secondaryColor = $activePerusahaan->secondary_color ?? ($activePerusahaan->parent?->secondary_color ?? '#154b87');

        $theme = [
          'company_id' => $activePerusahaan->id,
          'company_name' => $activePerusahaan->nama_perusahaan,
          'primary_color' => $primaryColor,
          'secondary_color' => $secondaryColor,
          'primary_rgb' => $hexToRgb($primaryColor),
          'logo' => asset('assets/img/logo_aset.png'),
          'is_custom' => true,
        ];
      }

      // =====================================
      // SHARE GLOBAL
      // =====================================

      $view->with('theme', $theme);
    });
  }
}
