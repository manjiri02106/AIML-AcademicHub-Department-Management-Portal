<?php
require_once __DIR__ . '/includes/functions.php';

ensureSession();

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard/');
    exit;
}

header('Location: ' . BASE_URL . '/login.php');
exit;
