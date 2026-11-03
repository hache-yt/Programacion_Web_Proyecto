# Plataforma Web para la Gestión y Optimización del Uso de Agua en Riego

Laravel 12, PHP 8.2 o superior, MySQL (XAMPP).

## Requisitos

- XAMPP con Apache y MySQL activos
- Composer instalado

## Instalación en XAMPP

1. Copiar la carpeta del proyecto en `C:\xampp\htdocs\plataforma-riego` (o en la carpeta `htdocs` de tu instalación de XAMPP)
2. Iniciar MySQL desde el panel de XAMPP
3. Ejecutar `instalar.bat` (instala las dependencias de Composer, crea la base `riego_db`, migra y carga datos de prueba). Esto genera `vendor/autoload.php`, necesario para iniciar Laravel.
4. Para volver a iniciarlo: `iniciar.bat`, y abrir http://localhost:8000

Si aparece un error indicando que no existe `vendor/autoload.php`, ejecuta `instalar.bat` desde la carpeta del proyecto para instalar las dependencias.

## Parcela principal

Cada cuenta trabaja con una parcela principal: los nuevos riegos se vinculan automáticamente a ella y ya no se ofrecen opciones para crear, cambiar o eliminar parcelas. Los registros y las relaciones históricas existentes se conservan; no se ejecuta una migración que los elimine o reasigne.

## Cuentas de prueba

- Administrador: admin@riego.test / admin1234
- Usuario: usuario@riego.test / usuario1234

## Pruebas

`php artisan test tests/Feature`

## Estructura

- `routes/web.php`
- `app/Models`: User, Parcela, Riego, Alerta
- `app/Http/Controllers`: Auth, Inicio, Parcela, Riego, Alerta, Estadistica, Usuario
- `app/Http/Requests`: validación en servidor
- `database/migrations` y `database/seeders`
- `resources/views`: vistas Blade
- `public/css/styles.css`, `public/js/validaciones.js`, `public/js/graficos.js`, `public/js/interacciones.js`
- `tests/Feature`
