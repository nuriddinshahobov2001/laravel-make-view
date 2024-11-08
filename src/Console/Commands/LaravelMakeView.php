<?php

namespace Zintel\LaravelViewMaker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaravelMakeView extends Command
{
    protected $signature = 'make:view {name} {--all} {--i} {--sh} {--u} {--e}';
    protected $description = 'Generate view files for a given section with optional flags for individual files.';

    public function handle()
    {
        $name = $this->argument('name');
        $viewDirectory = resource_path('views/' . $name);

        // Создаем директорию для представлений, если ее нет
        if (!File::isDirectory($viewDirectory)) {
            File::makeDirectory($viewDirectory, 0755, true);
        }

        // Генерируем файлы в зависимости от флагов
        if ($this->option('all') || $this->option('i')) {
            $this->createFile($viewDirectory, 'index');
        }

        if ($this->option('all') || $this->option('e')) {
            $this->createFile($viewDirectory, 'edit');
        }

        if ($this->option('all') || $this->option('u')) {
            $this->createFile($viewDirectory, 'update');
        }

        if ($this->option('all') || $this->option('sh')) {
            $this->createFile($viewDirectory, 'show');
        }

        $this->info('View files created successfully!');
    }

    protected function createFile($directory, $viewName)
    {
        $filePath = $directory . DIRECTORY_SEPARATOR . "{$viewName}.blade.php";

        if (!File::exists($filePath)) {
            $content = "<!-- {$viewName} view content -->"; // Контент шаблона по умолчанию
            File::put($filePath, $content);
            $this->info("Created {$viewName}.blade.php");
        } else {
            $this->warn("{$viewName}.blade.php already exists.");
        }
    }
}
