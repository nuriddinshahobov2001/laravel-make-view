# This package will help you create a view (blade)

# Install
```bash
composer require zintel/laravel-make-view
```
### Change config file
```php
<?php
// config/app.php

'providers' = [
        //...  
        Zintel\LaravelViewMaker\LaravelViewMakerServiceProvider::class,
        
    ],
```

### Usage
```bash
$ php artisan make:view admin --all --o=

--all - (index.blade.php, show.blade.php, edit.blade.php)
--i   - (index.blade.php)
--sh  - (show.blade.php)
--e   - (edit.blade.php)
--c   - (create controller)
--m   - (create model)
--r   - (create request)
```
### Example
```bash
$ php artisan make:view  admin --all --cmr
```
