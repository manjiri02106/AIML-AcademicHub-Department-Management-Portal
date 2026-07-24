/* ==========================================================================
   AIML AcademicHub Department Management Portal - Analytics Charts
   Chart.js visualizers for Attendance Trends, Subject Stats & Grade Distributions
   ========================================================================== */

let chartInstances = {};

function initDashboardCharts() {
  if (typeof Chart === 'undefined') return;

  const isDark = document.body.classList.contains('dark-mode');
  const textColor = isDark ? '#94a3b8' : '#64748b';
  const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';

  // 1. Attendance Trend Chart (Area Chart)
  const trendCtx = document.getElementById('attendanceTrendChart')?.getContext('2d');
  if (trendCtx) {
    if (chartInstances.trend) chartInstances.trend.destroy();
    
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.35)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    chartInstances.trend = new Chart(trendCtx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        datasets: [{
          label: 'Attendance Rate (%)',
          data: [86, 91, 88, 94, 89, 92],
          borderColor: '#2563eb',
          borderWidth: 3,
          backgroundColor: gradient,
          fill: true,
          tension: 0.35,
          pointRadius: 4,
          pointBackgroundColor: '#2563eb'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          x: { grid: { color: gridColor }, ticks: { color: textColor } },
          y: { min: 60, max: 100, grid: { color: gridColor }, ticks: { color: textColor, callback: v => v + '%' } }
        }
      }
    });
  }

  // 2. Subject-wise Attendance Bar Chart
  const subjCtx = document.getElementById('subjectAttendanceChart')?.getContext('2d');
  if (subjCtx) {
    if (chartInstances.subject) chartInstances.subject.destroy();

    chartInstances.subject = new Chart(subjCtx, {
      type: 'bar',
      data: {
        labels: ['Deep Learning', 'NLP', 'Comp Vision', 'RL', 'Gen AI', 'Ethics'],
        datasets: [{
          label: 'Avg Attendance (%)',
          data: [88, 92, 85, 90, 94, 86],
          backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#06b6d4', '#8b5cf6', '#64748b'],
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { color: textColor } },
          y: { min: 50, max: 100, grid: { color: gridColor }, ticks: { color: textColor, callback: v => v + '%' } }
        }
      }
    });
  }

  // 3. Marks Distribution Doughnut Chart
  const marksCtx = document.getElementById('marksDistributionChart')?.getContext('2d');
  if (marksCtx) {
    if (chartInstances.marks) chartInstances.marks.destroy();

    chartInstances.marks = new Chart(marksCtx, {
      type: 'doughnut',
      data: {
        labels: ['Grade S (Outstanding)', 'Grade A (Excellent)', 'Grade B (Good)', 'Grade C (Average)', 'Grade F (Fail)'],
        datasets: [{
          data: [6, 8, 4, 1, 1],
          backgroundColor: ['#10b981', '#2563eb', '#6366f1', '#f59e0b', '#ef4444'],
          borderWidth: 2,
          borderColor: isDark ? '#1e293b' : '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { color: textColor, font: { family: 'Poppins', size: 11 } } }
        },
        cutout: '70%'
      }
    });
  }
}

// Re-render charts on dark mode toggle
function updateChartsTheme() {
  initDashboardCharts();
}
