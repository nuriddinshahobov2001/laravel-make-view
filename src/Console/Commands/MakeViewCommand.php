<?php

namespace YourNamespace\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeViewCommand extends Command
{
    protected $signature = 'make:view {name} {--all} {--i} {--sh} {--u} {--e}';
    protected $description = 'Generate view files for a given section';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $path = resource_path("views/{$name}");

        // Создаём директорию, если её нет
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true);
        }

        // Проверяем опции и создаём нужные файлы
        if ($this->option('all') || $this->option('i')) {
            $this->createFile("{$path}/index.blade.php");
        }

        if ($this->option('all') || $this->option('e')) {
            $this->createFile("{$path}/edit.blade.php");
        }

        if ($this->option('all') || $this->option('u')) {
            $this->createFile("{$path}/update.blade.php");
        }

        if ($this->option('all') || $this->option('sh')) {
            $this->createFile("{$path}/show.blade.php");
        }

        $this->info('View files created successfully!');
    }

    protected function createFile($path)
    {
        $stub = ""; // Можешь добавить сюда содержимое шаблона по умолчанию
        $this->files->put($path, $stub);
    }
}
