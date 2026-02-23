(function (Drupal, drupalSettings) {
  function valueToColor(v) {
    // 0=No, 1=Partial, 2=Full
    if (v === 2) return '#16a34a'; // green
    if (v === 1) return '#f59e0b'; // amber
    return '#ef4444';             // red
  }

  function valueToLabel(v) {
    if (v === 2) return 'Full';
    if (v === 1) return 'Partial';
    return 'No';
  }

  Drupal.behaviors.dashchartCompatibilityMatrix = {
    attach(context) {
      const canvas = context.querySelector('#dashchartMatrixCanvas');
      if (!canvas || canvas.dataset.processed) return;
      canvas.dataset.processed = '1';

      const cfg = drupalSettings.dashchart?.compatibility;
      if (!cfg) return;

      const devices = cfg.devices || [];
      const features = cfg.features || [];
      const points = cfg.points || [];

      // chartjs-chart-matrix 会注册 matrix controller
      const chart = new Chart(canvas, {
        type: 'matrix',
        data: {
          datasets: [{
            label: 'Compatibility',
            data: points,
            backgroundColor: (ctx) => valueToColor(ctx.raw.v),
            borderWidth: 1,
            borderColor: 'rgba(0,0,0,0.08)',
            // 每个 cell 的宽高：根据 chartArea 动态计算
            width: ({ chart }) => {
              const a = chart.chartArea;
              if (!a) return 10;
              return Math.max(10, Math.floor(a.width / Math.max(1, devices.length)) - 2);
            },
            height: ({ chart }) => {
              const a = chart.chartArea;
              if (!a) return 10;
              return Math.max(10, Math.floor(a.height / Math.max(1, features.length)) - 2);
            },
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,

          // 关键：给左侧/底部留足空间放文字
          layout: {
            padding: { top: 10, right: 10, bottom: 90, left: 220 }
          },

          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                title: (items) => {
                  const r = items[0].raw;
                  return `${devices[r.x]} / ${features[r.y]}`;
                },
                label: (item) => `Status: ${valueToLabel(item.raw.v)}`
              }
            }
          },

          scales: {
            x: {
              type: 'linear',
              position: 'bottom',
              min: -0.5,
              max: devices.length - 0.5,
              ticks: {
                stepSize: 1,
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90,
                font: { size: 11 },
                callback: (value) => {
                  const idx = Math.round(value);
                  const label = devices[idx] ?? '';
                  return label.length > 14 ? label.slice(0, 14) + '…' : label;
                },
              },
              grid: { display: false },
            },
            y: {
              type: 'linear',
              min: -0.5,
              max: features.length - 0.5,
              ticks: {
                stepSize: 1,
                autoSkip: false,
                font: { size: 11 },
                callback: (value) => {
                  const idx = Math.round(value);
                  const label = features[idx] ?? '';
                  return label.length > 24 ? label.slice(0, 24) + '…' : label;
                },
              },
              grid: { display: false },
            },
          }
        }
      });
    }
  };
})(Drupal, drupalSettings);
