<?php

namespace Zintel\LaravelViewMaker\Providers;


use Illuminate\Support\ServiceProvider;
use Zintel\LaravelViewMaker\Console\Commands\MakeViewCommand;

class ViewGeneratorCommandProvider extends ServiceProvider
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