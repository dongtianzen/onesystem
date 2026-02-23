(function (Drupal, drupalSettings) {
  Drupal.behaviors.dashchartBrand = {
    attach(context) {
      const el = context.querySelector('#dashchartBrandCanvas');
      if (!el || el.dataset.processed) return;
      el.dataset.processed = '1';

      const cfg = drupalSettings.dashchart?.brandStats;
      if (!cfg) return;

      const meta = context.querySelector('#dashchartBrandMeta');
      if (meta) meta.textContent = `Showing Top ${cfg.topN}`;

      const chart = new Chart(el, {
        type: 'bar',
        data: {
          labels: cfg.labels,
          datasets: [{
            label: 'Products',
            data: cfg.counts,
            barThickness: 14,
            borderRadius: 6,
          }],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (ctx) => ` ${ctx.parsed.x} products`,
              }
            }
          },
          scales: {
            x: {
              beginAtZero: true,
              ticks: { precision: 0 },
              grid: { drawBorder: false },
            },
            y: {
              grid: { display: false },
            }
          },
          onClick: (evt, elements) => {
            if (!elements?.length) return;
            const idx = elements[0].index;
            const url = cfg.urls?.[idx];
            if (url) window.location.href = url;
          }
        }
      });
    }
  };
})(Drupal, drupalSettings);
