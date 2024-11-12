<?php
namespace Zintel\LaravelViewMaker\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeViewCommand extends Command
{
    protected $signature = 'make:view {name} {--all} {--i} {--sh} {--e} {--cmr}';
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

        if ($this->option('all') || $this->option('sh')) {
            $this->createFile($viewDirectory, 'show');
        }

        $string = $this->option('cmr');
        if (strpos($string, 'c') !== false) {
            $this->createController($name);
        }

        if (strpos($string, 'm') !== false) {
            $this->createModel($name);
        }

        if (strpos($string, 'r') !== false) {
            $this->createRequest($name);
        }

//        // Создание контроллера, модели с миграцией и запросов, если указаны флаги
//        if ($this->option('all') || $this->option('i') || $this->option('e') || $this->option('sh')) {
//            $this->createControllerAndModel($name);
//            $this->updateControllerMethods($name);
//        }

        $this->info('View files, controller, model, migration, and requests created successfully!');
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


    protected function createController($name){
        // Путь для контроллера с динамическим пространством имен
        $controllerPath = app_path('Http/Controllers/' . ucfirst($name) . '/' . ucfirst($name) . 'Controller.php');
        if (!File::exists($controllerPath)) {
            Artisan::call('make:controller', [
                'name' =>  ucfirst($name) . '/' . ucfirst($name) . 'Controller',
                '--resource' => true, // Создаем ресурсный контроллер
            ]);
            $this->info("Created controller: {$name}/{$name}Controller.php");
        } else {
            $this->warn("Controller {$name}/{$name}Controller.php already exists.");
        }
    }

    protected function createModel($name){
        // Создание модели в папке {Name}
        $modelDirectory = app_path('Models/' . ucfirst($name));
        $modelPath = $modelDirectory . '/' . ucfirst($name) . '.php';

        if (!File::exists($modelPath)) {
            // Создаем папку для модели, если ее нет
            if (!File::exists($modelDirectory)) {
                File::makeDirectory($modelDirectory, 0755, true);
            }

            Artisan::call('make:model', [
                'name' => ucfirst($name) . '/' . ucfirst($name), // Указываем путь модели внутри папки
                '--migration' => true, // Генерация миграции
            ]);
            $this->info("Created model: " . ucfirst($name) . " in folder {$name} with migration.");
        } else {
            $this->warn("Model " . ucfirst($name) . " already exists.");
        }
    }
    protected function createRequest($name)
    {

        // Создание запроса для store
        $storeRequestPath = app_path('Http/Requests/' . ucfirst($name)  . ucfirst($name) . 'Request.php');
        if (!File::exists($storeRequestPath)) {
            Artisan::call('make:request', [
                'name' => ucfirst($name) . '/Store' . ucfirst($name) . 'Request',
            ]);
            $this->info("Created request: Store{$name}Request.php");
        } else {
            $this->warn("Request Store{$name}Request.php already exists.");
        }

        // Создание запроса для update
        $updateRequestPath = app_path('Http/Requests/' . ucfirst($name) . '/Update' . ucfirst($name) . 'Request.php');
        if (!File::exists($updateRequestPath)) {
            Artisan::call('make:request', [
                'name' => ucfirst($name) . '/Update' . ucfirst($name) . 'Request',
            ]);
            $this->info("Created request: Update{$name}Request.php");
        } else {
            $this->warn("Request Update{$name}Request.php already exists.");
        }

        $this->updateControllerMethods($name);
    }


    protected function updateControllerMethods($name)
    {
        // Путь к контроллеру
        $controllerPath = app_path('Http/Controllers/' . ucfirst($name) . '/' . ucfirst($name) . 'Controller.php');

        if (!File::exists($controllerPath)) {
            $this->error("Controller {$name}/{$name}Controller.php not found.");
            return;
        }

        // Загружаем содержимое контроллера
        $controllerContent = File::get($controllerPath);

        // Классы запросов
        $storeRequestClass = 'Store' . ucfirst($name) . 'Request';
        $updateRequestClass = 'Update' . ucfirst($name) . 'Request';

        // Регулярные выражения для поиска методов
        $storePattern = '/public\s+function\s+store\s?\((.*?Request.*?)\)/';
        $updatePattern = '/public\s+function\s+update\s?\((.*?Request.*?)\)/';

        // Обновляем метод store
        $controllerContent = $this->replaceRequestInMethod(
            $controllerContent,
            $storePattern,
            'store',
            $storeRequestClass
        );

        // Обновляем метод update
        $controllerContent = $this->replaceRequestInMethod(
            $controllerContent,
            $updatePattern,
            'update',
            $updateRequestClass
        );

        // Сохраняем обновленный контроллер
        File::put($controllerPath, $controllerContent);
        $this->info('Controller methods updated successfully.');
    }

    /**
     * Метод для замены типов Request в контроллере
     */
    protected function replaceRequestInMethod($content, $pattern, $methodName, $requestClass)
    {
        if (preg_match($pattern, $content)) {
            // Заменяем Request на соответствующий запрос
            $content = preg_replace_callback($pattern, function ($matches) use ($requestClass) {
                return str_replace($matches[1], "{$requestClass} \$request", $matches[0]);
            }, $content);

            $this->info("Updated {$methodName} method to use {$requestClass}");
        } else {
            $this->warn("Method {$methodName} not found or already updated.");
        }

        return $content;
    }
}

