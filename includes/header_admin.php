<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
ensureSession();
$user = getCurrentUser();
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
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar bg-dark text-white p-4">
        <div class="sidebar-brand mb-5 d-flex align-items-center gap-3">
            <div class="sidebar-logo rounded-3 d-flex align-items-center justify-content-center overflow-hidden bg-white">
                <img src="<?= url('/assets/image/image.png') ?>" alt="AIML Hub Logo" class="img-fluid" />
            </div>
            <div>
                <h5 class="mb-0">AIML Hub</h5>
                <small class="text-muted text-white-50">Academic Portal</small>
            </div>
        </div>
        <ul class="nav flex-column sidebar-nav">
            <li class="nav-item"><a class="nav-link text-white" href="<?= url('/admin/dashboard/') ?>"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= url('/admin/users/list.php') ?>"><i class="bi bi-mortarboard-fill"></i>Students</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= url('/admin/roles/list.php') ?>"><i class="bi bi-person-badge"></i>Faculty</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= url('/admin/settings/') ?>"><i class="bi bi-calendar-check-fill"></i>Attendance</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= url('/admin/backup/') ?>"><i class="bi bi-kanban-fill"></i>Projects</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= url('/admin/reports/') ?>"><i class="bi bi-bar-chart-line-fill"></i>Reports</a></li>
        </ul>
        <div class="mt-auto pt-4 sidebar-footer">
            <a class="nav-link text-white px-0" href="<?= url('/logout.php') ?>"><i class="bi bi-box-arrow-right me-3"></i>Logout</a>
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
                    <span class="badge bg-primary"><?= htmlspecialchars($user['role_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="fw-semibold"><?= htmlspecialchars($user['full_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span>
                    <a class="btn btn-outline-secondary btn-sm" href="<?= url('/logout.php') ?>">Logout</a>
                </div>
            </div>
        </nav>
        <div class="content-body p-4">
