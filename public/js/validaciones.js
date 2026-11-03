document.addEventListener('DOMContentLoaded', () => {
  const formulario = document.getElementById('form-riego');
  if (!formulario) {
    return;
  }

  const reglas = {
    fecha: (valor) => {
      if (valor === '') {
        return 'Ingresa la fecha.';
      }
      const hoy = new Date();
      hoy.setHours(0, 0, 0, 0);
      return new Date(valor + 'T00:00:00') > hoy ? 'La fecha no puede ser futura.' : '';
    },
    duracion_min: (valor) => (/^[1-9]\d*$/.test(valor.trim()) ? '' : 'Ingresa minutos enteros mayores a 0.'),
    cantidad_litros: (valor) => (Number(valor) > 0 ? '' : 'Ingresa litros mayores a 0.'),
  };

  const campos = Object.keys(reglas).map((nombre) => formulario.elements[nombre]);

  const validar = (campo) => {
    const mensaje = reglas[campo.name](campo.value);
    campo.closest('.campo').querySelector('.error').textContent = mensaje;
    campo.setAttribute('aria-invalid', mensaje ? 'true' : 'false');
    return mensaje === '';
  };

  campos.forEach((campo) => campo.addEventListener('blur', () => validar(campo)));

  formulario.addEventListener('submit', (evento) => {
    const resultados = campos.map(validar);
    if (resultados.includes(false)) {
      evento.preventDefault();
      campos[resultados.indexOf(false)].focus();
    }
  });
});
