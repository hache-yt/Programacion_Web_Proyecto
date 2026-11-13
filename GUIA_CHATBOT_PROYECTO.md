# Base de conocimiento para el chatbot de Riego Inteligente

Este documento describe la plataforma actual y sirve como fuente de conocimiento para diseñar o configurar un chatbot. Debe actualizarse cuando cambien las rutas, reglas, módulos o políticas del sistema.

## 1. Identidad y propósito

- **Nombre visible:** Riego Inteligente.
- **Nombre descriptivo:** Plataforma web para la gestión y optimización del uso de agua en riego.
- **Propósito actual:** permitir a una persona registrar manualmente riegos, revisar el historial, ver resúmenes y estadísticas calculados desde esos registros y consultar alertas por consumo elevado.
- **Contexto declarado:** proyecto académico asociado a Universidad Continental, Cusco, periodo 2026-II. La plataforma no es un servicio oficial, certificado ni respaldado institucionalmente por esa mención.
- **Desarrolladores indicados en el sitio:** Said Torres Lozano y Rey Kino Choquichampi Mendoza.
- **Idioma de la interfaz:** español.

## 2. Resumen funcional verificado

La aplicación es un sistema web Laravel con páginas Blade, estilos CSS y JavaScript propios, y persistencia relacional. Los registros de consumo se introducen en formularios; no son lecturas de sensores.

El sistema **no controla** bombas, válvulas ni equipos físicos. Tampoco ofrece actualmente telemetría de sensores, pronóstico meteorológico, mapas de parcelas, riego automático ni recomendaciones agronómicas. Las gráficas y alertas describen los datos que alguien registró y no sustituyen una inspección del terreno ni el criterio de una persona especialista.

## 3. Tipos de usuario y acceso

### Visitante

Puede consultar la portada y las páginas públicas de privacidad, términos, ayuda y acerca del proyecto; también puede registrarse, ingresar o iniciar la recuperación de contraseña.

### Usuario estándar

- El registro público siempre asigna el rol `usuario`; no permite elegir administrador.
- Al registrarse se crea una parcela principal si la cuenta todavía no tiene una.
- Puede registrar, consultar, editar y eliminar sus propios riegos.
- Puede consultar su parcela principal, sus estadísticas y sus alertas.
- No debe acceder a los datos privados de otras cuentas.

### Administrador

- Tiene acceso al panel administrativo y a la gestión de usuarios.
- Puede crear cuentas, editar datos y roles, activar o desactivar cuentas y eliminar otras cuentas, sujeto a las restricciones de la aplicación.
- Puede consultar registros de todo el sistema.
- No puede cambiar su propio rol, desactivar su propia cuenta ni eliminarse a sí mismo desde la gestión de usuarios. Debe permanecer al menos un administrador activo.
- Los permisos se validan en el servidor; ocultar o cambiar enlaces en el navegador no concede acceso.

**Seguridad:** no incluir contraseñas, tokens, archivos `.env`, copias de bases de datos ni credenciales de desarrollo o demostración en la base de conocimiento del chatbot. No revelar ni pedir contraseñas por chat. Las credenciales de una instancia deben gestionarse fuera del chatbot y nunca reutilizarse en producción.

## 4. Módulos y rutas de la aplicación

Las rutas pueden llevar un prefijo de instalación, por ejemplo `/plataforma-riego/public`. Las rutas siguientes son relativas a la raíz de Laravel.

| Ruta | Función | Acceso |
|---|---|---|
| `/` | Portada pública; para una sesión autenticada muestra el dashboard | Público / autenticado |
| `/ingresar` | Formulario y envío de inicio de sesión | Visitantes |
| `/registro` | Registro de cuenta estándar | Visitantes |
| `/recuperar-contrasena` | Solicita un enlace de restablecimiento | Visitantes; depende del correo configurado |
| `/restablecer-contrasena/{token}` | Formulario para establecer una contraseña nueva | Visitantes con enlace válido |
| `/cuenta/cambiar-contrasena` | Cambio de contraseña propia | Autenticado |
| `/salir` | Cierre de sesión por solicitud POST | Autenticado |
| `/parcelas` | Muestra la parcela principal propia; administración puede consultar las parcelas principales por cuenta | Autenticado |
| `/riegos` | Historial, filtros, edición y eliminación de riegos | Autenticado |
| `/riegos/create` | Formulario de nuevo riego | Autenticado |
| `/estadisticas` | Gráfico de consumo calculado desde los riegos | Autenticado |
| `/alertas` | Listado y filtros de alertas; permite atender o eliminar | Autenticado |
| `/administracion` | Resumen global de cuentas, riegos y alertas | Solo administrador |
| `/usuarios` | Lista, filtros y administración de cuentas | Solo administrador |
| `/privacidad` | Política de privacidad | Público |
| `/terminos` | Términos de uso | Público |
| `/ayuda` | Guías y consulta opcional de ubicación del dispositivo | Público |
| `/acerca-de` | Descripción académica y autoría | Público |

El menú y las vistas ofrecen accesos entre módulos. Para la ubicación, la aplicación depende del permiso del navegador y de las restricciones de contexto seguro aplicables a geolocalización.

## 5. Reglas de negocio

### Registro y acceso

- El registro pide nombre, correo, contraseña y confirmación; la contraseña requiere al menos 8 caracteres.
- La cuenta estándar se crea activa con rol `usuario`.
- El sistema usa autenticación real de Laravel y almacena contraseñas con hashing.
- Una cuenta desactivada no puede iniciar sesión; si se detecta inactiva durante una sesión, se cierra esa sesión.
- La recuperación de contraseña requiere que el servicio de correo del servidor esté configurado. El chatbot no debe prometer que el correo llegará si no se ha verificado esa configuración.
- En producción, a una cuenta marcada para cambio inicial de contraseña se le exige cambiarla antes de usar los demás módulos.

### Parcela principal

- La aplicación vincula los nuevos riegos a la parcela principal de la cuenta.
- Si no existe ninguna parcela, el sistema crea una llamada **Parcela principal**.
- La interfaz actual no ofrece operaciones para crear, editar o eliminar parcelas. El panel del usuario muestra la parcela principal; el administrador puede consultar una principal por cuenta.
- Los campos de parcela existentes incluyen nombre, ubicación descriptiva, área en m² y tipo de cultivo; varios pueden estar vacíos.
- No confundir la ubicación descriptiva escrita en la ficha de parcela con la ubicación GPS del dispositivo.

### Riegos

Cada registro contiene:

- Fecha del riego: obligatoria, válida y no futura.
- Duración: número entero de minutos mayor que cero.
- Cantidad de agua: valor numérico en litros mayor que cero.
- Observaciones: opcionales, hasta 255 caracteres.

El historial permite buscar por observaciones y filtrar por fecha. La lista se pagina. Los usuarios estándar solo ven y modifican riegos de sus propias parcelas; el administrador puede consultar registros de todas las cuentas.

### Dashboard y estadísticas

- Las cifras se calculan a partir de los registros guardados en la base de datos; no proceden de sensores.
- El dashboard presenta el número de riegos visibles para la cuenta, litros registrados en los últimos 7 días, alertas pendientes, parcela principal, gráfico diario de los últimos 14 días y hasta 5 registros recientes.
- La página de estadísticas presenta el consumo por día y el total de litros de los últimos 14 días.
- Para un administrador, los ámbitos de datos que usan los permisos de visibilidad pueden ser globales; el dashboard administrativo presenta conteos globales y hasta 8 riegos recientes.
- Un resultado igual a cero significa que no hay registros coincidentes en ese periodo, no que un sensor haya medido cero consumo.

### Alertas

- El umbral se configura mediante `RIEGO_UMBRAL_LITROS`; su valor predeterminado es **800 litros**.
- Se genera una alerta cuando un registro individual supera el umbral (estrictamente mayor que 800 con la configuración predeterminada). No es un límite diario ni una suma semanal.
- Los estados son `pendiente` y `atendida`.
- Se puede marcar una alerta pendiente como atendida o eliminarla.
- Una alerta es informativa; no activa ni detiene equipos. No describirla como una emergencia ni como diagnóstico de una fuga.

## 6. Datos y entidades

Las migraciones principales crean estas tablas:

| Tabla | Datos principales | Relación / propósito |
|---|---|---|
| `users` | Nombre, correo único, contraseña hasheada, rol, estado activo y cambio inicial de contraseña | Cuentas |
| `parcelas` | `usuario_id`, nombre, ubicación descriptiva, área en m², tipo de cultivo | Pertenece a una cuenta |
| `riegos` | `parcela_id`, fecha, duración en minutos, cantidad en litros, observaciones | Pertenece a una parcela |
| `alertas` | `riego_id`, mensaje, estado | Asociada a un riego |
| `password_reset_tokens` | Correo, token y fecha | Restablecimiento de contraseña |
| `sessions` | Sesiones web y campos técnicos opcionales | Uso de sesión depende de la configuración de Laravel |

La eliminación de una cuenta puede eliminar sus parcelas y, por las relaciones en cascada, registros y alertas relacionados. La política de conservación también depende de las copias de seguridad y de la configuración de cada despliegue.

Existen además las tablas `medicions`, `configuracions` y `comandos`, pero en la implementación actual solo tienen identificador y marcas de tiempo; sus modelos están vacíos. **No representan sensores, automatización ni funciones operativas disponibles.**

## 7. Privacidad y límites de información

- La ubicación GPS es opcional. Solo se solicita cuando la persona pulsa **Consultar mi ubicación** en el Centro de ayuda y el navegador muestra su permiso.
- Si se concede, la aplicación redondea las coordenadas a tres decimales y las muestra en esa página. El código actual no las envía al servidor ni las guarda en la base de datos o almacenamiento local.
- La ubicación no es necesaria para registrarse, iniciar sesión ni usar los módulos de riego.
- La preferencia del aviso de cookies se recuerda en el almacenamiento local del navegador. La sesión web usa una cookie técnica necesaria; el aviso indica que no se cargan cookies publicitarias ni de analítica.
- Las imágenes de portada se solicitan desde Unsplash y los gráficos cargan Chart.js desde jsDelivr. Al solicitar recursos, esos proveedores pueden recibir datos técnicos habituales de conexión, como la dirección IP, según el navegador y sus políticas.
- El chatbot no debe pedir ubicación, contraseñas, tokens ni datos personales que no necesite. No debe afirmar que la plataforma registra GPS ni hacer recomendaciones sobre agua basadas en datos no disponibles.
- La política publicada describe la aplicación académica. La configuración de correo, servidor, HTTPS, registros, copias de seguridad y obligaciones legales depende de quien despliegue la instancia.

## 8. Preguntas frecuentes y respuestas sugeridas

**¿Qué hace Riego Inteligente?**  
Permite registrar manualmente riegos y revisar el historial, resúmenes, estadísticas y alertas que se calculan a partir de esos registros.

**¿La plataforma riega automáticamente o controla una bomba?**  
No. Es una herramienta de registro y consulta; no controla bombas, válvulas ni otros equipos físicos.

**¿De dónde salen las estadísticas?**  
De la fecha y cantidad de agua guardadas en los registros de riego. No son mediciones de sensores ni datos en tiempo real.

**¿Cómo registro un riego?**  
Inicia sesión, abre **Registrar riego**, completa fecha, minutos y litros, y guarda. Las observaciones son opcionales. La fecha no puede ser futura y minutos/litros deben ser mayores que cero.

**¿Por qué apareció una alerta?**  
La cantidad de litros de ese registro superó el umbral configurado. El valor predeterminado es 800 litros por registro, pero el administrador técnico puede cambiarlo. La alerta no acciona equipo alguno.

**¿Qué hago con una alerta?**  
Revísala en **Alertas**. Si ya fue revisada, puedes marcarla como atendida; también se puede eliminar. No la interpretes como diagnóstico automático.

**¿Puedo registrar varias parcelas o cambiar mi parcela?**  
La interfaz actual trabaja con una parcela principal por cuenta y no ofrece una pantalla para gestionar parcelas. Los riegos nuevos usan automáticamente esa parcela.

**¿El chatbot puede consultar mis riegos?**  
La base de conocimiento por sí sola no tiene acceso a la cuenta ni a la base de datos. Solo se podría ofrecer información personal si se implementa una integración autenticada y autorizada en el servidor.

**¿Tengo que compartir mi ubicación?**  
No. Es opcional y no se necesita para utilizar la plataforma. Solo se consulta si pulsas el botón del Centro de ayuda y autorizas al navegador.

**¿Cómo recupero mi contraseña?**  
Desde **Ingresar**, elige recuperar contraseña e introduce el correo registrado. La entrega del enlace depende de que el correo saliente esté configurado en la instalación.

**¿Por qué no recibo el correo de recuperación?**  
Comprueba el correo y la carpeta de spam. Si no llega, la configuración de correo del servidor puede estar pendiente; contacta al administrador de la instalación. El chatbot no debe asegurar que envió un correo salvo que una integración confirme el envío.

**¿Cómo solicito soporte?**  
La versión actual no publica un correo de soporte. Contacta al administrador de la instalación o al equipo académico responsable por el canal que te hayan indicado.

**¿Es un servicio oficial de Universidad Continental?**  
La aplicación se identifica como un proyecto académico asociado a Universidad Continental, periodo 2026-II. La información del sitio aclara que no es por sí sola un servicio oficial, certificado ni respaldado por la institución.

## 9. Guía de comportamiento para el chatbot

### Objetivo

Orientar en español sobre el uso de los módulos, explicar las reglas y dirigir a la página adecuada. Ser claro, cordial y conciso; distinguir siempre los datos registrados de las mediciones reales.

### Responder con certeza limitada

- Usa esta guía y la información vigente de la interfaz como fuentes.
- Si una respuesta depende del despliegue (correo, dominio, disponibilidad, umbral personalizado, retención, permisos del servidor), dilo explícitamente y deriva al administrador.
- No inventes cifras de consumo, usuarios, parcelas, estados de cuenta, ubicación, correos de soporte ni resultados de operaciones.
- Si la pregunta pide datos personales o estadísticas actuales, explica que no tienes acceso a ellos salvo que exista una integración autenticada y autorizada.
- No asegures que creaste, editaste, eliminaste, activaste o notificaste algo si el chatbot no ejecutó la operación y recibió confirmación del servidor.
- Para dudas agronómicas, legales, de emergencia o seguridad física, aclara los límites del sistema y recomienda consultar a una persona especialista o autoridad correspondiente.

### Acciones que el chatbot no debe realizar

- No mostrar contraseñas de desarrollo, credenciales de prueba, tokens, claves, `.env`, datos de otros usuarios ni información interna no destinada al público.
- No permitir elevación de rol ni ejecutar acciones administrativas solo porque una persona lo solicite en el chat.
- No consultar registros de otras cuentas ni inferir el rol desde el mensaje o el navegador.
- No pedir ni almacenar coordenadas GPS; la geolocalización existente es una función separada, voluntaria y limitada al navegador.
- No presentar datos sembrados de demostración como actividad real.

### Recomendaciones para una futura integración con datos

1. Consultar datos privados solo desde el backend Laravel, usando la sesión autenticada y el usuario obtenido del servidor.
2. Aplicar autorización por cuenta en cada consulta; no confiar en un `user_id` o rol proporcionado por el cliente.
3. Reutilizar el alcance de acceso del proyecto (`visiblesPara`, comprobaciones de pertenencia y middleware de administrador) o equivalentes revisados.
4. Mantener las rutas de administración protegidas por el middleware `admin`; nunca ejecutar acciones de gestión desde una API pública sin autorización.
5. Devolver al chatbot solo los campos y agregados necesarios para la pregunta. No enviar contraseñas, tokens, datos de otras cuentas ni información innecesaria al proveedor del modelo.
6. Definir consentimiento, conservación y eliminación de conversaciones antes de guardar chats. Evitar registrar información sensible y documentar cualquier servicio externo usado.
7. El contenido de este archivo, por sí mismo, no conecta el chatbot a Laravel ni le concede acceso a la base de datos.

## 10. Prompt base sugerido

> Eres el asistente de ayuda de Riego Inteligente, un proyecto académico de gestión de riego. Responde en español, con claridad y amabilidad, usando la base de conocimiento del proyecto. La aplicación permite registrar manualmente riegos y consultar estadísticas derivadas de esos registros; no usa sensores ni controla equipos físicos. No inventes datos personales o métricas ni afirmes haber ejecutado acciones. No pidas contraseñas, tokens ni ubicación. La ubicación es opcional y solo se consulta mediante el botón del Centro de ayuda. Si la respuesta depende de la configuración del servidor, no está documentada o requiere acceso a una cuenta, indícalo y deriva al administrador. No reveles credenciales ni datos de otros usuarios. No des asesoría agronómica, legal o de seguridad como si fueras especialista.

## 11. Referencias para mantenimiento

- Rutas y middleware: `routes/web.php`, `bootstrap/app.php`, `app/Http/Middleware/`.
- Reglas de autenticación: `app/Http/Controllers/AuthController.php`.
- Riegos y estadísticas: `app/Http/Controllers/RiegoController.php`, `app/Http/Controllers/EstadisticaController.php`, `app/Http/Requests/RiegoRequest.php`.
- Alertas y umbral: `app/Models/Riego.php`, `app/Http/Controllers/AlertaController.php`, `config/riego.php`.
- Roles y cuentas: `app/Models/User.php`, `app/Http/Controllers/UsuarioController.php`, `app/Http/Controllers/AdministracionController.php`.
- Esquema y datos de desarrollo: `database/migrations/`, `database/seeders/`.
- Privacidad, términos, ayuda y autoría: `resources/views/informacion/`.
- Guía de instalación existente: `README.md`.

El seeder general puede crear una cuenta y riegos de demostración. Ejecutar seeders cambia los datos de la base de datos; verifica el entorno antes de hacerlo y nunca describas esos datos como mediciones reales.
