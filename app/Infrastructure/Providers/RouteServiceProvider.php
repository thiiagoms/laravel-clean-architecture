<?php

namespace App\Infrastructure\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            Route::middleware('api')
                ->prefix('api/v1/')
                ->name('api.v1.user.')
                ->group(base_path('app/Presentation/Http/Api/V1/User/Routes/routes.php'));

            Route::middleware(['api'])
                ->prefix('api/v1/auth/')
                ->name('api.v1.auth.')
                ->group(base_path('app/Presentation/Http/Api/V1/Auth/Routes/routes.php'));

            Route::middleware(['api', 'auth:api'])
                ->prefix('api/v1/task/')
                ->name('api.v1.task.')
                ->group(base_path('app/Presentation/Http/Api/V1/Task/Routes/routes.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
