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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
