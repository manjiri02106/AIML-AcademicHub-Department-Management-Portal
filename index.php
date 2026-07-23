<?php
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . roleToDashboardPath($_SESSION['user']['role']));
} else {
    clearAuthSession();
    header('Location: ' . rtrim(BASE_URL, '/') . '/auth/login.php');
}
exit;
