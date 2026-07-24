/* AIML ACADEMICHUB - Chart Visualizations Engine (Normal White Theme) */

window.ChartEngine = {
  renderDashboardCharts: function() {
    if (!window.Chart) return;

    function destroyIfExists(canvas) {
      if (!canvas || typeof Chart.getChart !== 'function') return;
      var existing = Chart.getChart(canvas);
      if (existing) {
        existing.destroy();
      }
    }

    // Chart 1: Pass % & Attendance Trends
    var ctx1 = document.getElementById('dashChartPassAtt');
    if (ctx1) {
      destroyIfExists(ctx1);
      new Chart(ctx1, {
        type: 'line',
        data: {
          labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5', 'Sem 6', 'Sem 7', 'Sem 8'],
          datasets: [
            { label: 'Pass %', data: [94, 91, 88, 92, 95, 93, 96, 98], borderColor: '#059669', backgroundColor: 'rgba(5, 150, 105, 0.1)', fill: true, tension: 0.4 },
            { label: 'Avg Attendance %', data: [89, 87, 84, 88, 90, 89, 92, 94], borderColor: '#0284c7', backgroundColor: 'rgba(2, 132, 199, 0.1)', fill: true, tension: 0.4 }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom', labels: { color: '#475569' } } },
          scales: {
            x: { ticks: { color: '#475569' }, grid: { color: '#e2e8f0' } },
            y: { ticks: { color: '#475569' }, grid: { color: '#e2e8f0' }, beginAtZero: false, min: 70, max: 100 }
          }
        }
      });
    }

    // Chart 2: CO-PO Attainment Radar
    var ctx2 = document.getElementById('dashChartCopAttainment');
    if (ctx2) {
      destroyIfExists(ctx2);
      new Chart(ctx2, {
        type: 'radar',
        data: {
          labels: ['PO1 Engg Knowledge', 'PO2 Problem Analysis', 'PO3 Design Solution', 'PO4 Investigation', 'PO5 Modern Tools (PyTorch)', 'PSO1 Vision & NLP', 'PSO2 MLOps'],
          datasets: [
            { label: 'Actual Attainment', data: [2.85, 2.72, 2.90, 2.68, 2.95, 2.88, 2.80], borderColor: '#4f46e5', backgroundColor: 'rgba(79, 70, 229, 0.2)' },
            { label: 'Target Threshold', data: [2.5, 2.5, 2.5, 2.5, 2.5, 2.5, 2.5], borderColor: '#d97706', borderDash: [5, 5], backgroundColor: 'transparent' }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom', labels: { color: '#475569' } } },
          scales: { r: { angleLines: { color: '#cbd5e1' }, grid: { color: '#cbd5e1' }, pointLabels: { color: '#475569' }, min: 0, max: 3 } }
        }
      });
    }

    // Chart 3: Placement CTC Distribution
    var ctx3 = document.getElementById('dashChartPlacementTier');
    if (ctx3) {
      destroyIfExists(ctx3);
      new Chart(ctx3, {
        type: 'doughnut',
        data: {
          labels: ['Super Dream (30+ LPA)', 'Dream (15-30 LPA)', 'Core (8-15 LPA)', 'Higher Studies / Seeking'],
          datasets: [{
            data: [15, 25, 12, 8],
            backgroundColor: ['#059669', '#0284c7', '#4f46e5', '#d97706']
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom', labels: { color: '#475569' } } }
        }
      });
    }

    // Chart 4: Research & Patents Output
    var ctx4 = document.getElementById('dashChartResearch');
    if (ctx4) {
      destroyIfExists(ctx4);
      new Chart(ctx4, {
        type: 'bar',
        data: {
          labels: ['2021', '2022', '2023', '2024', '2025'],
          datasets: [
            { label: 'IEEE / Scopus Papers', data: [8, 12, 18, 24, 29], backgroundColor: '#4f46e5', borderRadius: 4 },
            { label: 'Patents Filed', data: [1, 2, 4, 6, 9], backgroundColor: '#0284c7', borderRadius: 4 }
          ]
        },
        options: {
          responsive: true,
          plugins: { legend: { position: 'bottom', labels: { color: '#475569' } } },
          scales: {
            x: { ticks: { color: '#475569' }, grid: { color: '#e2e8f0' } },
            y: { ticks: { color: '#475569' }, grid: { color: '#e2e8f0' } }
          }
        }
      });
    }
  }
};
