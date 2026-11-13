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
          backgroundColor: 'rgba(35, 123, 91, 0.14)',
          borderColor: '#237b5b',
          pointBackgroundColor: '#237b5b',
          pointBorderColor: '#fffefa',
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          borderWidth: 2.5,
          fill: true,
          tension: 0.35,
        }],
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#173a32',
            padding: 11,
            displayColors: false,
            callbacks: { label: (contexto) => `${contexto.parsed.y} L` },
          },
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#829189', maxRotation: 0, autoSkip: true, maxTicksLimit: 7 },
            border: { display: false },
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(23, 58, 50, 0.07)' },
            ticks: { color: '#829189' },
            border: { display: false },
          },
        },
      },
    });
  };

  crear('grafico-dias', 'line');
  crear('grafico-inicio', 'line');
});
