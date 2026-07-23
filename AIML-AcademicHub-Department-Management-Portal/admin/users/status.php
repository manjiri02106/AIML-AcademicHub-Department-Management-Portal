<?php
require_once __DIR__ . '/../../includes/functions.php';
requirePermission('users');

$pdo = getDbConnection();
$id = (int)($_GET['id'] ?? 0);
$status = trim($_GET['status'] ?? 'active');
if ($id > 0) {
    $stmt = $pdo->prepare('UPDATE users SET status = ? WHERE id = ?');
    $stmt->execute([$status, $id]);
    logActivity('Updated status for user ID ' . $id);
}

header('Location: ' . BASE_URL . '/admin/users/list.php');
exit;
