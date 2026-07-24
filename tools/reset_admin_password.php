<?php
require __DIR__ . '/../includes/config.php';

if (PHP_SAPI !== 'cli') {
    echo "Run this script from the command line.\n";
    exit(1);
}

if ($argc < 3) {
    echo "Usage: php reset_admin_password.php <email> <new_password>\n";
    exit(1);
}

$email = $argv[1];
$newPassword = $argv[2];

try {
    $pdo = getDbConnection();
    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $stmt->execute([$hash, $email]);

    if ($stmt->rowCount() > 0) {
        echo "Password updated for {$email}\n";
        exit(0);
    }

    echo "No user found with email {$email}\n";
    exit(2);
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(3);
}
