<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$user = currentUser();
logActivity('Signed out', $user['id'] ?? null);

clearAuthSession();
session_unset();
session_destroy();

header('Location: /auth/login.php');
exit;
