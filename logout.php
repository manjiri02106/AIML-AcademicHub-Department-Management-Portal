<?php
require_once __DIR__ . '/includes/functions.php';

ensureSession();
logActivity('Signed out', getCurrentUser()['id'] ?? null);

session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/login.php');
exit;
