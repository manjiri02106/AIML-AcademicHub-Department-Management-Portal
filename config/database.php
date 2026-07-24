<?php
/**
 * Database configuration for AIML AcademicHub.
 * Uses PDO with MySQL and auto-creates the target database when missing.
 */

$host = '127.0.0.1';
$dbName = 'aiml_academichub';
$dbUser = 'root';
$dbPass = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $dbUser, $dbPass, $options);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    if (strpos($e->getMessage(), 'Connection refused') !== false || strpos($e->getMessage(), 'SQLSTATE[HY000] [2002]') !== false) {
        die('MySQL server is not running. Start MySQL in XAMPP and refresh this page.');
    }

    die('Unable to connect to the database. Please check your MySQL configuration and try again.');
}
