<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
ensureSession();
$user = getCurrentUser();

$pdo = getDbConnection();
$settings = $pdo->query('SELECT theme_color FROM department_settings ORDER BY id DESC LIMIT 1')->fetch();
$themeColor = $settings['theme_color'] ?? '#0ea5e9';

function hexToRgb(string $hex): string {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 6) {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    } elseif (strlen($hex) === 3) {
        $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
        $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
        $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
    } else {
        return '14, 165, 233';
    }
    return "$r, $g, $b";
}
$themeColorRgb = hexToRgb($themeColor);

function isPageActive(string $path): string {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if ($path === '/admin/dashboard/') {
        return (strpos($uri, '/admin/dashboard/') !== false) ? 'active' : '';
    }
    $cleanPath = str_replace('list.php', '', $path);
    return (strpos($uri, $cleanPath) !== false) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIML AcademicHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('/assets/css/admin.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?= $themeColor ?>;
            --primary-color-rgb: <?= $themeColorRgb ?>;
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar p-4">
        <div class="sidebar-brand mb-5 d-flex align-items-center gap-3">
            <div class="sidebar-logo rounded-3 d-flex align-items-center justify-content-center overflow-hidden bg-white">
                <img src="<?= url('/assets/image/image.png') ?>" alt="AIML Hub Logo" class="img-fluid" />
            </div>
            <div>
                <h5 class="mb-0 text-dark">AIML Hub</h5>
                <small class="text-muted">Academic Portal</small>
            </div>
        </div>
        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item"><a class="nav-link <?= isPageActive('/admin/dashboard/') ?>" href="<?= url('/admin/dashboard/') ?>"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link <?= isPageActive('/admin/users/list.php') ?>" href="<?= url('/admin/users/list.php') ?>"><i class="bi bi-mortarboard-fill"></i>Students</a></li>
            <li class="nav-item"><a class="nav-link <?= isPageActive('/admin/roles/list.php') ?>" href="<?= url('/admin/roles/list.php') ?>"><i class="bi bi-person-badge"></i>Faculty</a></li>
            <li class="nav-item"><a class="nav-link <?= isPageActive('/admin/settings/') ?>" href="<?= url('/admin/settings/') ?>"><i class="bi bi-calendar-check-fill"></i>Attendance</a></li>
            <li class="nav-item"><a class="nav-link <?= isPageActive('/admin/backup/') ?>" href="<?= url('/admin/backup/') ?>"><i class="bi bi-kanban-fill"></i>Projects</a></li>
            <li class="nav-item"><a class="nav-link <?= isPageActive('/admin/reports/') ?>" href="<?= url('/admin/reports/') ?>"><i class="bi bi-bar-chart-line-fill"></i>Reports</a></li>
        </ul>
        <div class="mt-auto pt-4 sidebar-footer">
            <a class="nav-link px-0" href="<?= url('/logout.php') ?>"><i class="bi bi-box-arrow-right me-3"></i>Logout</a>
        </div>
    </aside>
    <main class="content-area bg-light">
        <nav class="navbar navbar-expand-lg bg-white border-bottom px-4 py-3 shadow-sm">
            <div class="d-flex justify-content-between w-100 align-items-center">
                <div>
                    <h5 class="mb-0">Dashboard</h5>
                    <small class="text-muted">University Department Management</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-theme-primary"><?= htmlspecialchars($user['role_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="fw-semibold text-dark"><?= htmlspecialchars($user['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/logout.php') ?>">Logout</a>
                </div>
            </div>
        </nav>
        <div class="content-body p-4">
