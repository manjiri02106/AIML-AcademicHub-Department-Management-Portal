<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/config.php';

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']['id']) && !empty($_SESSION['user']['role']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function clearAuthSession(): void
{
    $_SESSION['user'] = [];
    unset($_SESSION['user']);
    unset($_SESSION['csrf_token']);
    unset($_SESSION['last_activity']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        clearAuthSession();
        header('Location: ' . rtrim(BASE_URL, '/') . '/auth/login.php');
        exit;
    }
}

function requireRole(array $allowedRoles): void
{
    requireLogin();

    $role = $_SESSION['user']['role'] ?? '';
    if (!in_array($role, $allowedRoles, true)) {
        clearAuthSession();
        header('Location: ' . rtrim(BASE_URL, '/') . '/403.php');
        exit;
    }
}

function roleToDashboardPath(string $role): string
{
    $routes = [
        'Administrator' => '/admin/dashboard/',
        'HOD' => '/hod/dashboard.php',
        'Faculty' => '/faculty/dashboard.php',
        'Student' => '/student/dashboard.php',
        'TPO' => '/academic_hub/dashboard.php',
        'Lab Coordinator' => '/lab/dashboard.php',
        'IQAC/NBA Coordinator' => '/iqac/dashboard.php',
    ];

    $path = $routes[$role] ?? '/auth/login.php';
    return rtrim(BASE_URL, '/') . $path;
}

function passwordStrength(string $password): array
{
    $score = 0;
    if (strlen($password) >= 8) {
        $score++;
    }
    if (preg_match('/[A-Z]/', $password)) {
        $score++;
    }
    if (preg_match('/[a-z]/', $password)) {
        $score++;
    }
    if (preg_match('/\d/', $password)) {
        $score++;
    }
    if (preg_match('/[^A-Za-z0-9]/', $password)) {
        $score++;
    }

    $label = 'Weak';
    $class = 'danger';
    if ($score >= 4) {
        $label = 'Strong';
        $class = 'success';
    } elseif ($score >= 3) {
        $label = 'Good';
        $class = 'warning';
    }

    return ['score' => $score, 'label' => $label, 'class' => $class];
}

function escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
