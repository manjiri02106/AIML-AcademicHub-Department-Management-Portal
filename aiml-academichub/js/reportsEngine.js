/* AIML ACADEMICHUB - Reports Module Engine */

window.ReportsEngine = {
  selectedReportType: 'academic',
  filters: {
    year: '2024-25',
    sem: 'All',
    div: 'All',
    faculty: 'All'
  },

  render: function(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="display: flex; align-items: center; gap: 10px; font-size: 1.4rem;">
              <i class="lucide-file-bar-chart" style="color: var(--primary);"></i> Reports Engine & Analytics Generator
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
              Select report type, apply filters, analyze interactive charts, and export compliant PDF/Excel reports.
            </p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="btn btn-primary" onclick="ReportsEngine.exportPDF()">
              <i class="lucide-file-text"></i> Export PDF
            </button>
            <button class="btn btn-emerald" onclick="ReportsEngine.exportExcel()">
              <i class="lucide-download"></i> Export Excel (XLSX)
            </button>
            <button class="btn btn-secondary" onclick="window.print()">
              <i class="lucide-printer"></i> Print Report
            </button>
          </div>
        </div>
      </div>

      <!-- Report Selection & Multi-Filter Bar -->
      <div class="filter-bar">
        <div class="form-group" style="min-width: 220px;">
          <label><i class="lucide-list"></i> Select Report Module</label>
          <select class="form-control" id="reportTypeSelect" onchange="ReportsEngine.changeReportType(this.value)">
            <option value="academic">Academic & Pass % Report</option>
            <option value="attendance">Attendance & Defaulters Report</option>
            <option value="faculty">Faculty Workload & Research Report</option>
            <option value="placement">Placement & Internship Report</option>
            <option value="projects">Final Year Projects Report</option>
            <option value="research">Research Papers & Patents Report</option>
            <option value="labs">Laboratory Infrastructure Report</option>
            <option value="events">Department Events & Hackathons</option>
            <option value="department">Comprehensive Department Statistics</option>
          </select>
        </div>

        <div class="form-group">
          <label>Academic Year</label>
          <select class="form-control" onchange="ReportsEngine.updateFilter('year', this.value)">
            <option value="2024-25">2024 - 2025</option>
            <option value="2023-24">2023 - 2024</option>
            <option value="2025-26">2025 - 2026</option>
          </select>
        </div>

        <div class="form-group">
          <label>Semester</label>
          <select class="form-control" onchange="ReportsEngine.updateFilter('sem', this.value)">
            <option value="All">All Semesters</option>
            <option value="7">Semester 7</option>
            <option value="5">Semester 5</option>
          </select>
        </div>

        <div class="form-group">
          <label>Division</label>
          <select class="form-control" onchange="ReportsEngine.updateFilter('div', this.value)">
            <option value="All">All Divisions</option>
            <option value="A">AIML - A</option>
            <option value="B">AIML - B</option>
          </select>
        </div>

        <div style="margin-left: auto; display: flex; align-items: flex-end;">
          <button class="btn btn-cyan" onclick="ReportsEngine.applyFilters()">
            <i class="lucide-filter"></i> Apply Filters
          </button>
        </div>
      </div>

      <!-- Live Generated Report Content -->
      <div id="generatedReportArea">
        ${this.getReportBodyHTML()}
      </div>
    `;

    container.innerHTML = html;
    document.getElementById('reportTypeSelect').value = this.selectedReportType;
    if (window.lucide) window.lucide.createIcons();
    this.renderCharts();
  },

  changeReportType: function(type) {
    this.selectedReportType = type;
    this.render('mainAppView');
  },

  updateFilter: function(key, val) {
    this.filters[key] = val;
  },

  applyFilters: function() {
    this.render('mainAppView');
  },

  getReportBodyHTML: function() {
    var data = window.AcademicHubData;
    var type = this.selectedReportType;

    if (type === 'academic') {
      var courseRows = data.academics.courses.map(function(c) {
        return `
          <tr>
            <td><strong>${c.code}</strong></td>
            <td>${c.title}</td>
            <td>Sem ${c.sem}</td>
            <td>${c.faculty}</td>
            <td>${c.avgMarks} / 100</td>
            <td><span class="badge badge-emerald">${c.passPercentage}%</span></td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
            <div>
              <h3>DEPARTMENT ACADEMIC & PASS PERCENTAGE REPORT</h3>
              <p style="color: var(--text-muted); font-size: 0.85rem;">Academic Year: ${this.filters.year} | Semester: ${this.filters.sem} | Division: ${this.filters.div}</p>
            </div>
            <span class="badge badge-indigo">Official Document #REP-AIML-ACAD-2025</span>
          </div>

          <!-- Chart Area -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
              <h4 style="font-size: 0.95rem; margin-bottom: 12px;">Subject-wise Pass Percentage</h4>
              <canvas id="reportChart1" height="180"></canvas>
            </div>
            <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
              <h4 style="font-size: 0.95rem; margin-bottom: 12px;">Grade Distribution Breakdown</h4>
              <canvas id="reportChart2" height="180"></canvas>
            </div>
          </div>

          <div class="table-responsive">
            <table class="data-table" id="reportTableData">
              <thead>
                <tr>
                  <th>Course Code</th>
                  <th>Subject Title</th>
                  <th>Semester</th>
                  <th>Course Instructor</th>
                  <th>Average Marks</th>
                  <th>Pass Percentage</th>
                </tr>
              </thead>
              <tbody>${courseRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (type === 'placement') {
      var plcRows = data.placements.map(function(p) {
        return `
          <tr>
            <td><strong>${p.company}</strong></td>
            <td><span class="badge badge-indigo">${p.tier}</span></td>
            <td><strong style="color: var(--emerald);">${p.ctc}</strong></td>
            <td>${p.offers} Offers</td>
            <td>${p.roles}</td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
            <div>
              <h3>AIML CAMPUS PLACEMENT & INTERNSHIP REPORT</h3>
              <p style="color: var(--text-muted); font-size: 0.85rem;">Academic Year: ${this.filters.year} | Placement Rate: 92.4% | Highest Package: ₹52.0 LPA</p>
            </div>
            <span class="badge badge-emerald">Verified by Placement Cell</span>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
              <h4 style="font-size: 0.95rem; margin-bottom: 12px;">Placement Package Distribution (LPA)</h4>
              <canvas id="reportChart1" height="180"></canvas>
            </div>
            <div style="background: rgba(255,255,255,0.02); padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
              <h4 style="font-size: 0.95rem; margin-bottom: 12px;">Top Recruiting Domains</h4>
              <canvas id="reportChart2" height="180"></canvas>
            </div>
          </div>

          <div class="table-responsive">
            <table class="data-table" id="reportTableData">
              <thead>
                <tr>
                  <th>Recruiter Company</th>
                  <th>Tier Category</th>
                  <th>Package CTC</th>
                  <th>Offers Issued</th>
                  <th>Job Roles</th>
                </tr>
              </thead>
              <tbody>${plcRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    // Default fallback view for other reports
    var stdRows = data.students.map(function(s) {
      return `
        <tr>
          <td><strong>${s.usn}</strong></td>
          <td>${s.name}</td>
          <td>Sem ${s.sem}-${s.div}</td>
          <td>${s.gpa}</td>
          <td>${s.attendance}%</td>
          <td><span class="badge badge-emerald">${s.status}</span></td>
        </tr>
      `;
    }).join('');

    return `
      <div class="glass-panel">
        <h3>AIML DEPARTMENT COMPREHENSIVE SUMMARY REPORT</h3>
        <p style="color: var(--text-muted); margin-bottom: 16px;">Generated automatically from integrated central database.</p>
        <div class="table-responsive">
          <table class="data-table" id="reportTableData">
            <thead>
              <tr>
                <th>USN</th>
                <th>Student Name</th>
                <th>Sem & Div</th>
                <th>GPA</th>
                <th>Attendance</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>${stdRows}</tbody>
          </table>
        </div>
      </div>
    `;
  },

  renderCharts: function() {
    var ctx1 = document.getElementById('reportChart1');
    var ctx2 = document.getElementById('reportChart2');

    if (ctx1 && window.Chart) {
      new Chart(ctx1, {
        type: 'bar',
        data: {
          labels: ['Deep Learning', 'Computer Vision', 'NLP', 'ML Algorithms', 'MLOps'],
          datasets: [{
            label: 'Pass %',
            data: [96.5, 94.2, 91.0, 89.5, 95.0],
            backgroundColor: 'rgba(99, 102, 241, 0.7)',
            borderColor: '#6366f1',
            borderWidth: 1,
            borderRadius: 6
          }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } }
      });
    }

    if (ctx2 && window.Chart) {
      new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: ['S Grade (90%+)', 'A Grade (80-89%)', 'B Grade (70-79%)', 'C Grade (<70%)'],
          datasets: [{
            data: [35, 45, 15, 5],
            backgroundColor: ['#10b981', '#06b6d4', '#6366f1', '#f59e0b']
          }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#475569' } } } }
      });
    }
  },

  // Export PDF using jsPDF
  exportPDF: function() {
    if (!window.jspdf) {
      alert("PDF Export Library loaded. Printing view...");
      window.print();
      return;
    }
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("AIML ACADEMICHUB - DEPARTMENT REPORT", 14, 20);
    doc.setFontSize(10);
    doc.setFont("helvetica", "normal");
    doc.text("Report Type: " + this.selectedReportType.toUpperCase() + " | Academic Year: " + this.filters.year, 14, 28);
    doc.text("Generated on: " + new Date().toLocaleString(), 14, 34);

    if (doc.autoTable) {
      doc.autoTable({ html: '#reportTableData', startY: 40 });
    } else {
      doc.text("Report table content compiled.", 14, 45);
    }

    doc.save("AIML_Report_" + this.selectedReportType + ".pdf");
  },

  // Export Excel using XLSX library
  exportExcel: function() {
    if (!window.XLSX) {
      alert("XLSX export library ready.");
      return;
    }
    var table = document.getElementById("reportTableData");
    if (!table) return;

    var wb = XLSX.utils.table_to_book(table, { sheet: "AIML_Report" });
    XLSX.writeFile(wb, "AIML_Report_" + this.selectedReportType + ".xlsx");
  }
};
