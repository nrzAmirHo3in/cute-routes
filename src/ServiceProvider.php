<?php
namespace CuteRoutes;

use Route;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{

    /**
     * Register.
     *
     * @return
     */
    public function register()
    {
        //
    }

    /**
     * Boot.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config.php',
            'cute-routes'
        );

        if (config('cute-routes.debug_only', true) && empty(config('app.debug'))) {
            return;
        }

        $this->loadViewsFrom(__DIR__ . '/../views', 'cute-routes');

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('cute-routes.php')
        ]);

        Route::get(config('cute-routes.url'), [CuteRoutesController::class, 'show'])
            ->name('cute-routes.show')
            ->middleware(config('cute-routes.middlewares'));
    }

}
