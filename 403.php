<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Access Denied</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="auth-page">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card auth-card shadow-sm border-0 p-4 text-center">
            <div class="brand-logo mx-auto mb-3">
                <img src="/assets/images/image.png" alt="AIML AcademicHub Logo" class="img-fluid" style="max-width: 100px;">
            </div>
            <h1 class="display-4 fw-bold text-primary">403</h1>
            <h3 class="fw-semibold">Access Denied</h3>
            <p class="text-muted">You do not have permission to view this page.</p>
            <a href="/auth/login.php" class="btn btn-primary">Back to login</a>
        </div>
    </div>
</body>
</html>
