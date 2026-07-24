/* AIML ACADEMICHUB - Accreditation Module Engine */

window.AccreditationEngine = {
  selectedBody: 'NBA',
  activeCriterion: 1,

  render: function(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var data = window.AcademicHubData.accreditation;

    var html = `
      <div class="glass-panel" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h2 style="display: flex; align-items: center; gap: 10px; font-size: 1.4rem;">
              <i class="lucide-award" style="color: var(--amber);"></i> Accreditation & Regulatory Compliance Engine
            </h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 4px;">
              Automated data aggregation from Central DB for NBA (SAR), NAAC (SSR), AICTE, and University Audits.
            </p>
          </div>
          <div style="display: flex; gap: 10px;">
            <button class="btn btn-amber" onclick="AccreditationEngine.collectDataAuto(this)">
              <i class="lucide-zap"></i> Collect Data Automatically
            </button>
            <button class="btn btn-primary" onclick="AccreditationEngine.generateAccreditationReport()">
              <i class="lucide-file-check"></i> Compile SAR / SSR Report
            </button>
          </div>
        </div>

        <!-- Accreditation Body Selector -->
        <div style="display: flex; gap: 12px; margin-top: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 14px;">
          <button class="btn ${this.selectedBody === 'NBA' ? 'btn-primary' : 'btn-secondary'}" onclick="AccreditationEngine.selectBody('NBA')">
            <i class="lucide-shield-check"></i> NBA Tier-I SAR (Score: 785/1000)
          </button>
          <button class="btn ${this.selectedBody === 'NAAC' ? 'btn-primary' : 'btn-secondary'}" onclick="AccreditationEngine.selectBody('NAAC')">
            <i class="lucide-star"></i> NAAC SSR RAF (Grade: A++ CGPA 3.78)
          </button>
          <button class="btn ${this.selectedBody === 'AICTE' ? 'btn-primary' : 'btn-secondary'}" onclick="AccreditationEngine.selectBody('AICTE')">
            <i class="lucide-building-2"></i> AICTE CII Industry Survey
          </button>
          <button class="btn ${this.selectedBody === 'UNIV' ? 'btn-primary' : 'btn-secondary'}" onclick="AccreditationEngine.selectBody('UNIV')">
            <i class="lucide-graduation-cap"></i> University Academic Audit
          </button>
        </div>
      </div>

      <!-- NBA/NAAC Criteria Grid & Evidence Viewer -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        
        <!-- Criteria List Panel -->
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3>${this.selectedBody} Criteria Evaluation</h3>
            <span class="badge badge-emerald">100% Data Synced</span>
          </div>

          <div style="display: flex; flex-direction: column; gap: 12px;">
            ${this.getCriteriaListHTML()}
          </div>
        </div>

        <!-- Supporting Evidence & Document Audit -->
        <div class="glass-panel">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3><i class="lucide-file-text" style="color: var(--cyan);"></i> Supporting Evidence Documents</h3>
            <button class="btn btn-secondary" onclick="AccreditationEngine.uploadDocumentModal()">
              <i class="lucide-upload-cloud"></i> Upload Proof
            </button>
          </div>
          
          <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 16px;">
            Evidence files automatically linked to criteria (PDFs, MoUs, Certificates, Lab Asset Registers).
          </p>

          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Doc ID</th>
                  <th>Criterion</th>
                  <th>Document Name</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                ${this.getEvidencesRowsHTML()}
              </tbody>
            </table>
          </div>
        </div>

      </div>
    `;

    container.innerHTML = html;
    if (window.lucide) window.lucide.createIcons();
  },

  selectBody: function(body) {
    this.selectedBody = body;
    this.render('mainAppView');
  },

  selectCriterion: function(critId) {
    this.activeCriterion = critId;
    this.render('mainAppView');
  },

  getCriteriaListHTML: function() {
    var self = this;
    var criteria = window.AcademicHubData.accreditation.nba.criteria;

    return criteria.map(function(c) {
      var isActive = self.activeCriterion === c.id;
      var pct = Math.round((c.obtained / c.maxMarks) * 100);
      return `
        <div style="background: ${isActive ? 'rgba(99, 102, 241, 0.12)' : 'rgba(255,255,255,0.02)'}; border: 1px solid ${isActive ? 'var(--primary)' : 'var(--border-color)'}; padding: 16px; border-radius: 12px; cursor: pointer;" onclick="AccreditationEngine.selectCriterion(${c.id})">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <strong style="font-size: 0.95rem;">Criterion ${c.id}: ${c.title}</strong>
            <span class="badge badge-emerald">${c.obtained} / ${c.maxMarks} Pts (${pct}%)</span>
          </div>
          <p style="color: var(--text-muted); font-size: 0.82rem; margin-top: 6px;">${c.details}</p>
          <div style="margin-top: 10px; background: rgba(255,255,255,0.05); border-radius: 999px; height: 6px; overflow: hidden;">
            <div style="width: ${pct}%; background: linear-gradient(90deg, var(--primary), var(--cyan)); height: 100%;"></div>
          </div>
        </div>
      `;
    }).join('');
  },

  getEvidencesRowsHTML: function() {
    var data = window.AcademicHubData.accreditation.nba.evidences;

    return data.map(function(ev) {
      return `
        <tr>
          <td><strong>${ev.id}</strong></td>
          <td><span class="badge badge-indigo">${ev.criterion}</span></td>
          <td>${ev.docName} <br><small style="color: var(--text-muted);">${ev.size} • Uploaded by ${ev.uploadedBy}</small></td>
          <td><span class="badge badge-emerald">${ev.status}</span></td>
          <td>
            <button class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.75rem;" onclick="alert('Viewing evidence proof document: ${ev.docName}')">
              <i class="lucide-eye"></i> View
            </button>
          </td>
        </tr>
      `;
    }).join('');
  },

  collectDataAuto: function(btn) {
    if (!btn) return;
    btn.innerHTML = '<i class="lucide-loader-2 spin"></i> Auto Extracting...';
    btn.disabled = true;

    setTimeout(function() {
      btn.innerHTML = '<i class="lucide-check-circle"></i> 100% Data Harvested!';
      alert("Auto Data Aggregation Successful!\n\nExtracted:\n- 240 Student Attainment Scores\n- 14 Faculty Research Records & FDPs\n- 3 GPU Lab Asset Registers\n- 60 Capstone Project Rubric Marks");
      setTimeout(function() {
        btn.innerHTML = '<i class="lucide-zap"></i> Collect Data Automatically';
        btn.disabled = false;
      }, 1500);
    }, 1200);
  },

  uploadDocumentModal: function() {
    var name = prompt("Enter Supporting Document Title (e.g., NBA_Criterion3_Research_Grants.pdf):");
    if (name) {
      window.AcademicHubData.accreditation.nba.evidences.push({
        id: "EVID-NBA-0" + (window.AcademicHubData.accreditation.nba.evidences.length + 1),
        criterion: "Criterion " + this.activeCriterion,
        docName: name,
        size: "3.2 MB",
        type: "PDF",
        status: "Verified",
        uploadedBy: "IQAC Coordinator"
      });
      this.render('mainAppView');
    }
  },

  generateAccreditationReport: function() {
    alert("Compiling NBA Self Assessment Report (SAR) Document...\n\nTotal Score: 785 / 1000 Points\nStatus: Tier-I Accreditation Recommended for 6 Years.\n\nReport downloaded to workspace!");
    window.ReportsEngine.selectedReportType = 'department';
    window.ReportsEngine.exportPDF();
  }
};
