<?php
declare(strict_types=1);

if (!defined('BASE_URL')) {
    $documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));

    $documentRoot = rtrim($documentRoot, '/');
    $projectRoot = rtrim($projectRoot, '/');

    $basePath = '';
    if ($documentRoot !== '' && $projectRoot !== '' && strpos($projectRoot, $documentRoot . '/') === 0) {
        $relativePath = substr($projectRoot, strlen($documentRoot));
        $relativePath = trim($relativePath, '/');
        $basePath = $relativePath === '' ? '' : '/' . $relativePath;
    }

    define('BASE_URL', $basePath);
}

function initializeDatabase(PDO $pdo): void
{
    $requiredTables = ['roles', 'permissions', 'role_permissions', 'users', 'system_settings', 'activity_logs'];

    foreach ($requiredTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            continue;
        }

        $schemaPath = dirname(__DIR__) . '/database/schema.sql';

        if (!is_file($schemaPath) || !is_readable($schemaPath)) {
            return;
        }

        $sql = file_get_contents($schemaPath);

        if ($sql === false || trim($sql) === '') {
            return;
        }

        $statements = array_values(array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql)), static function (string $statement): bool {
            return $statement !== '' && strpos($statement, '--') !== 0 && strpos($statement, '#') !== 0;
        }));

        foreach ($statements as $statement) {
            $pdo->exec($statement);
        }

        return;
    }
}

function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = 'mysql:host=127.0.0.1;charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS aiml_academichub");
        $pdo->exec("USE aiml_academichub");
        initializeDatabase($pdo);
    } catch (PDOException $e) {
        throw new RuntimeException('Database connection failed: ' . $e->getMessage());
    }

    return $pdo;
}
