<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function ensureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function getDb(): PDO
{
    return getDbConnection();
}

function setFlash(string $type, string $message): void
{
    ensureSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function renderFlash(): void
{
    ensureSession();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        $type = htmlspecialchars((string)$flash['type'], ENT_QUOTES, 'UTF-8');
        $message = htmlspecialchars((string)$flash['message'], ENT_QUOTES, 'UTF-8');
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['flash']);
    }
}

function logActivity(string $message, ?int $userId = null): void
{
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO activity_logs (user_id, message, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$userId ?? null, $message]);
    } catch (Throwable $e) {
        // ignore
    }
}

function getSettingValue(string $key, string $default = ''): string
{
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT value FROM system_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $row = $stmt->fetch();

        return $row['value'] ?? $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function setSettingValue(string $key, string $value): void
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('INSERT INTO system_settings (setting_key, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)');
    $stmt->execute([$key, $value]);
}

function url(string $path): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
