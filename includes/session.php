<?php
/**
 * Secure session bootstrap and helper functions.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_name('aiml_academichub_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
}

if (!empty($_SESSION['user'])) {
    $timeoutSeconds = 1800;
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }

    if (time() - $_SESSION['last_activity'] > $timeoutSeconds) {
        session_unset();
        session_destroy();
        
        if (defined('BASE_URL')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/auth/login.php');
        } else {
            header('Location: /auth/login.php');
        }
        exit;
    }

    $_SESSION['last_activity'] = time();
}
