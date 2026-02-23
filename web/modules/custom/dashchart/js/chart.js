(function (Drupal, drupalSettings) {
  Drupal.behaviors.dashchartBehavior = {
    attach(context) {

      console.log(999);
      const el = context.querySelector('#dashchartCanvas');
      if (!el || el.dataset.processed) return;
      el.dataset.processed = '1';

      const cfg = drupalSettings.dashchart?.devices;
      if (!cfg) return;

      new Chart(el, {
        type: 'radar',
        data: {
          labels: cfg.labels,
          datasets: [{
            label: 'Device Score',
            data: cfg.values,
          }],
        },
        options: {
          responsive: true,
          scales: {
            r: {
              beginAtZero: true,
              suggestedMax: 100
            }
          }
        }
      });
    }
  };
})(Drupal, drupalSettings);
