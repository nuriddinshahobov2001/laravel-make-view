<?php
namespace Zintel\LaravelViewMaker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeViewCommand extends Command
{
    protected $signature = 'make:view {name} {--all} {--i} {--sh} {--e} {--o=}';
    protected $description = 'Generate view files, controller, model, and requests for a given section.';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $viewDirectory = resource_path("views/{$name}");

        // Создаем директорию для представлений, если ее нет
        $this->createDirectory($viewDirectory);

        // Генерация файлов представлений
        $this->generateViews($viewDirectory);

        // Генерация контроллера, модели и запросов
        $this->processCMR($name);

    }

    /**
     * Создает директорию, если она не существует.
     */
    protected function createDirectory($path)
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory(strtolower($path), 0755, true);
            $this->info("Directory created: {$path}");
        }
    }

    /**
     * Генерация файлов представлений.
     */
    protected function generateViews($viewDirectory)
    {
        $views = [
            'index' => $this->option('all') || $this->option('i'),
            'edit' => $this->option('all') || $this->option('e'),
            'show' => $this->option('all') || $this->option('sh'),
        ];

        foreach ($views as $viewName => $shouldCreate) {
            if ($shouldCreate) {
                $this->createViewFile($viewDirectory, $viewName);
            }
        }
    }

    /**
     * Создает файл представления, если он не существует.
     */
    protected function createViewFile($directory, $viewName)
    {
        $filePath = $directory . DIRECTORY_SEPARATOR . "{$viewName}.blade.php";

        if (!File::exists($filePath)) {
            $content = $this->getViewTemplate($viewName);
            File::put($filePath, $content);
            $this->info("Created view: {$viewName}.blade.php");
        } else {
            $this->warn("View already exists: {$viewName}.blade.php");
        }
    }

    /**
     * Возвращает шаблон для представления.
     */
    protected function getViewTemplate($viewName)
    {
        return <<<HTML
<!-- {$viewName} view content Powered by Nuriddin Shahobov -->

@extends('layouts.app')

@section('title')
@endsection

@section('css-links')
@endsection

@section('main')
@endsection

@section('js-links')
@endsection
HTML;
    }

    /**
     * Обрабатывает флаг --cmr для генерации контроллера, модели и запросов.
     */
   protected function processCMR($name)
{
    $cmrOptions = $this->option('o');

    if (!$cmrOptions) {
        return;
    }

    // Разбиваем строку на массив символов
    $options = str_split($cmrOptions);

    // Уникально обрабатываем каждый флаг
    if (in_array('c', $options)) {
        $this->createController($name);
    }

    if (in_array('m', $options)) {
        $this->createModel($name);
    }

    if (in_array('r', $options)) {
        $this->createRequest($name);
    }
}


    /**
     * Создает контроллер.
     */
    protected function createController($name)
    {
        $controllerPath = app_path("Http/Controllers/{$name}/{$name}Controller.php");

        if (!File::exists($controllerPath)) {
            Artisan::call('make:controller', [
                'name' => "{$name}/{$name}Controller",
                '--resource' => true,
            ]);
            $this->info("Created controller: {$name}Controller");
        } else {
            $this->warn("Controller already exists: {$name}Controller");
        }
    }

    /**
     * Создает модель с миграцией.
     */
    protected function createModel($name)
    {
        $modelPath = app_path("Models/{$name}/{$name}.php");

        if (!File::exists($modelPath)) {
            Artisan::call('make:model', [
                'name' => "Models/{$name}/{$name}Model",
                '--migration' => true,
            ]);
            $this->info("Created model: {$name}");
        } else {
            $this->warn("Model already exists: {$name}");
        }
    }

    /**
     * Создает запросы Store и Update.
     */
    protected function createRequest($name)
    {
        $requests = [
            'Store' => "Store{$name}Request",
            'Update' => "Update{$name}Request",
        ];

        foreach ($requests as $type => $className) {
            $requestPath = app_path("Http/Requests/{$name}/{$className}.php");

            if (!File::exists($requestPath)) {
                Artisan::call('make:request', [
                    'name' => "{$name}/{$className}",
                ]);
                $this->info("Created request: {$className}");
            } else {
                $this->warn("Request already exists: {$className}");
            }
        }
    }
}
