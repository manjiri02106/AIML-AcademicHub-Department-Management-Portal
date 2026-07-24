<?php
require_once __DIR__ . '/config.php';
$active_role = $_SESSION['user_role'] ?? 'HOD';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AIML ACADEMICHUB - Reports, Accreditation & Integration Portal (PHP)</title>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- jsPDF & AutoTable for PDF Export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

  <!-- XLSX SheetJS for Excel Export -->
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

  <!-- Stylesheet -->
  <link rel="stylesheet" href="css/main.css">
</head>
<body>

  <div id="app">
    <!-- Left Navigation Sidebar -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content Right Area -->
    <div class="main-content">

      <!-- Top Header Navbar -->
      <header class="top-navbar">
        <div style="display: flex; align-items: center; gap: 12px;">
          <h2 style="font-size: 1.1rem; color: var(--text-main);">Dept. of <?php echo htmlspecialchars($DEPT_INFO['name']); ?></h2>
        </div>

        <div style="display: flex; align-items: center; gap: 16px;">
          
          <!-- Role Based Switcher (PHP Session Synced) -->
          <div class="role-badge-container" style="background: rgba(99, 102, 241, 0.12); border: 1px solid var(--primary);">
            <i class="lucide-user-check" style="color: var(--cyan); font-size: 0.95rem;"></i>
            <span style="font-size: 0.82rem; color: var(--text-muted); font-weight: 600;">Active Role:</span>
            <select class="role-selector" id="phpRoleSelect" style="font-weight: 700; color: var(--cyan);" onchange="App.setRole(this.value)">
              <option value="HOD" <?php echo $active_role === 'HOD' ? 'selected' : ''; ?>>HOD (Head of Department)</option>
              <option value="Administrator" <?php echo $active_role === 'Administrator' ? 'selected' : ''; ?>>Administrator (System Control)</option>
              <option value="IQAC" <?php echo $active_role === 'IQAC' ? 'selected' : ''; ?>>IQAC / NBA Coordinator</option>
              <option value="Student" <?php echo $active_role === 'Student' ? 'selected' : ''; ?>>Student</option>
            </select>
            <span id="currentRoleDisplay" style="margin-left: 12px; font-weight: 700; color: var(--cyan);"><?php echo htmlspecialchars($active_role); ?></span>
          </div>

          <button class="btn btn-secondary" style="padding: 8px 12px;" onclick="document.body.classList.toggle('light-theme')">
            <i class="lucide-sun-moon"></i> Theme
          </button>
        </div>
      </header>
<?php // Header component continues to main container ?>
