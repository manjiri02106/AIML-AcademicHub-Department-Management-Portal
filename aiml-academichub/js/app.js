/* AIML ACADEMICHUB - Main App Controller & Router */

window.App = {
  currentRole: 'HOD', // Roles: 'HOD', 'Administrator', 'IQAC', 'Student'
  activeView: 'dashboard',

  init: function() {
    this.bindEvents();
    this.renderView('dashboard');
  },

  setRole: function(role) {
    this.currentRole = role;
    var display = document.getElementById('currentRoleDisplay');
    if (display) display.innerText = role;
    var select = document.getElementById('phpRoleSelect');
    if (select) select.value = role;

    // Show toast message for role change
    this.showToast("Switched active view to " + role + " Portal", "info");

    // Re-render current view with the new role's dashboard configuration
    this.renderView(this.activeView);
  },

  renderView: function(viewName) {
    this.activeView = viewName;
    
    // Update sidebar navigation active state
    var items = document.querySelectorAll('.nav-item');
    items.forEach(function(el) {
      if (el.getAttribute('data-view') === viewName) {
        el.classList.add('active');
      } else {
        el.classList.remove('active');
      }
    });

    var container = document.getElementById('mainAppView');
    if (!container) return;

    if (viewName === 'dashboard') {
      if (this.currentRole === 'Administrator') {
        this.renderAdminDashboard(container);
      } else if (this.currentRole === 'IQAC') {
        this.renderIQACDashboard(container);
      } else if (this.currentRole === 'Student') {
        this.renderStudentDashboard(container);
      } else {
        this.renderHODDashboard(container);
      }
    } else if (viewName === 'integration') {
      window.IntegrationModule.render('mainAppView');
    } else if (viewName === 'processing') {
      this.renderProcessing(container);
    } else if (viewName === 'reports') {
      window.ReportsEngine.render('mainAppView');
    } else if (viewName === 'accreditation') {
      window.AccreditationEngine.render('mainAppView');
    }

    if (window.lucide) window.lucide.createIcons();
  },

  /* ------------------------------------------------------------------------
     1. STUDENT DASHBOARD (Student Academic Portal)
  ------------------------------------------------------------------------ */
  renderStudentDashboard: function(container) {
    var student = window.AcademicHubData.students[0]; // Student (USN: 1VA21AI001)

    var html = `
      <!-- Student Profile Header Banner -->
      <div class="glass-panel" style="margin-bottom: 24px; border-left: 4px solid var(--cyan);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--cyan), var(--primary)); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; font-weight: bold;">
              ST
            </div>
            <div>
              <h2 style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
                Student Portal <span class="badge badge-cyan" style="font-size: 0.8rem;">${student.usn}</span>
              </h2>
              <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 2px;">
                Department of AIML • Semester ${student.sem} (Division ${student.div}) • Mentor: ${student.guide}
              </p>
            </div>
          </div>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="btn btn-cyan" onclick="App.downloadStudentTranscript()">
              <i class="lucide-file-text"></i> Download Grade Card PDF
            </button>
            <button class="btn btn-emerald" onclick="App.viewPlacementOfferLetter()">
              <i class="lucide-briefcase"></i> View Offer Letter
            </button>
          </div>
        </div>
      </div>

      <!-- Student KPI Cards -->
      <div class="stat-grid">
        <div class="stat-card cyan">
          <div class="stat-info">
            <p>Overall Cumulative GPA</p>
            <h3>${student.gpa} / 10.0</h3>
          </div>
          <div class="stat-icon" style="color: var(--cyan);"><i class="lucide-award"></i></div>
        </div>

        <div class="stat-card emerald">
          <div class="stat-info">
            <p>Monthly Attendance Rate</p>
            <h3>${student.attendance}%</h3>
          </div>
          <div class="stat-icon" style="color: var(--emerald);"><i class="lucide-calendar-check"></i></div>
        </div>

        <div class="stat-card indigo">
          <div class="stat-info">
            <p>Placement Status</p>
            <h3 style="font-size: 1.25rem;">NVIDIA (₹44 LPA)</h3>
          </div>
          <div class="stat-icon" style="color: var(--primary);"><i class="lucide-briefcase"></i></div>
        </div>

        <div class="stat-card amber">
          <div class="stat-info">
            <p>Capstone Review Score</p>
            <h3>98 / 100</h3>
          </div>
          <div class="stat-icon" style="color: var(--amber);"><i class="lucide-cpu"></i></div>
        </div>
      </div>

      <!-- Quick Student Actions Toolbar -->
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
          <h3><i class="lucide-zap" style="color: var(--amber);"></i> Student Quick Actions</h3>
          <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button class="btn btn-primary" onclick="App.submitStudentProjectReport()">
              <i class="lucide-upload-cloud"></i> Submit Capstone Phase 3 Report
            </button>
            <button class="btn btn-secondary" onclick="App.viewSubjectAttendanceModal()">
              <i class="lucide-list-checks"></i> Subject Attendance Breakdown
            </button>
            <button class="btn btn-secondary" onclick="App.requestMentorMeeting()">
              <i class="lucide-message-square"></i> Book Mentor Session
            </button>
          </div>
        </div>
      </div>

      <!-- Enrolled Courses & CO-PO Attainment Progress -->
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h3><i class="lucide-book-open" style="color: var(--cyan);"></i> Semester 7 Enrolled Courses & Attainment</h3>
          <span class="badge badge-emerald">5 Active Courses</span>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Instructor</th>
                <th>Internal Test 1</th>
                <th>Internal Test 2</th>
                <th>Attendance</th>
                <th>CO Attainment Status</th>
              </tr>
            </thead>
            <tbody>
              <tr><td><strong>21AI71</strong></td><td>Deep Learning Architectures</td><td>Dr. Geoffrey Hinton</td><td>48/50</td><td>46/50</td><td><span class="badge badge-emerald">94%</span></td><td><span class="badge badge-indigo">Target Achieved (2.85/3)</span></td></tr>
              <tr><td><strong>21AI72</strong></td><td>Computer Vision & Video Analytics</td><td>Dr. Sarah Jenkins</td><td>49/50</td><td>48/50</td><td><span class="badge badge-emerald">92%</span></td><td><span class="badge badge-indigo">Target Achieved (2.90/3)</span></td></tr>
              <tr><td><strong>21AI73</strong></td><td>Natural Language Processing & LLMs</td><td>Prof. Alan Turing</td><td>44/50</td><td>45/50</td><td><span class="badge badge-emerald">90%</span></td><td><span class="badge badge-indigo">Target Achieved (2.68/3)</span></td></tr>
              <tr><td><strong>21AI74</strong></td><td>MLOps & Cloud Infrastructure</td><td>Dr. Fei-Fei Li</td><td>47/50</td><td>49/50</td><td><span class="badge badge-emerald">95%</span></td><td><span class="badge badge-indigo">Target Achieved (2.95/3)</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- My Capstone Project Overview -->
      <div class="glass-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h3><i class="lucide-cpu" style="color: var(--primary);"></i> Final Year Capstone Project Portal</h3>
          <span class="badge badge-emerald">Approved & Verified</span>
        </div>
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); padding: 20px; border-radius: 12px;">
          <h4 style="font-size: 1.1rem; color: #fff;">${student.project}</h4>
          <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 6px;">
            Guide: <strong>${student.guide}</strong> | Domain: <strong>Computer Vision & Edge AI</strong> | Team: Aarav Sharma (1VA21AI001), Priya Sundaram (1VA21AI006)
          </p>
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 16px;">
            <div style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: 8px;">
              <span style="font-size: 0.78rem; color: var(--text-muted);">Phase 1 Evaluation</span>
              <h4 style="color: var(--emerald); font-size: 1.2rem;">95 / 100</h4>
            </div>
            <div style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: 8px;">
              <span style="font-size: 0.78rem; color: var(--text-muted);">Phase 2 Evaluation</span>
              <h4 style="color: var(--cyan); font-size: 1.2rem;">94 / 100</h4>
            </div>
            <div style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: 8px;">
              <span style="font-size: 0.78rem; color: var(--text-muted);">Phase 3 Final Evaluation</span>
              <h4 style="color: var(--amber); font-size: 1.2rem;">98 / 100</h4>
            </div>
          </div>
        </div>
      </div>
    `;

    container.innerHTML = html;
  },

  /* ------------------------------------------------------------------------
     2. HOD DASHBOARD (Head of Department View)
  ------------------------------------------------------------------------ */
  renderHODDashboard: function(container) {
    var data = window.AcademicHubData;
    var analytics = window.DataProcessingEngine.calculateAnalytics();

    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
              <i class="lucide-layout-dashboard" style="color: var(--primary);"></i> Head of Department (HOD) Executive Control Center
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
              Department overview: Faculty workload, student performance, placement statistics, and pending approvals.
            </p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="btn btn-primary" onclick="App.triggerHODNotice()">
              <i class="lucide-bell"></i> Issue Defaulters Notice
            </button>
            <button class="btn btn-emerald" onclick="window.ReportsEngine.exportPDF()">
              <i class="lucide-file-down"></i> HOD Summary PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Top KPI Stats Row -->
      <div class="stat-grid">
        <div class="stat-card cyan">
          <div class="stat-info">
            <p>Enrolled Students</p>
            <h3>${data.department.totalStudents}</h3>
          </div>
          <div class="stat-icon" style="color: var(--cyan);"><i class="lucide-users"></i></div>
        </div>

        <div class="stat-card emerald">
          <div class="stat-info">
            <p>Placement Offer Rate</p>
            <h3>${analytics.placementPercentage}%</h3>
          </div>
          <div class="stat-icon" style="color: var(--emerald);"><i class="lucide-briefcase"></i></div>
        </div>

        <div class="stat-card amber">
          <div class="stat-info">
            <p>Attendance Defaulters (<75%)</p>
            <h3 style="-webkit-text-fill-color: var(--rose);">${analytics.defaulterCount} Students</h3>
          </div>
          <div class="stat-icon" style="color: var(--rose);"><i class="lucide-alert-triangle"></i></div>
        </div>

        <div class="stat-card rose">
          <div class="stat-info">
            <p>IEEE / Scopus Papers</p>
            <h3>${analytics.totalPublications} Papers</h3>
          </div>
          <div class="stat-icon" style="color: var(--purple);"><i class="lucide-file-text"></i></div>
        </div>
      </div>

      <!-- Pending HOD Approvals Section -->
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h3><i class="lucide-clock" style="color: var(--amber);"></i> Pending HOD Approvals & Submissions</h3>
          <span class="badge badge-amber">3 Actions Pending</span>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Request Type</th>
                <th>Submitted By</th>
                <th>Details</th>
                <th>Submission Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><strong>Capstone Project Topic Revision</strong></td>
                <td>Prof. Andrew Ng</td>
                <td>Reinforcement Learning for Autonomous Drone Navigation</td>
                <td>2025-02-18</td>
                <td>
                  <button class="btn btn-emerald" style="padding: 4px 10px; font-size: 0.78rem;" onclick="App.approveRequest(this, 'Project Revision')"><i class="lucide-check"></i> Approve</button>
                  <button class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.78rem;" onclick="App.rejectRequest(this)"><i class="lucide-x"></i> Reject</button>
                </td>
              </tr>
              <tr>
                <td><strong>FDP Conference Grant Request</strong></td>
                <td>Dr. Fei-Fei Li</td>
                <td>NeurIPS 2025 Registration (₹45,000)</td>
                <td>2025-02-17</td>
                <td>
                  <button class="btn btn-emerald" style="padding: 4px 10px; font-size: 0.78rem;" onclick="App.approveRequest(this, 'FDP Grant')"><i class="lucide-check"></i> Approve</button>
                  <button class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.78rem;" onclick="App.rejectRequest(this)"><i class="lucide-x"></i> Reject</button>
                </td>
              </tr>
              <tr>
                <td><strong>GPU Server Maintenance Request</strong></td>
                <td>Dr. Sarah Jenkins</td>
                <td>NVIDIA H100 Node 2 Thermal Repasting</td>
                <td>2025-02-15</td>
                <td>
                  <button class="btn btn-emerald" style="padding: 4px 10px; font-size: 0.78rem;" onclick="App.approveRequest(this, 'GPU Repair')"><i class="lucide-check"></i> Approve</button>
                  <button class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.78rem;" onclick="App.rejectRequest(this)"><i class="lucide-x"></i> Reject</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Charts Grid -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
        <div class="glass-panel">
          <h3 style="font-size: 1.05rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="lucide-trending-up" style="color: var(--emerald);"></i> Academic Pass % & Attendance Trends
          </h3>
          <canvas id="dashChartPassAtt" height="180"></canvas>
        </div>

        <div class="glass-panel">
          <h3 style="font-size: 1.05rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="lucide-target" style="color: var(--primary);"></i> CO-PO Attainment Radar Matrix
          </h3>
          <canvas id="dashChartCopAttainment" height="180"></canvas>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <div class="glass-panel">
          <h3 style="font-size: 1.05rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="lucide-pie-chart" style="color: var(--cyan);"></i> Placement Package Tiers (LPA)
          </h3>
          <canvas id="dashChartPlacementTier" height="180"></canvas>
        </div>

        <div class="glass-panel">
          <h3 style="font-size: 1.05rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="lucide-award" style="color: var(--amber);"></i> Research Publications & Patents Growth
          </h3>
          <canvas id="dashChartResearch" height="180"></canvas>
        </div>
      </div>
    `;

    container.innerHTML = html;
    setTimeout(function() { window.ChartEngine.renderDashboardCharts(); }, 100);
  },

  /* ------------------------------------------------------------------------
     3. ADMINISTRATOR DASHBOARD (System Admin View)
  ------------------------------------------------------------------------ */
  renderAdminDashboard: function(container) {
    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
              <i class="lucide-shield-alert" style="color: var(--cyan);"></i> System Administrator Integration & Telemetry Control
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
              Database connections, API webhooks, sub-module sync health, and system user access governance.
            </p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="btn btn-cyan" onclick="App.runAdminBackup()">
              <i class="lucide-database"></i> Trigger System Backup
            </button>
            <button class="btn btn-secondary" onclick="App.purgeSystemCache()">
              <i class="lucide-trash-2"></i> Purge System Cache
            </button>
          </div>
        </div>
      </div>

      <!-- System Health Cards -->
      <div class="stat-grid">
        <div class="stat-card emerald">
          <div class="stat-info">
            <p>Central DB Status</p>
            <h3>100% ONLINE</h3>
          </div>
          <div class="stat-icon" style="color: var(--emerald);"><i class="lucide-database"></i></div>
        </div>

        <div class="stat-card cyan">
          <div class="stat-info">
            <p>Sub-Modules Integrated</p>
            <h3>10 / 10 Active</h3>
          </div>
          <div class="stat-icon" style="color: var(--cyan);"><i class="lucide-layers"></i></div>
        </div>

        <div class="stat-card indigo">
          <div class="stat-info">
            <p>API Processing Latency</p>
            <h3>14 ms</h3>
          </div>
          <div class="stat-icon" style="color: var(--primary);"><i class="lucide-activity"></i></div>
        </div>

        <div class="stat-card amber">
          <div class="stat-info">
            <p>Active User Sessions</p>
            <h3>18 Logged In</h3>
          </div>
          <div class="stat-icon" style="color: var(--amber);"><i class="lucide-user-check"></i></div>
        </div>
      </div>

      <!-- Sub-Module Integration Telemetry Table -->
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h3><i class="lucide-server" style="color: var(--cyan);"></i> Integrated Sub-Modules Telemetry & Record Counts</h3>
          <span class="badge badge-emerald">Sync Pipeline Healthy</span>
        </div>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Sub-Module Name</th>
                <th>Target DB Table</th>
                <th>Record Count</th>
                <th>Sync Status</th>
                <th>Last Synced Timestamp</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>Student Management</td><td><code>tbl_aiml_students</code></td><td>240 Records</td><td><span class="badge badge-emerald">Connected</span></td><td>Just Now</td><td><button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="App.reSyncModule('Student Records')"><i class="lucide-refresh-cw"></i> Re-Sync</button></td></tr>
              <tr><td>Faculty Profiles & Workload</td><td><code>tbl_aiml_faculty</code></td><td>14 Profiles</td><td><span class="badge badge-emerald">Connected</span></td><td>2 mins ago</td><td><button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="App.reSyncModule('Faculty Profiles')"><i class="lucide-refresh-cw"></i> Re-Sync</button></td></tr>
              <tr><td>Daily & Monthly Attendance</td><td><code>tbl_aiml_attendance</code></td><td>4,800 Logs</td><td><span class="badge badge-emerald">Connected</span></td><td>5 mins ago</td><td><button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="App.reSyncModule('Attendance')"><i class="lucide-refresh-cw"></i> Re-Sync</button></td></tr>
              <tr><td>Academics & CO-PO Attainment</td><td><code>tbl_aiml_co_po</code></td><td>32 Matrices</td><td><span class="badge badge-emerald">Connected</span></td><td>10 mins ago</td><td><button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="App.reSyncModule('Academics')"><i class="lucide-refresh-cw"></i> Re-Sync</button></td></tr>
              <tr><td>Capstone Projects & Reviews</td><td><code>tbl_aiml_projects</code></td><td>60 Projects</td><td><span class="badge badge-emerald">Connected</span></td><td>15 mins ago</td><td><button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="App.reSyncModule('Capstone Projects')"><i class="lucide-refresh-cw"></i> Re-Sync</button></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    `;

    container.innerHTML = html;
  },

  /* ------------------------------------------------------------------------
     4. IQAC / NBA COORDINATOR DASHBOARD (Accreditation Quality View)
  ------------------------------------------------------------------------ */
  renderIQACDashboard: function(container) {
    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
              <i class="lucide-award" style="color: var(--amber);"></i> IQAC & NBA Accreditation Quality Control Dashboard
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
              Self-Assessment Report (SAR) & Self-Study Report (SSR) compliance status for NBA Tier-I & NAAC.
            </p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="btn btn-amber" onclick="window.AccreditationEngine.collectDataAuto(this)">
              <i class="lucide-zap"></i> Harvest Central DB Data
            </button>
            <button class="btn btn-primary" onclick="window.AccreditationEngine.generateAccreditationReport()">
              <i class="lucide-file-check"></i> Compile SAR Report
            </button>
          </div>
        </div>
      </div>

      <!-- Accreditation Summary Banner -->
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <div class="glass-panel" style="border-left: 4px solid var(--emerald);">
          <p style="color: var(--text-muted); font-size: 0.85rem;">NBA Accreditation Score</p>
          <h2 style="font-size: 1.8rem; color: var(--emerald); margin-top: 4px;">785 / 1000 Pts</h2>
          <span class="badge badge-emerald" style="margin-top: 8px;">Tier-I Accredited (6 Years)</span>
        </div>

        <div class="glass-panel" style="border-left: 4px solid var(--cyan);">
          <p style="color: var(--text-muted); font-size: 0.85rem;">NAAC RAF CGPA Score</p>
          <h2 style="font-size: 1.8rem; color: var(--cyan); margin-top: 4px;">3.78 CGPA</h2>
          <span class="badge badge-cyan" style="margin-top: 8px;">Grade A++ Compliant</span>
        </div>

        <div class="glass-panel" style="border-left: 4px solid var(--amber);">
          <p style="color: var(--text-muted); font-size: 0.85rem;">Supporting Proof Documents</p>
          <h2 style="font-size: 1.8rem; color: var(--amber); margin-top: 4px;">4 / 4 Verified</h2>
          <span class="badge badge-amber" style="margin-top: 8px;">100% Audit Ready</span>
        </div>
      </div>
    `;

    container.innerHTML = html;
  },

  /* ------------------------------------------------------------------------
     Student Interactive Actions
  ------------------------------------------------------------------------ */
  downloadStudentTranscript: function() {
    if (window.jspdf) {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      doc.setFont("helvetica", "bold");
      doc.setFontSize(16);
      doc.text("OFFICIAL ACADEMIC TRANSCRIPT - DEPT OF AIML", 14, 20);
      doc.setFontSize(10);
      doc.setFont("helvetica", "normal");
      doc.text("Student Portal | USN: 1VA21AI001 | Semester: 7", 14, 28);
      doc.text("CGPA: 9.42 / 10.0 | Branch: AIML | Status: PASS (0 Backlogs)", 14, 34);
      doc.text("Placement Status: PLACED at NVIDIA (CTC: Rs. 44.0 LPA)", 14, 40);
      doc.save("Student_Official_Transcript_1VA21AI001.pdf");
      this.showToast("Downloaded Official Transcript PDF", "success");
    } else {
      alert("Official Grade Transcript generated for Student (USN: 1VA21AI001).\nCumulative GPA: 9.42 / 10.0");
    }
  },

  viewPlacementOfferLetter: function() {
    alert("OFFICIAL CAMPUS PLACEMENT OFFER LETTER\n\nCompany: NVIDIA Graphics Pvt Ltd\nRole: AI Systems Engineer\nPackage CTC: Rs. 44,00,000 / Annum (₹44 LPA)\nJoining Location: Bengaluru R&D Center\nStatus: VERIFIED BY PLACEMENT CELL");
  },

  submitStudentProjectReport: function() {
    var file = prompt("Enter Capstone Phase 3 Report File Name (e.g. RealTime_EdgeVision_TensorRT_Final.pdf):");
    if (file) {
      alert("Capstone Project Phase 3 Report '" + file + "' successfully uploaded!\nGuide Dr. Sarah Jenkins has been notified for final review.");
      this.showToast("Uploaded Capstone Phase 3 Report", "success");
    }
  },

  viewSubjectAttendanceModal: function() {
    alert("SUBJECT-WISE ATTENDANCE BREAKDOWN (Semester 7):\n\n- 21AI71 Deep Learning: 94%\n- 21AI72 Computer Vision: 92%\n- 21AI73 Natural Language Processing: 90%\n- 21AI74 MLOps & Cloud: 95%\n\nOverall Attendance: 92% (No Defaulter Warning - Good Standing)");
  },

  requestMentorMeeting: function() {
    var date = prompt("Propose date for mentorship session with Dr. Sarah Jenkins (e.g. 2025-02-25 at 03:00 PM):");
    if (date) {
      alert("Mentorship meeting request sent to Dr. Sarah Jenkins for " + date + ".");
      this.showToast("Mentorship session request sent", "success");
    }
  },

  /* ------------------------------------------------------------------------
     HOD & Admin Actions
  ------------------------------------------------------------------------ */
  triggerHODNotice: function() {
    alert("Official HOD Notice Broadcasted!\n\nDefaulter warnings dispatched to parents of 14 students with < 75% attendance via SMS & Email portal.");
    this.showToast("Defaulters notice broadcasted to 14 parents", "success");
  },

  approveRequest: function(btn, type) {
    var row = btn.closest('tr');
    if (row) {
      row.style.opacity = '0.4';
      btn.parentNode.innerHTML = '<span class="badge badge-emerald"><i class="lucide-check-circle"></i> Approved</span>';
    }
    this.showToast("Approved request: " + type, "success");
  },

  rejectRequest: function(btn) {
    var row = btn.closest('tr');
    if (row) {
      row.style.opacity = '0.4';
      btn.parentNode.innerHTML = '<span class="badge badge-rose"><i class="lucide-x-circle"></i> Rejected</span>';
    }
    this.showToast("Rejected request submission", "warning");
  },

  runAdminBackup: function() {
    alert("System Backup Triggered!\n\nAll 10 Central DB tables backed up cleanly to encrypted cloud storage.\nFile: AIML_CentralDB_Backup_20250220.sql.gz (142 MB)");
    this.showToast("Database backup created successfully", "success");
  },

  purgeSystemCache: function() {
    alert("System cache purged cleanly!\n\nAll temporary queries and cached CO-PO vectors cleared.");
    this.showToast("System cache cleared", "info");
  },

  reSyncModule: function(modName) {
    this.showToast("Re-syncing " + modName + " table...", "info");
    setTimeout(function() {
      alert("Sub-module '" + modName + "' synced with Central DB!");
    }, 500);
  },

  showToast: function(msg, type) {
    var toast = document.createElement('div');
    toast.className = 'toast-notification ' + (type || 'info');
    toast.innerHTML = `<i class="lucide-info"></i> ${msg}`;
    toast.style.cssText = "position: fixed; bottom: 24px; right: 24px; background: rgba(15,23,42,0.95); color: #fff; border: 1px solid var(--primary); padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 0.88rem; box-shadow: var(--shadow-md); z-index: 9999; display: flex; align-items: center; gap: 8px; animation: slideUp 0.3s ease;";
    document.body.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 3000);
  },

  renderProcessing: function(container) {
    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <h2 style="font-size: 1.4rem; display: flex; align-items: center; gap: 10px;">
          <i class="lucide-cpu" style="color: var(--cyan);"></i> Data Processing Engine & Analytics Compiler
        </h2>
        <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
          Executes data validation, deduplication, module merging, and attainment analytics calculation.
        </p>
      </div>

      <div class="glass-panel">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
          <h3>Data Processing Engine Pipeline</h3>
          <button class="btn btn-primary" onclick="App.triggerProcessingRun()">
            <i class="lucide-play"></i> Execute Full Data Processing Pipeline
          </button>
        </div>

        <div id="processingLogArea" style="background: rgba(0,0,0,0.4); border: 1px solid var(--border-color); padding: 20px; border-radius: 12px; font-family: monospace; font-size: 0.88rem; min-height: 240px; color: var(--text-muted);">
          <p style="color: var(--cyan);">[Ready] Central Database Engine connected. Select 'Execute' to run pipeline.</p>
        </div>
      </div>
    `;
    container.innerHTML = html;
  },

  triggerProcessingRun: function() {
    var logArea = document.getElementById('processingLogArea');
    logArea.innerHTML = '<p style="color: var(--amber);">[Processing] Pipeline initiated...</p>';

    window.DataProcessingEngine.runPipeline(
      function(step, total, name) {
        logArea.innerHTML += `<p style="color: var(--cyan);">[Step ${step}/${total}] ${name}</p>`;
      },
      function(results) {
        logArea.innerHTML += `<p style="color: var(--emerald); font-weight: bold; margin-top: 12px;">✔ DATA PIPELINE COMPLETED SUCCESSFULLY!</p>`;
        logArea.innerHTML += `<p style="color: #fff;">- Pass Rate Computed: ${results.passPercentage}%</p>`;
        logArea.innerHTML += `<p style="color: #fff;">- Attendance Defaulters Mapped: ${results.defaulterCount} Students</p>`;
        logArea.innerHTML += `<p style="color: #fff;">- NBA Attainment Score Calculated: ${results.nbaScore}</p>`;
      }
    );
  },

  bindEvents: function() {
    var self = this;
    window.addEventListener('hashchange', function() {
      var view = window.location.hash.replace('#', '') || 'dashboard';
      self.renderView(view);
    });
  }
};

document.addEventListener('DOMContentLoaded', function() {
  window.App.init();
});
