<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Database Connection Configuration (PDO)
// ====================================================================

$host     = 'localhost';
$username = 'root';
$password = '';
$dbname   = 'aiml_lab_db';

try {
    // 1. Try connecting directly to aiml_lab_db
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // 2. If database does not exist, try creating it and importing aiml_lab_db.sql automatically
    try {
        $pdoRoot = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        $sqlFilePath = __DIR__ . '/../database/aiml_lab_db.sql';
        if (file_exists($sqlFilePath)) {
            $sqlContent = file_get_contents($sqlFilePath);
            $pdo->exec($sqlContent);
        }
    } catch (PDOException $ex) {
        die("<div style='padding:20px; font-family:sans-serif; background:#fee2e2; color:#991b1b; border-radius:8px; margin:20px;'>
            <h2>Database Connection Error</h2>
            <p>Could not connect to MySQL server or database: <strong>" . htmlspecialchars($ex->getMessage()) . "</strong></p>
            <p>Please make sure XAMPP MySQL service is running on <code>localhost</code> with root user and no password, or import <code>database/aiml_lab_db.sql</code> into phpMyAdmin.</p>
        </div>");
    }
}
?>
