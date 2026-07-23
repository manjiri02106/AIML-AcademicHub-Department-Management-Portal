<?php
require_once __DIR__ . '/../../includes/functions.php';
requirePermission('users');

$pdo = getDbConnection();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $newPassword = 'Welcome123!';
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $id]);
    logActivity('Reset password for user ID ' . $id);
}

header('Location: ' . BASE_URL . '/admin/users/list.php');
exit;
