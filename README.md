primeros pasos para hacer un dashboard noticas que hice pero mejor vean el video https://www.youtube.com/watch?v=hZ7In0NRmME&list=PLbFjjy1sD3hqpbPGYP9bxwyd2B79V09kV&index=1

composer create-project laravel/laravel curso 
APP_NAME=LeggerISO
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=devphp7.allxposible.com
DB_PORT=3306
DB_DATABASE=devphp7_iso20000
DB_USERNAME=devphp7_iso2000
DB_PASSWORD=cpsess0188927557
DB_FOREIGN_KEYS=true

modificar archivo
app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
+

php artisan migrate
php artisan migrate:status

Cómo generar modelos de Laravel desde una base de datos existente
Instalación
composer require reliese/laravel --dev
Ahora hay que publicar el archivo de configuraciones
php artisan vendor:publish --tag=reliese-models
Ahora sí 🎉
php artisan code:models
php artisan code:models --table=nombretabla

Generates Laravel Migrations from an existing database
Install via Composer:
composer require --dev kitloong/laravel-migrations-generator
php artisan migrate:generate --squash

instalar https://filamentphp.com/docs/3.x/panels/installation
composer require filament/filament:"^3.3" -W
php artisan filament:install --panels
llamarlo dashboard, luego dale yes
php artisan make:filament-user

para iniciar 
php artisan serve
donde ver http://127.0.0.1:8000/dashboard/login


https://filamentphp.com/docs/3.x/panels/resources/getting-started

los modelos estan aca App\Models\Custome
php artisan make:filament-resource nombredelmodelo  --generate
php artisan serve

Mastering FilamentPHP Roles & Permissions (A Practical Guide)
https://www.youtube.com/watch?v=A-MjUqW5Ouo


ejemplos
php artisan make:filament-resource Usuario  --generate |
php artisan make:filament-resource Contrato  --generate |
php artisan make:filament-resource ProyectoContrato  --generate 


php artisan code:models --table=calificacion  |
php artisan code:models --table=cambio |
php artisan code:models --table=checklist |
php artisan code:models --table=configuracion |
php artisan code:models --table=contrato |
php artisan code:models --table=contrato_proveedor  |
php artisan code:models --table=cronograma |
php artisan code:models --table=detalleconsumo |
php artisan code:models --table=diagnostico |
php artisan code:models --table=historial |
php artisan code:models --table=mejora |
php artisan code:models --table=proveedor |
php artisan code:models --table=proyecto |
php artisan code:models --table=proyecto_contrato |
php artisan code:models --table=proyecto_detalleconsumo |
php artisan code:models --table=proyecto_servicio |
php artisan code:models --table=proyecto_ticket |
php artisan code:models --table=recursosproyectos |
php artisan code:models --table=servicio |
php artisan code:models --table=ticket |
php artisan code:models --table=ticket_detalleconsumo |
php artisan code:models --table=ticket_servicio |
php artisan code:models --table=ticket_usuario |
php artisan code:models --table=usuario