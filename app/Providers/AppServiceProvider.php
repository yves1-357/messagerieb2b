<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

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
        Vite::prefetch(concurrency: 3);

        // S'assurer que le helper Vite est disponible
        if (!function_exists('vite')) {
            Blade::directive('vite', function ($expression) {
                return "<?php echo app(Illuminate\Foundation\Vite::class)($expression); ?>";
            });
        }

        // Forcer HTTPS en production sur Railway
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
