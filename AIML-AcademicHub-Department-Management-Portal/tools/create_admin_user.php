<?php
require __DIR__ . '/../includes/config.php';

if (PHP_SAPI !== 'cli') {
    echo "Run this script from the command line.\n";
    exit(1);
}

function genRandStr(int $len = 6): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $s = '';
    for ($i = 0; $i < $len; $i++) {
        $s .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $s;
}

$localDomain = 'aiml.edu';
$email = 'admin+' . genRandStr(6) . '@' . $localDomain;
$password = bin2hex(random_bytes(8)); // 16 hex chars (~16 chars)
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $pdo = getDbConnection();

    // Ensure email is unique; if not, append extra suffix
    $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM users WHERE email = ?');
    $attempt = 0;
    while (true) {
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row || $row['cnt'] == 0) {
            break;
        }
        $attempt++;
        $email = 'admin+' . genRandStr(6) . ($attempt > 1 ? $attempt : '') . '@' . $localDomain;
    }

    $insert = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role_id, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $insert->execute(['Auto Generated Admin', $email, $hash, 1, 'active']);

    echo "NEW_ACCOUNT_CREATED\n";
    echo "email: {$email}\n";
    echo "password: {$password}\n";
    echo "login_url: http://127.0.0.1:8000/login.php\n";
    exit(0);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
