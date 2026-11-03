document.addEventListener('DOMContentLoaded', () => {
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
