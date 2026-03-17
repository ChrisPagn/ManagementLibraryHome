<?php

namespace App\Providers;

use App\Importers\OpenLibraryImporter;
use App\Services\ImportService;
use App\Services\ItemService;
use App\Services\LoanService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ItemService::class);
        $this->app->singleton(LoanService::class);
        $this->app->singleton(OpenLibraryImporter::class);
        $this->app->singleton(ImportService::class);
        $this->app->singleton(\App\Services\CollectionService::class);
    }

    /**
     * Bootstrap any application services.
     * Configure les paramètres régionaux pour Carbon et les dates Laravel afin d'afficher les mois en français dans les widgets.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale('fr');
        \Illuminate\Support\Facades\Date::setLocale('fr');
    }
}
