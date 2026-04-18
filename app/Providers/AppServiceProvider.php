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
    });
  }
}
