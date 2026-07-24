<!-- Left Navigation Sidebar Component -->
<aside class="sidebar">
  <div class="brand-header">
    <div class="brand-icon">
      <img src="image/image.png" alt="AIML AcademicHub logo">
    </div>
    <div class="brand-text">
      <h1>AIML ACADEMICHUB</h1>
      <span>PHP Department Portal</span>
    </div>
  </div>

  <nav class="nav-menu">
    <div class="nav-section-title">Core Dashboards</div>
    <a class="nav-item active" data-view="dashboard" onclick="App.renderView('dashboard')">
      <i class="lucide-layout-dashboard"></i> Department Dashboard
    </a>

    <div class="nav-section-title">Core Workflow Architecture</div>
    <a class="nav-item" data-view="integration" onclick="App.renderView('integration')">
      <i class="lucide-layers"></i> Integration Layer & Central DB
    </a>
    <a class="nav-item" data-view="processing" onclick="App.renderView('processing')">
      <i class="lucide-cpu"></i> Data Processing Engine
    </a>
    <a class="nav-item" data-view="reports" onclick="App.renderView('reports')">
      <i class="lucide-file-bar-chart"></i> Reports Module
    </a>
    <a class="nav-item" data-view="accreditation" onclick="App.renderView('accreditation')">
      <i class="lucide-award"></i> Accreditation Module
    </a>
  </nav>

  <div style="padding: 16px 24px; border-top: 1px solid var(--border-color);">
    <p style="font-size: 0.75rem; color: var(--text-subtle);">AIML AcademicHub v2.5 (PHP)</p>
    <p style="font-size: 0.72rem; color: var(--cyan); margin-top: 2px;">● Central DB Synced</p>
  </div>
</aside>
