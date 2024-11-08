# This package will help you create a view (blade)

# Install
```bash
composer require zintel/laravel-make-view
```
### Change config file
```php
<?php
// config/app.php

'providers' => ServiceProvider::defaultProviders()->merge([
        //...
        
        \Zintel\LaravelViewMaker\Providers\ViewGeneratorServiceProvider::class,
        
    ])->toArray(),
```

### Usage
```bash
$ php artisan make:view forder_name (crearet file)

--all - (index.blade.php, show.blade.php, edit.blade.php)
--i   - (index.blade.php)
--sh  - (show.blade.php)
--e   - (edit.blade.php)
```
### Example
```bash
$ php artisan make:view  admin --all 
```
