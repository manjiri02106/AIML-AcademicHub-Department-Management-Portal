<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Module 1: Laboratory Dashboard
// ====================================================================
require_once 'includes/db.php';

$pageTitle = "Laboratory Dashboard | AIML AcademicHub";

// Dynamic Counts from DB
$totalLabsCount = 6;
$totalEquipmentCount = 0;
$underMaintenanceCount = 0;

try {
    // Total Labs count
    $stmt = $pdo->query("SELECT COUNT(*) FROM labs");
    $totalLabsCount = $stmt->fetchColumn() ?: 6;

    // Total Equipment count (sum of quantities)
    $stmt = $pdo->query("SELECT SUM(quantity) FROM equipment");
    $totalEquipmentCount = $stmt->fetchColumn() ?: 0;

    // Under Maintenance count from maintenance_logs (active cases)
    $stmt = $pdo->query("SELECT COUNT(*) FROM maintenance_logs WHERE repair_status IN ('Pending', 'In Progress')");
    $underMaintenanceCount = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    // Fallback if query fails before db setup
}

include('header.php');
include('sidebar.php');
?>

<!-- 1. Hero Section Slider -->
<section class="hero-slider-container">
    <!-- Slide 1 -->
    <div class="hero-slide active" style="background-image: url('images/lab_hero1.jpg');">
        <div class="hero-overlay">
            <span class="hero-badge"><i class="fa-solid fa-microchip"></i> AIML Department Infrastructure</span>
            <h2 class="hero-welcome-title">Welcome to AIML Laboratory Management</h2>
            <div class="hero-quote-box">
                <p class="hero-quote">"Well-equipped laboratories inspire innovation, experimentation and practical learning."</p>
            </div>
        </div>
    </div>
    <!-- Slide 2 -->
    <div class="hero-slide" style="background-image: url('images/lab_hero2.jpg');">
        <div class="hero-overlay">
            <span class="hero-badge"><i class="fa-solid fa-atom"></i> Hardware & AI Systems</span>
            <h2 class="hero-welcome-title">Empowering Practical Excellence & Research</h2>
            <div class="hero-quote-box">
                <p class="hero-quote">"Well-equipped laboratories inspire innovation, experimentation and practical learning."</p>
            </div>
        </div>
    </div>
    <!-- Navigation Dots -->
    <div class="slider-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
    </div>
</section>

<!-- 2. Statistics Cards Section -->
<section class="stats-grid">
    <!-- Stat 1: Total Labs -->
    <div class="stat-card stat-navy">
        <div class="stat-icon-wrapper">
            <i class="fa-solid fa-flask-vial"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?php echo htmlspecialchars($totalLabsCount); ?></span>
            <span class="stat-label">Total Laboratories</span>
        </div>
    </div>

    <!-- Stat 2: Total Equipment -->
    <div class="stat-card stat-teal">
        <div class="stat-icon-wrapper">
            <i class="fa-solid fa-desktop"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?php echo htmlspecialchars($totalEquipmentCount); ?></span>
            <span class="stat-label">Total Equipment Units</span>
        </div>
    </div>

    <!-- Stat 3: Under Maintenance -->
    <div class="stat-card stat-warning">
        <div class="stat-icon-wrapper">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value"><?php echo htmlspecialchars($underMaintenanceCount); ?></span>
            <span class="stat-label">Under Maintenance</span>
        </div>
    </div>
</section>

<!-- 3. Quick Access Cards Section -->
<section class="quick-access-section">
    <div class="section-title-wrapper">
        <h2 class="section-title"><i class="fa-solid fa-bolt"></i> Quick Access Modules</h2>
    </div>
    <div class="quick-access-grid">
        <!-- Lab Assets -->
        <a href="lab_assets.php" class="quick-card">
            <div class="quick-card-icon">
                <i class="fa-solid fa-flask"></i>
            </div>
            <h3 class="quick-card-title">Lab Assets</h3>
            <p class="quick-card-desc">Overview of 6 departmental labs, capacities, faculty incharges, and installed software.</p>
            <i class="fa-solid fa-arrow-right quick-card-arrow"></i>
        </a>

        <!-- Equipment Records -->
        <a href="equipment.php" class="quick-card">
            <div class="quick-card-icon">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <h3 class="quick-card-title">Equipment Records</h3>
            <p class="quick-card-desc">Inventory management for Computer, Physics & BXEE, and Chemistry equipment.</p>
            <i class="fa-solid fa-arrow-right quick-card-arrow"></i>
        </a>

        <!-- Maintenance Logs -->
        <a href="maintenance.php" class="quick-card">
            <div class="quick-card-icon">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <h3 class="quick-card-title">Maintenance Logs</h3>
            <p class="quick-card-desc">Track issues, repair statuses, cost allocations, and hardware replacements.</p>
            <i class="fa-solid fa-arrow-right quick-card-arrow"></i>
        </a>

        <!-- Lab Schedule -->
        <a href="schedule.php" class="quick-card">
            <div class="quick-card-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <h3 class="quick-card-title">Lab Schedule</h3>
            <p class="quick-card-desc">Weekly interactive practical timetable filtered by year, division, and batch.</p>
            <i class="fa-solid fa-arrow-right quick-card-arrow"></i>
        </a>
    </div>
</section>

<!-- 4. About Laboratory Section -->
<section class="about-lab-card">
    <div class="about-grid">
        <div class="about-text">
            <h3><i class="fa-solid fa-building-circle-check"></i> About Department Laboratories</h3>
            <p>The Department of Artificial Intelligence & Machine Learning maintains state-of-the-art laboratory facilities designed to cultivate hands-on computational, hardware, and analytical capabilities among engineering students.</p>
            <p>Equipped with high-performance GPU workstations, Gigabit network backbones, advanced electronic trainer kits, and modern chemical analysis apparatus, our labs ensure an immersive learning ecosystem aligned with industry 4.0 standard requirements.</p>
            
            <ul class="features-list">
                <li><i class="fa-solid fa-circle-check"></i> High-Speed Centralized Gigabit LAN & WiFi</li>
                <li><i class="fa-solid fa-circle-check"></i> 100% Online UPS Power Backup</li>
                <li><i class="fa-solid fa-circle-check"></i> High-Definition LCD Projection Systems</li>
                <li><i class="fa-solid fa-circle-check"></i> 24x7 CCTV Security & Fire Safety Systems</li>
            </ul>
        </div>
        
        <div class="about-highlights">
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fa-solid fa-user-gear"></i></div>
                <div>
                    <span class="highlight-title">Certified Incharges</span>
                    <p class="highlight-sub">Dedicated faculty supervisors for every lab unit.</p>
                </div>
            </div>
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <span class="highlight-title">Safety & Compliance</span>
                    <p class="highlight-sub">Fully audited fire safety and chemical handling protocols.</p>
                </div>
            </div>
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fa-solid fa-code"></i></div>
                <div>
                    <span class="highlight-title">Industry Stack</span>
                    <p class="highlight-sub">Python, VS Code, CUDA, MySQL, Dev C++, XAMPP installed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('footer.php'); ?>
