<?php

namespace App\Providers;
use App\Models\Categoria;
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

    /**
     * Bootstrap any application services.
     */

public function boot()
{
    View::composer('*', function ($view) {
        $view->with('categorias', Categoria::all());
    });
    // Configurar Carbon para español
    \Carbon\Carbon::setLocale('es');
    
    // Opcional: Configurar Laravel también
    app()->setLocale('es');
}
}
