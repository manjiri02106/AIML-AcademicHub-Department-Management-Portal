<?php
require_once __DIR__ . '/../../includes/functions.php';
requirePermission('users');

$pdo = getDbConnection();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
    logActivity('Deleted user ID ' . $id);
}

header('Location: ' . BASE_URL . '/admin/users/list.php');
exit;
