<?php

namespace Naif\Chatgpt;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Naif\Chatgpt\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/chatgpt-nova4.php' => config_path('chatgpt-nova4.php')
            ], 'config');

            if (!class_exists('CreateChatGPTNova4Table')) {
//                $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

                $this->publishes([
                    __DIR__ . '/../database/migrations/create_chatgpt_nova4_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_chatgpt_nova4_table.php'),
                ], 'chatgpt-nova4-migrations');
            }

            if (!class_exists('ChatGPTNova4')) {
                $this->publishes([
                    __DIR__ . '/Models/ChatGPTNova4.php' => app_path('Models/ChatGPTNova4.php')
                ], 'models');
            }



        }
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'chatgpt')
            ->group(__DIR__ . '/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/chatgpt')
            ->group(__DIR__ . '/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
