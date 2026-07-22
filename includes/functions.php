<?php
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
