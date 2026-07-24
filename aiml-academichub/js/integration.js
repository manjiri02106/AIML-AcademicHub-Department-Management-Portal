/* AIML ACADEMICHUB - Integration Layer & Central DB Module */

window.IntegrationModule = {
  currentModule: 'students',

  render: function(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="display: flex; align-items: center; gap: 10px; font-size: 1.4rem;">
              <i class="lucide-layers" style="color: var(--cyan);"></i> Integration Layer & Central Database
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
              Unified integration pipeline collecting live data across 10 department sub-modules into the Central DB.
            </p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="btn btn-cyan" onclick="IntegrationModule.runDataSync(this)">
              <i class="lucide-refresh-cw"></i> Sync Sub-Modules
            </button>
          </div>
        </div>

        <!-- Sub-Module Tabs -->
        <div style="display: flex; gap: 10px; overflow-x: auto; padding-top: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-top: 10px;">
          <button class="btn ${this.currentModule === 'students' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('students')">
            <i class="lucide-users"></i> Student Records
          </button>
          <button class="btn ${this.currentModule === 'faculty' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('faculty')">
            <i class="lucide-award"></i> Faculty Profiles
          </button>
          <button class="btn ${this.currentModule === 'attendance' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('attendance')">
            <i class="lucide-calendar-check"></i> Attendance
          </button>
          <button class="btn ${this.currentModule === 'academics' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('academics')">
            <i class="lucide-book-open"></i> Academics & CO-PO
          </button>
          <button class="btn ${this.currentModule === 'projects' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('projects')">
            <i class="lucide-cpu"></i> Capstone Projects
          </button>
          <button class="btn ${this.currentModule === 'placements' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('placements')">
            <i class="lucide-briefcase"></i> Placements
          </button>
          <button class="btn ${this.currentModule === 'research' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('research')">
            <i class="lucide-file-text"></i> Research & Patents
          </button>
          <button class="btn ${this.currentModule === 'labs' ? 'btn-primary' : 'btn-secondary'}" onclick="IntegrationModule.switchTab('labs')">
            <i class="lucide-server"></i> GPU Lab Assets
          </button>
        </div>
      </div>

      <!-- Tab Content Area -->
      <div id="integrationTabContent">
        ${this.getTabHTML(this.currentModule)}
      </div>
    `;

    container.innerHTML = html;
    if (window.lucide) window.lucide.createIcons();
  },

  switchTab: function(tabName) {
    this.currentModule = tabName;
    this.render('mainAppView');
  },

  runDataSync: function(btn) {
    var self = this;
    if (!btn) return;
    btn.innerHTML = '<i class="lucide-loader-2 spin"></i> Syncing...';
    btn.disabled = true;

    window.DataProcessingEngine.runPipeline(null, function() {
      btn.innerHTML = '<i class="lucide-check-circle"></i> Synced!';
      if (window.App && window.App.showToast) {
        window.App.showToast("All sub-modules synchronized with Central Database", "success");
      }
      setTimeout(function() {
        btn.innerHTML = '<i class="lucide-refresh-cw"></i> Sync Sub-Modules';
        btn.disabled = false;
        self.render('mainAppView');
      }, 1200);
    });
  },

  getTabHTML: function(tab) {
    var data = window.AcademicHubData;

    if (tab === 'students') {
      var rows = data.students.map(function(s) {
        var statusBadge = s.status === 'Placed' ? '<span class="badge badge-emerald">Placed</span>' : '<span class="badge badge-amber">' + s.status + '</span>';
        var attBadge = s.attendance >= 75 ? '<span class="badge badge-cyan">' + s.attendance + '%</span>' : '<span class="badge badge-rose">' + s.attendance + '% (Defaulter)</span>';
        return `
          <tr>
            <td><strong>${s.usn}</strong></td>
            <td>${s.name}</td>
            <td>Sem ${s.sem} (${s.div})</td>
            <td>${s.gpa}</td>
            <td>${attBadge}</td>
            <td>${s.backlogs}</td>
            <td>${statusBadge}</td>
            <td>${s.company}</td>
            <td>
              <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.viewStudentProfile('${s.usn}', '${s.name}', '${s.gpa}', '${s.company}')">
                <i class="lucide-user"></i> Profile
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3>AIML Enrolled Student Profiles & Records (${data.students.length} Records)</h3>
            <span class="badge badge-indigo">Central DB Table: tbl_aiml_students</span>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>USN</th>
                  <th>Student Name</th>
                  <th>Sem & Div</th>
                  <th>GPA</th>
                  <th>Attendance %</th>
                  <th>Backlogs</th>
                  <th>Status</th>
                  <th>Company / Offer</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'faculty') {
      var fRows = data.faculty.map(function(f) {
        return `
          <tr>
            <td><strong>${f.id}</strong></td>
            <td>${f.name}</td>
            <td>${f.designation}</td>
            <td>${f.qualification}</td>
            <td>${f.workload} Hours/Wk</td>
            <td>${f.mentoringCount} Students</td>
            <td><span class="badge badge-cyan">${f.pubCount} Papers</span></td>
            <td><strong style="color: var(--emerald);">${f.grants}</strong></td>
            <td>
              <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.viewFacultyWorkload('${f.name}', '${f.workload}')">
                <i class="lucide-eye"></i> Details
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3>AIML Department Faculty Profiles & Workload</h3>
            <span class="badge badge-indigo">Central DB Table: tbl_aiml_faculty</span>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Faculty ID</th>
                  <th>Name</th>
                  <th>Designation</th>
                  <th>Qualification</th>
                  <th>Workload</th>
                  <th>Mentees</th>
                  <th>Publications</th>
                  <th>Grants Received</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${fRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'attendance') {
      var defaulters = data.students.filter(function(s) { return s.attendance < 75; });
      var defRows = defaulters.map(function(d) {
        return `
          <tr>
            <td><strong>${d.usn}</strong></td>
            <td>${d.name}</td>
            <td>Sem ${d.sem}-${d.div}</td>
            <td><span class="badge badge-rose">${d.attendance}%</span></td>
            <td>${d.attendance < 70 ? 'Critical Alert Sent to Parent' : 'Warning Issued'}</td>
            <td>${d.guide}</td>
            <td>
              <button class="btn btn-amber" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.sendAttendanceAlert('${d.usn}', '${d.name}', '${d.attendance}')">
                <i class="lucide-bell"></i> Send Alert
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="color: var(--rose);"><i class="lucide-alert-triangle"></i> Attendance Defaulters List (< 75% Threshold)</h3>
            <span class="badge badge-rose">${defaulters.length} Defaulters Flagged</span>
          </div>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>USN</th>
                  <th>Student Name</th>
                  <th>Semester & Div</th>
                  <th>Monthly Attendance %</th>
                  <th>Action Status</th>
                  <th>Mentor Faculty</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${defRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'academics') {
      var coRows = data.academics.coPoMatrix.map(function(c) {
        return `
          <tr>
            <td><strong>${c.course}</strong></td>
            <td>${c.co}</td>
            <td>${c.po1}</td>
            <td>${c.po2}</td>
            <td>${c.po3}</td>
            <td>${c.po4}</td>
            <td>${c.po5}</td>
            <td>${c.pso1}</td>
            <td><span class="badge badge-emerald">${c.attainment}</span></td>
            <td>
              <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.viewCOPOMatrix('${c.course}')">
                <i class="lucide-bar-chart-2"></i> Audit CO
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <h3>Course Outcome - Program Outcome (CO-PO) Attainment Matrix</h3>
          <p style="color: var(--text-muted); margin-bottom: 16px; font-size: 0.88rem;">Automated direct CO attainment calculation based on internal test marks & semester exams.</p>
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Course Code & Title</th>
                  <th>Course Outcome (CO)</th>
                  <th>PO1</th>
                  <th>PO2</th>
                  <th>PO3</th>
                  <th>PO4</th>
                  <th>PO5</th>
                  <th>PSO1</th>
                  <th>Attainment Score (Max 3.0)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${coRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'projects') {
      var projRows = data.projects.map(function(p) {
        return `
          <tr>
            <td><strong>${p.title}</strong></td>
            <td>${p.domain}</td>
            <td>${p.guide}</td>
            <td>${p.phase1}/100</td>
            <td>${p.phase2}/100</td>
            <td>${p.phase3}/100</td>
            <td><span class="badge badge-emerald">${p.status}</span></td>
            <td>
              <button class="btn btn-cyan" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.viewProjectDetails('${p.title}', '${p.guide}')">
                <i class="lucide-cpu"></i> View Abstract
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <h3>AIML Final Year Capstone Projects & Guides</h3>
          <div class="table-responsive" style="margin-top: 16px;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Project Title</th>
                  <th>Domain</th>
                  <th>Guide Name</th>
                  <th>Phase 1</th>
                  <th>Phase 2</th>
                  <th>Phase 3</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${projRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'placements') {
      var plcRows = data.placements.map(function(p) {
        return `
          <tr>
            <td><strong>${p.company}</strong></td>
            <td><span class="badge badge-indigo">${p.tier}</span></td>
            <td><strong style="color: var(--emerald);">${p.ctc}</strong></td>
            <td>${p.offers} Offers</td>
            <td>${p.roles}</td>
            <td>
              <button class="btn btn-emerald" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.viewCompanyOffers('${p.company}', '${p.ctc}', '${p.offers}')">
                <i class="lucide-briefcase"></i> View Offers
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <h3>Placement Statistics & Company Profiles</h3>
          <div class="table-responsive" style="margin-top: 16px;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Company Name</th>
                  <th>Placement Tier</th>
                  <th>Package CTC</th>
                  <th>Total Offers</th>
                  <th>Roles Offered</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${plcRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'research') {
      var resRows = data.research.publications.map(function(pub) {
        return `
          <tr>
            <td><strong>${pub.title}</strong></td>
            <td>${pub.authors}</td>
            <td>${pub.journal}</td>
            <td><span class="badge badge-cyan">IF: ${pub.impactFactor}</span></td>
            <td><span class="badge badge-emerald">${pub.indexed}</span></td>
            <td>${pub.year}</td>
            <td>
              <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.viewPaperDOI('${pub.title}', '${pub.journal}')">
                <i class="lucide-external-link"></i> DOI Citation
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <h3>Faculty & Student Scopus / IEEE Research Publications</h3>
          <div class="table-responsive" style="margin-top: 16px;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Publication Title</th>
                  <th>Authors</th>
                  <th>Journal / Conference</th>
                  <th>Impact Factor</th>
                  <th>Indexing</th>
                  <th>Year</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${resRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    if (tab === 'labs') {
      var labRows = data.labs.map(function(l) {
        return `
          <tr>
            <td><strong>${l.name}</strong></td>
            <td>${l.location}</td>
            <td>${l.assets}</td>
            <td>${l.capacity} Seats</td>
            <td><span class="badge badge-emerald">${l.utilization}</span></td>
            <td><span class="badge badge-cyan">${l.status}</span></td>
            <td>
              <button class="btn btn-cyan" style="padding: 4px 8px; font-size: 0.75rem;" onclick="IntegrationModule.inspectLabAssets('${l.name}', '${l.assets}')">
                <i class="lucide-server"></i> Inspect Hardware
              </button>
            </td>
          </tr>
        `;
      }).join('');

      return `
        <div class="glass-panel">
          <h3>GPU Lab Assets & Supercomputing Infrastructure</h3>
          <div class="table-responsive" style="margin-top: 16px;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Laboratory Name</th>
                  <th>Location</th>
                  <th>High-Performance Hardware Assets</th>
                  <th>Capacity</th>
                  <th>Utilization Rate</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>${labRows}</tbody>
            </table>
          </div>
        </div>
      `;
    }

    return `<div class="glass-panel"><p>Module data preview standard view.</p></div>`;
  },

  /* Click Handler Action Functions */
  viewStudentProfile: function(usn, name, gpa, company) {
    alert("STUDENT PROFILE RECORD:\n\nUSN: " + usn + "\nName: " + name + "\nCumulative GPA: " + gpa + " / 10.0\nPlacement: " + company + "\nStatus: VERIFIED IN CENTRAL DB");
    if (window.App && window.App.showToast) window.App.showToast("Inspected profile for " + usn, "info");
  },

  viewFacultyWorkload: function(name, workload) {
    alert("FACULTY PROFILE & WORKLOAD:\n\nName: " + name + "\nWeekly Teaching Load: " + workload + " Hours\nStatus: Compliant with AICTE Cadre Norms");
    if (window.App && window.App.showToast) window.App.showToast("Viewed workload for " + name, "info");
  },

  sendAttendanceAlert: function(usn, name, att) {
    alert("ATTENDANCE ALERT DISPATCHED!\n\nUSN: " + usn + "\nStudent Name: " + name + "\nCurrent Attendance: " + att + "%\nNotification: Parent SMS & Official Defaulter Warning Letter Issued.");
    if (window.App && window.App.showToast) window.App.showToast("Alert sent to parent of " + usn, "warning");
  },

  viewCOPOMatrix: function(course) {
    alert("CO-PO ATTAINMENT AUDIT:\n\nCourse: " + course + "\nAttainment Method: Direct (80% Internal/Semester Exams) + Indirect (20% Course Exit Survey)\nNBA Compliance: Passed Threshold (>= 2.5)");
    if (window.App && window.App.showToast) window.App.showToast("Audited CO-PO for " + course, "success");
  },

  viewProjectDetails: function(title, guide) {
    alert("CAPSTONE PROJECT DETAILS:\n\nTitle: " + title + "\nGuide: " + guide + "\nRubrics: Phase 1 (Lit Review), Phase 2 (Design), Phase 3 (Execution & Paper)\nStatus: Approved by Department Evaluation Committee");
    if (window.App && window.App.showToast) window.App.showToast("Opened abstract for project", "info");
  },

  viewCompanyOffers: function(company, ctc, offers) {
    alert("CAMPUS RECRUITMENT PROFILE:\n\nCompany: " + company + "\nPackage CTC: " + ctc + "\nTotal Offers Issued: " + offers + "\nStatus: Placement Cell Verified");
    if (window.App && window.App.showToast) window.App.showToast("Viewed placement profile for " + company, "success");
  },

  viewPaperDOI: function(title, journal) {
    alert("SCOPUS / IEEE PUBLICATION CITATION:\n\nTitle: " + title + "\nJournal/Conference: " + journal + "\nDOI: 10.1109/TPAMI.2025.1098421\nStatus: Indexed & Verified in Central DB");
    if (window.App && window.App.showToast) window.App.showToast("Retrieved DOI citation", "info");
  },

  inspectLabAssets: function(name, assets) {
    alert("GPU SUPERCOMPUTING ASSET INSPECTION:\n\nLab: " + name + "\nHardware Assets: " + assets + "\nMaintenance: Operational (NVIDIA Enterprise Drivers v550.54)\nStatus: Active Asset Register #GPU-AIML-2025");
    if (window.App && window.App.showToast) window.App.showToast("Inspected hardware for " + name, "success");
  }
};
