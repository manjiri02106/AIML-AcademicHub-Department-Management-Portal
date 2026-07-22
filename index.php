<?php
<<<<<<< HEAD
require_once __DIR__ . '/includes/functions.php';

ensureSession();

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard/');
    exit;
}

header('Location: ' . BASE_URL . '/login.php');
=======
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . roleToDashboardPath($_SESSION['user']['role']));
} else {
    clearAuthSession();
    header('Location: /auth/login.php');
}
>>>>>>> 5547624 (i have done my part)
exit;
