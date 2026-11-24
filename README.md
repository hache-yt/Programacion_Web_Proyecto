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

## Sistemas de riego y telemetría

Cada cuenta puede registrar varios sistemas asociados a su parcela principal. El panel permite configurar el modo y los parámetros de cada uno, asociar registros históricos de riego de forma explícita y guardar lecturas de prueba con tipo, unidad, sensor opcional y fecha. No se conectan sensores ni actuadores en esta versión: las lecturas manuales se identifican como pruebas, el estado del riego no cambia y las órdenes se guardan como `no_conectado`. La automatización solo conserva reglas; no hay un proceso que las ejecute.

Para instalaciones existentes, primero respalda la base de datos y revisa la migración `2026_11_14_000001_add_irrigation_system_management.php`. Cuando decidas desplegarla en el entorno correspondiente, ejecuta `php artisan migrate --pretend` para revisar las operaciones y luego `php artisan migrate`; no uses `migrate:fresh` ni `db:wipe`. La migración agrega la tabla `sistemas_riego`, añade campos a `medicions`, `configuracions` y `comandos` y un vínculo opcional en `riegos`; no reasigna registros históricos. El `down()` retira únicamente las columnas nuevas y la tabla nueva, por lo que un rollback posterior a la captura de datos añadidos elimina esos datos nuevos. No ejecutes migraciones en producción sin un respaldo y una ventana de mantenimiento aprobados.

Para conectar hardware se necesita definir y asegurar un protocolo de ingesta autenticado (identidad por sistema/sensor, transporte, validación y protección contra repetición), además de un servicio o cola que ejecute órdenes y confirme estado del actuador. La ejecución automática requerirá validar sensores recientes, nivel de agua, fallas, límites y temporizadores en ese servicio antes de emitir cualquier orden. Esta aplicación no supone que esos componentes existan.

## Cuentas locales

Los datos de demostración se crean únicamente en `APP_ENV=local`. Para crear las cuentas locales, define `ADMIN_DEVELOPMENT_PASSWORD` y `DEMO_USER_PASSWORD` en el archivo `.env` privado antes de ejecutar `php artisan db:seed`. No se incluyen credenciales predeterminadas en el repositorio. En producción no se crean estas cuentas; si una cuenta marcada para cambio inicial de contraseña se usa allí, el sistema exige cambiarla antes de acceder al resto de la plataforma.

## Pruebas

`php artisan test tests/Feature`

## Evidencia frente a la rúbrica

| Indicador | Dónde se evidencia |
|---|---|
| 1. Comprensión y aplicación del framework | Laravel 12 en `composer.json`; rutas y middleware en `routes/web.php`; controladores, validación con Form Requests, modelos Eloquent, migraciones y vistas Blade. |
| 2. Estructura y navegabilidad | Menú por rol, rutas con nombre, navegación activa y enlaces a los módulos en `resources/views/layouts/app.blade.php`; los permisos se verifican en middleware y rutas. |
| 3. Apariencia visual | Panel de usuario en `resources/views/inicio.blade.php`, panel administrativo en `resources/views/admin/index.blade.php` y estilos centralizados en `public/css/styles.css`. |
| 4. Compatibilidad y responsabilidad | `public/css/styles.css` adapta el diseño en puntos de quiebre móvil y contempla movimiento reducido; las vistas usan etiquetas semánticas, estados accesibles y controles etiquetados. |
| 5. Consultas con Laravel y Eloquent | Relaciones y ámbitos de acceso en `app/Models`; vistas con `with()` y listados paginados. Los gráficos agrupan litros y promedian lecturas en la base de datos en `InicioController` y `EstadisticaController`, en vez de cargar todo el historial a PHP. |
| 6. Funcionalidad y requisitos | Registro de riegos, alertas, estadísticas, sistemas, lecturas de prueba, historial y administración; flujos principales cubiertos por `tests/Feature`. No se afirma control real de hardware: las órdenes y lecturas se indican como no conectadas o de prueba. |
| 7. Pruebas relacionadas con seguridad | `AccesoTest` comprueba autenticación, cuentas inactivas y autorización administrativa; `RiegoTest` comprueba aislamiento de datos y escape de contenido HTML; `SistemaRiegoTest` comprueba que no se consulten sistemas ajenos. Las rutas web conservan middleware CSRF de Laravel y el chatbot tiene límite de solicitudes. |
| 8. Claridad del informe | Este README documenta instalación, configuración, límites de hardware, migraciones y pruebas, además de mapear la evidencia por indicador. |
| 9. Acceso para diferentes usuarios | Registro e inicio de sesión para usuarios; middleware `admin` reserva administración y gestión de cuentas; ámbitos `visiblesPara()` separan datos de cada cuenta y los administradores pueden consultar datos globales. |
| 10. Presentación profesional del dashboard | `resources/views/inicio.blade.php` presenta indicadores, consumo gráfico, sistemas, actividad reciente y accesos rápidos; `resources/views/admin/index.blade.php` muestra indicadores y actividad para administración. |

### Ejecutar y revisar las pruebas

Desde la carpeta del proyecto, ejecuta `php artisan test tests/Feature`. Las pruebas de acceso y autorización incluyen intentos con roles o recursos ajenos; las de riego verifican validación, alertas y que una observación con etiquetas `<script>` se renderice escapada; las de sistemas verifican permisos, validación de lecturas y agregaciones de estadísticas. Estas pruebas no sustituyen una auditoría de seguridad ni demuestran compatibilidad con dispositivos físicos.

## Estructura

- `routes/web.php`
- `app/Models`: User, Parcela, Riego, Alerta
- `app/Http/Controllers`: Auth, Inicio, Parcela, Riego, Alerta, Estadistica, Usuario
- `app/Http/Requests`: validación en servidor
- `database/migrations` y `database/seeders`
- `resources/views`: vistas Blade
- `public/css/styles.css`, `public/js/validaciones.js`, `public/js/graficos.js`, `public/js/interacciones.js`
- `tests/Feature`
