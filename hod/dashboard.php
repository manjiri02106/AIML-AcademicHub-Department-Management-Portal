<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['HOD']);
$pageTitle = 'HOD Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> | AIML AcademicHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="dashboard-card p-4 p-lg-5">
            <h2 class="page-title">HOD Dashboard</h2>
            <p class="text-muted">Welcome, <?= escape($_SESSION['user']['name']) ?>.</p>
            <a href="/auth/logout.php" class="btn btn-primary">Logout</a>
        </div>
    </div>
</body>
</html>
