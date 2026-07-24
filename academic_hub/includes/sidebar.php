<?php
// Professional sidebar for Academic ERP Placement module
$currentPage = basename($_SERVER['PHP_SELF']);
$menuItems = [
    ['label' => 'Dashboard', 'icon' => 'speedometer2', 'href' => 'dashboard.php', 'page' => 'dashboard.php'],
    ['label' => 'Companies', 'icon' => 'building', 'href' => 'companies.php', 'page' => 'companies.php'],
    ['label' => 'Placement Drives', 'icon' => 'calendar-event', 'href' => 'placement-drives.php', 'page' => 'placement-drives.php'],
    ['label' => 'Student Registration', 'icon' => 'person-plus', 'href' => 'students.php', 'page' => 'students.php'],
    ['label' => 'Eligible Students', 'icon' => 'list-check', 'href' => 'eligible-students.php', 'page' => 'eligible-students.php'],
    ['label' => 'Placed Students', 'icon' => 'award', 'href' => '#', 'page' => 'placed-students.php'],
    ['label' => 'Offers', 'icon' => 'graph-up', 'href' => '#', 'page' => 'offers.php'],
    ['label' => 'Reports', 'icon' => 'bar-chart-line', 'href' => 'reports.php', 'page' => 'reports.php'],
    ['label' => 'Settings', 'icon' => 'gear', 'href' => 'settings.php', 'page' => 'settings.php'],
];
?>
<aside class="sidebar-panel">
    <div class="sidebar-inner">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <div class="sidebar-brand">Academic ERP</div>
                <div class="sidebar-subtitle">Placement TPO Module</div>
            </div>
            <button class="btn btn-sm btn-outline-light sidebar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle menu">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <div class="collapse d-lg-block sidebar-menu" id="sidebarMenu">
            <?php foreach ($menuItems as $item):
                $isActive = $currentPage === $item['page'] ? 'active' : ''; ?>
                <a href="<?= $item['href'] ?>" class="sidebar-link <?= $isActive ?>">
                    <i class="bi bi-<?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="sidebar-divider"></div>

        <div class="sidebar-footer">
            <a href="<?= url('/auth/logout.php') ?>" class="sidebar-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

