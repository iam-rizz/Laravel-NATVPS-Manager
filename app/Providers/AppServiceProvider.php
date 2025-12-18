<?php

namespace App\Providers;

use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use App\Services\Virtualizor\VirtualizorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register VirtualizorService as a singleton
        $this->app->singleton(VirtualizorServiceInterface::class, VirtualizorService::class);
        
        // Also bind the concrete class for direct injection
        $this->app->singleton(VirtualizorService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Include the Virtualizor enduser.php library
        require_once app_path('Libraries/Virtualizor/enduser.php');
    }
}
