<?php
/* AIML ACADEMICHUB - PHP Configuration & Session Manager */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default active role in PHP session if not set
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'HOD';
}

// Handle role switcher POST/GET request in PHP
if (isset($_GET['role'])) {
    $allowed_roles = ['HOD', 'Administrator', 'IQAC', 'Student'];
    if (in_array($_GET['role'], $allowed_roles)) {
        $_SESSION['user_role'] = $_GET['role'];
    }
}

// Department PHP Metadata Definition
$DEPT_INFO = [
    'name' => 'Artificial Intelligence & Machine Learning',
    'code' => 'AIML',
    'institution' => 'AIML ACADEMICHUB Portal',
    'version' => '2.5 (PHP Engine)'
];
?>
