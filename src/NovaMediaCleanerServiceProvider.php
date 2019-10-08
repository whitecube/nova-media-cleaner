<?php

namespace Whitecube\NovaMediaCleaner;

use Illuminate\Support\ServiceProvider;

class NovaPageServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the Container.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Register Console Commands
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->commands([
            CreateTemplate::class
        ]);
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
