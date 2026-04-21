<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    View::composer('*', function ($view) {

        $json = file_get_contents(resource_path('menu/verticalMenu.json'));
        $menuData = json_decode($json);

        $view->with('menuData', $menuData);
        
        // Company branding
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'super_admin') {
                $perusahaan = \App\Models\Perusahaan::where('nama_perusahaan', 'PT Sembilan Matahari Sakti')->first();
            } else {
                $perusahaan = $user->perusahaan;
            }
           
        }
    });
  }
}
