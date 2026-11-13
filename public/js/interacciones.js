document.addEventListener('DOMContentLoaded', () => {
  const cookieBanner = document.getElementById('cookie-banner');
  const cookiePreferenceKey = 'riego-cookie-preference';
  let cookiePreference = null;

  try {
    cookiePreference = localStorage.getItem(cookiePreferenceKey);
  } catch (error) {
    console.warn('No se pudo leer la preferencia de cookies:', error);
  }

  if (cookieBanner && !cookiePreference) {
    cookieBanner.hidden = false;
  }

  document.querySelectorAll('[data-cookie-choice]').forEach((boton) => {
    boton.addEventListener('click', () => {
      try {
        localStorage.setItem(cookiePreferenceKey, boton.dataset.cookieChoice);
      } catch (error) {
        console.warn('No se pudo guardar la preferencia de cookies:', error);
      }

      if (cookieBanner) {
        cookieBanner.hidden = true;
      }
    });
  });

  document.querySelectorAll('[data-cookie-open]').forEach((boton) => {
    boton.addEventListener('click', () => {
      if (cookieBanner) {
        cookieBanner.hidden = false;
        cookieBanner.querySelector('[data-cookie-choice]')?.focus();
      }
    });
  });

  document.querySelectorAll('[data-location-request]').forEach((boton) => {
    boton.addEventListener('click', () => {
      const resultado = document.getElementById('location-result');
      if (!resultado) {
        return;
      }

      if (!navigator.geolocation) {
        resultado.textContent = 'Este navegador no permite consultar la ubicación.';
        return;
      }

      boton.disabled = true;
      resultado.textContent = 'Solicitando permiso al navegador…';

      navigator.geolocation.getCurrentPosition(
        (position) => {
          const latitud = position.coords.latitude.toFixed(3);
          const longitud = position.coords.longitude.toFixed(3);
          resultado.textContent = `Ubicación aproximada: ${latitud}, ${longitud}. Solo se muestra en esta página; no se envía ni se guarda.`;
          boton.disabled = false;
        },
        (error) => {
          const mensajes = {
            1: 'No se concedió el permiso. Puedes continuar usando el sistema sin compartir ubicación.',
            2: 'El dispositivo no pudo determinar la ubicación. Inténtalo nuevamente o continúa sin compartirla.',
            3: 'La consulta demoró demasiado. Inténtalo nuevamente o continúa sin compartir ubicación.',
          };
          resultado.textContent = mensajes[error.code] || 'No se pudo consultar la ubicación. Puedes continuar sin compartirla.';
          boton.disabled = false;
        },
        { enableHighAccuracy: false, maximumAge: 0, timeout: 10000 }
      );
    });
  });

  const presentation = document.getElementById('presentation-screen');
  if (presentation) {
    let mostrarPresentacion = true;

    try {
      mostrarPresentacion = sessionStorage.getItem('riego-presentacion-vista') !== 'true';
      if (mostrarPresentacion) {
        sessionStorage.setItem('riego-presentacion-vista', 'true');
      }
    } catch (error) {
      console.warn('No se pudo guardar el estado de la presentación:', error);
    }

    if (mostrarPresentacion) {
      presentation.classList.add('is-visible');
      const reducido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const duracion = reducido ? 600 : 4700;
      const mensajes = [
        'Preparando una experiencia más sostenible',
        'Tecnología al servicio de cada cultivo',
        'Cada gota cuenta. Empecemos.',
      ];
      const texto = presentation.querySelector('#presentation-loading');
      mensajes.slice(1).forEach((mensaje, indice) => {
        window.setTimeout(() => {
          if (texto) {
            texto.textContent = mensaje;
          }
        }, reducido ? 0 : (indice + 1) * 1500);
      });

      window.setTimeout(() => presentation.classList.add('is-leaving'), duracion);
      window.setTimeout(() => presentation.remove(), duracion + 700);
    } else {
      presentation.remove();
    }
  }

  document.querySelectorAll('[data-password-toggle]').forEach((boton) => {
    boton.addEventListener('click', () => {
      const campo = document.getElementById(boton.dataset.passwordToggle);
      if (!(campo instanceof HTMLInputElement)) {
        return;
      }

      const mostrar = campo.type === 'password';
      campo.type = mostrar ? 'text' : 'password';
      boton.textContent = mostrar ? 'Ocultar' : 'Ver';
      boton.setAttribute('aria-label', `${mostrar ? 'Ocultar' : 'Mostrar'} contraseña`);
      boton.setAttribute('aria-pressed', String(mostrar));
    });
  });

  const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.getElementById('main-nav');
  menuToggle?.addEventListener('click', () => {
    const abierto = menuToggle.getAttribute('aria-expanded') !== 'true';
    menuToggle.setAttribute('aria-expanded', String(abierto));
    menuToggle.setAttribute('aria-label', abierto ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
    nav?.classList.toggle('is-open', abierto);
  });

  const chatbotPanel = document.getElementById('chatbot-panel');
  const chatbotFrame = chatbotPanel?.querySelector('iframe[data-src]');
  const chatbotLaunchers = document.querySelectorAll('[data-chatbot-open]');
  const chatbotClose = chatbotPanel?.querySelector('[data-chatbot-close]');

  const closeChatbot = () => {
    if (!chatbotPanel || chatbotPanel.hidden) {
      return;
    }

    chatbotPanel.hidden = true;
    chatbotLaunchers.forEach((boton) => boton.setAttribute('aria-expanded', 'false'));
    chatbotLaunchers[0]?.focus();
  };

  chatbotLaunchers.forEach((boton) => {
    boton.addEventListener('click', () => {
      if (!chatbotPanel || !chatbotFrame) {
        return;
      }

      chatbotPanel.hidden = false;
      chatbotLaunchers.forEach((lanzador) => lanzador.setAttribute('aria-expanded', 'true'));
      if (!chatbotFrame.src) {
        chatbotFrame.src = chatbotFrame.dataset.src;
      }
      chatbotClose?.focus();
    });
  });

  chatbotClose?.addEventListener('click', closeChatbot);
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeChatbot();
    }
  });

  const dialogo = document.getElementById('dialogo-confirmacion');
  const mensaje = document.getElementById('dialogo-mensaje');
  const titulo = document.getElementById('dialogo-titulo');
  const cancelar = dialogo?.querySelector('[data-confirm-cancel]');
  const aceptar = dialogo?.querySelector('[data-confirm-accept]');
  let formularioPendiente = null;

  document.addEventListener('submit', (evento) => {
    const formulario = evento.target;
    if (!(formulario instanceof HTMLFormElement) || !formulario.hasAttribute('data-confirm')) {
      return;
    }

    if (formulario.dataset.confirmed === 'true') {
      delete formulario.dataset.confirmed;
      return;
    }

    if (!dialogo || typeof dialogo.showModal !== 'function') {
      if (!window.confirm(formulario.dataset.confirm)) {
        evento.preventDefault();
      }
      return;
    }

    evento.preventDefault();
    formularioPendiente = formulario;
    mensaje.textContent = formulario.dataset.confirm;
    titulo.textContent = formulario.dataset.confirmTitle || 'Confirmar acción';
    dialogo.showModal();
  });

  cancelar?.addEventListener('click', () => dialogo.close());

  aceptar?.addEventListener('click', () => {
    if (!formularioPendiente) {
      return;
    }

    const formulario = formularioPendiente;
    formularioPendiente = null;
    formulario.dataset.confirmed = 'true';
    dialogo.close();
    formulario.requestSubmit();
  });

  dialogo?.addEventListener('close', () => {
    formularioPendiente = null;
  });
});
