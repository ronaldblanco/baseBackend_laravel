# baseBackend_laravel

Install:
```
composer install 
or 
composer install --ignore-platform-req=php
```
Configure:
```
php artisan migrate:refresh --seed
php artisan passport:install
```
Start App:
```
php artisan serve
```
Create examples:
```
php artisan make:model Model -mc //Create model, migration and controller
php artisan make:transformer ModelTransformer //Create transformer
php artisan -v
```
