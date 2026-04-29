<?php

namespace App\Providers;

use App\Models\Brand;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('components.brand-menu', function ($view) {
            $brands = Brand::orderBy('name')->get();
            $view->with('brands', $brands);
        });
    }
}
