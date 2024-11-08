<?php

namespace Zintel\LaravelService\Providers;


use YourNamespace\Commands\MakeViewCommand;
use Zintel\LaravelService\Console\Commands\MakeServiceCommand;

class MakeServiceCommandProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeViewCommand::class
            ]);
        }
    }
}