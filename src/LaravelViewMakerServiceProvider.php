<?php

namespace Zintel\LaravelViewMaker;

use Illuminate\Support\ServiceProvider;
use Zintel\LaravelViewMaker\Console\Commands\MakeViewCommand;

class LaravelViewMakerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeViewCommand::class,
        ]);
    }

    public function boot()
    {
        // Логика загрузки пакета
    }

}
