<?php
require_once __DIR__ . '/config/database.php';

$sql = "
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    $pdo->exec($sql);
    $adminHash = password_hash('Admin@123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute(['admin@aiml.edu']);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare('INSERT INTO users (name, email, password, role, is_active) VALUES (?, ?, ?, ?, 1)');
        $insert->execute(['System Administrator', 'admin@aiml.edu', $adminHash, 'Administrator']);
    }
    echo "Database setup completed successfully.\n";
    echo "Default login: admin@aiml.edu / Admin@123\n";
} catch (PDOException $e) {
    echo 'Setup failed: ' . $e->getMessage();
}
