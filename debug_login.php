<?php
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
echo "DB_OK\n";

$desiredPassword = 'admin123';
$desiredHash = password_hash($desiredPassword, PASSWORD_DEFAULT);

$updateStmt = $pdo->prepare('UPDATE users SET password_hash = ?, status = ? WHERE email = ?');
$updateStmt->execute([$desiredHash, 'active', 'admin@aiml.edu']);
echo "ADMIN RESET\n";

$stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, status, role_id FROM users WHERE email = ? LIMIT 1');
$stmt->execute(['admin@aiml.edu']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($user);
if ($user) {
    echo password_verify($desiredPassword, $user['password_hash']) ? "verified\n" : "mismatch\n";
}
