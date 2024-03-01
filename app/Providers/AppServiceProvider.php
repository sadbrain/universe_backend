<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            // \App\Repository\IRepository\ICategoryRepository::class,
            // \App\Repository\CategoryRepository::class,
            // \App\Repository\IRepository\IProductRepository::class,
            // \App\Repository\ProductRepository::class,
            \App\Repository\IRepository\IUnitOfWork::class,
            \App\Repository\UnitOfWork::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
