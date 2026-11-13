# Chatbot · Riego Inteligente

Usa OpenRouter y el router gratuito `openrouter/free`. Los modelos gratuitos tienen límites y pueden estar temporalmente ocupados.

## Preparar la clave

1. Crea una cuenta y una clave en https://openrouter.ai/keys.
2. En Laravel, agrega `OPENROUTER_API_KEY=tu_clave` al archivo `.env` privado del servidor.
3. Para el prototipo independiente, configura `OPENROUTER_API_KEY` en el entorno de PHP.

No guardes la clave en JavaScript ni en archivos versionados. Revoca y reemplaza cualquier clave que se haya compartido o publicado anteriormente.

## Integración con Laravel

Laravel sirve la interfaz en `/asistente/index.html` y las preguntas en `/asistente/chat.php`. Conserva la carpeta `riego-chatbot-infinityfree` junto a los archivos de Laravel al desplegar, y configura la clave en el `.env` privado del servidor.

El asistente aparece mediante un botón flotante en la aplicación y no consulta cuentas ni registros privados.

## Probar el prototipo por separado

1. Abre `iniciar_chatbot.bat`.
2. La página se abrirá en `http://127.0.0.1:8787`.
3. Para cerrar, cierra la ventana de consola.

## Límites y privacidad

OpenRouter envía cada pregunta y la guía incluida al modelo seleccionado. No incluyas datos personales, contraseñas ni datos privados de usuarios. La cuota de modelos gratuitos es limitada y la disponibilidad depende del modelo y proveedor. Si se agota o no está disponible, espera o prueba más tarde; el programa no cambia a modelos de pago.

Este chatbot no accede a las cuentas, registros ni base de datos de Laravel. No controla bombas ni válvulas. La conversación se conserva solo en la memoria de la página abierta.

