document.addEventListener('DOMContentLoaded', () => {
  if (typeof Chart === 'undefined') {
    return;
  }

  const crear = (id, tipo) => {
    const lienzo = document.getElementById(id);
    if (!lienzo) {
      return;
    }
    new Chart(lienzo, {
      type: tipo,
      data: {
        labels: JSON.parse(lienzo.dataset.etiquetas),
        datasets: [{
          label: 'Litros',
          data: JSON.parse(lienzo.dataset.valores),
          backgroundColor: '#0E7C86',
          borderColor: '#0E7C86',
        }],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
      },
    });
  };

  crear('grafico-dias', 'line');
  crear('grafico-parcelas', 'bar');
});
