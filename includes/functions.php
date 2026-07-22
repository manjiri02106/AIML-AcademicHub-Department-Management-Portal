<?php
<<<<<<< HEAD
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function ensureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function requireLogin(): void
{
    // Authentication bypassed for local development/testing.
    // WARNING: this disables all login checks. Revert this change for production.
    return;
}

function requirePermission(string $permission): void
{
    requireLogin();

    if (!hasPermission($permission)) {
        http_response_code(403);
        echo '<div class="alert alert-danger m-4">Access denied.</div>';
        exit;
    }
}

function hasPermission(string $permission): bool
{
    ensureSession();
    $permissions = $_SESSION['permissions'] ?? [];

    return in_array($permission, $permissions, true) || in_array('dashboard', $permissions, true);
}

function getCurrentUser(): array
{
    ensureSession();

    return $_SESSION['user'] ?? [];
}

function logActivity(string $message, ?int $userId = null): void
{
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO activity_logs (user_id, message, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$userId ?? (getCurrentUser()['id'] ?? null), $message]);
    } catch (Throwable $e) {
        // Intentionally ignored to keep the UI responsive.
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
    return BASE_URL . $path;
}
=======
require_once __DIR__ . '/../config/database.php';

function getDb(): PDO
{
    global $pdo;
    return $pdo;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function renderFlash(): void
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        echo '<div class="alert alert-' . escape($flash['type']) . ' alert-dismissible fade show" role="alert">' . escape($flash['message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        unset($_SESSION['flash']);
    }
}
>>>>>>> 5547624 (i have done my part)
