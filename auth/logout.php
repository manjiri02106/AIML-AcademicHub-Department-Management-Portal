<?php
require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../includes/functions.php';

session_unset();
session_destroy();
header('Location: ' . url('/auth/login.php'));
exit;
