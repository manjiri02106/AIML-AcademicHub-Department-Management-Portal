<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Sidebar Navigation Component
// ====================================================================
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fa-solid fa-flask-vial"></i>
            <span>Lab Manager</span>
        </div>
        <button class="sidebar-close-btn" id="sidebarCloseBtn">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">MAIN MENU</div>
        <a href="index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line nav-icon"></i>
            <span class="nav-text">Laboratory Dashboard</span>
        </a>
        <a href="lab_assets.php" class="nav-link <?php echo ($currentPage == 'lab_assets.php' || $currentPage == 'lab_details.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-flask nav-icon"></i>
            <span class="nav-text">Lab Assets</span>
        </a>
        <a href="equipment.php" class="nav-link <?php echo ($currentPage == 'equipment.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-desktop nav-icon"></i>
            <span class="nav-text">Equipment Records</span>
        </a>
        <a href="maintenance.php" class="nav-link <?php echo ($currentPage == 'maintenance.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-wrench nav-icon"></i>
            <span class="nav-text">Maintenance Logs</span>
        </a>
        <a href="schedule.php" class="nav-link <?php echo ($currentPage == 'schedule.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-calendar-days nav-icon"></i>
            <span class="nav-text">Lab Schedule</span>
        </a>

        <div class="nav-section-label">SYSTEM INFO</div>
        <div class="sidebar-info-card">
            <div class="info-card-header">
                <i class="fa-solid fa-building-columns"></i>
                <span>AIML Dept</span>
            </div>
            <p class="info-card-text">Academic Management & Lab Infrastructure Support System</p>
            <span class="status-indicator"><i class="fa-solid fa-circle"></i> System Active</span>
        </div>
    </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<main class="app-main-content">
