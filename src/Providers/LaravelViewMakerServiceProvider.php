<?php

namespace Zintel\LaravelViewMaker;

use Illuminate\Support\ServiceProvider;
use Zintel\LaravelViewMaker\Console\Commands\MakeViewCommand;

class LaravelViewMakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Регистрируем команду
        $this->commands([
            MakeViewCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
