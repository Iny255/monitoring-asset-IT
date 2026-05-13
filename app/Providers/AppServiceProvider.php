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
        // JIKA BUKAN SUPER ADMIN
        // =====================================

        if ($user->role !== 'super_admin') {
          $perusahaan = $user->perusahaan;

          if ($perusahaan) {
            $theme = [
              'company_name' => $perusahaan->nama_perusahaan,

              'primary_color' => $perusahaan->primary_color ?? '#0b2f57',

              'secondary_color' => $perusahaan->secondary_color ?? '#154b87',

              'logo' => $perusahaan->logo ? asset($perusahaan->logo) : asset('assets/img/logo_sembilan.png'),
            ];
          }
        }
      }

      // =====================================
      // SHARE GLOBAL
      // =====================================

      $view->with('theme', $theme);
    });
  }
}
