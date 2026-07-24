<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['Student']);
$pageTitle = 'Student Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> | AIML AcademicHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
</head>
<body>
    <div class="container py-5">
        <div class="dashboard-card p-4 p-lg-5 text-center">
            <h2 class="page-title">Student Dashboard</h2>
            <p class="text-muted mb-4">Welcome, <?= escape($_SESSION['user']['name']) ?>.</p>
            
            <div class="row g-4 max-width-600 mx-auto mb-4">
                <div class="col-md-6">
                    <div class="card p-3 border shadow-sm h-100">
                        <h5><i class="bi bi-kanban text-primary fs-3 d-block mb-2"></i> Projects & Internships</h5>
                        <p class="small text-muted">Register projects, upload deliverables and track progress.</p>
                        <a href="<?= url('/project-internships/') ?>" class="btn btn-primary btn-sm mt-auto">Open Workspace</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3 border shadow-sm h-100">
                        <h5><i class="bi bi-briefcase text-success fs-3 d-block mb-2"></i> Placements (TPO)</h5>
                        <p class="small text-muted">Register, view upcoming placement drives and status.</p>
                        <a href="<?= url('/academic_hub/dashboard.php') ?>" class="btn btn-success btn-sm mt-auto">Open Placement Drive</a>
                    </div>
                </div>
            </div>

            <a href="<?= url('/auth/logout.php') ?>" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</body>
</html>
