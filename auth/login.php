<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . roleToDashboardPath($_SESSION['user']['role']));
    exit;
}

$message = '';
$type = 'danger';

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        if ($email === '' || $password === '') {
            $message = 'Please enter both email and password.';
        } else {
            $stmt = getDb()->prepare('SELECT u.id, u.full_name, u.name, u.email, u.password_hash, u.password, u.role, u.role_id, u.is_active, u.status, u.last_login, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $pwHash = $user['password_hash'] ?? $user['password'] ?? '';
                $isActive = ($user['status'] === 'active') || ((int)($user['is_active'] ?? 0) === 1);

                if (password_verify($password, $pwHash) && $isActive) {
                    $roleName = $user['role'] ?? $user['role_name'] ?? 'Administrator';
                    $displayName = $user['name'] ?? $user['full_name'] ?? 'User';

                    // Load permissions for old admin module
                    if ($user['role_id']) {
                        $permStmt = getDb()->prepare('SELECT p.slug FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = ?');
                        $permStmt->execute([$user['role_id']]);
                        $permissions = array_column($permStmt->fetchAll(), 'slug');
                    } else {
                        $permissions = [];
                    }

                    // Old session format
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['permissions'] = $permissions;

                    // New session format
                    $_SESSION['user'] = [
                        'id' => (int) $user['id'],
                        'name' => $displayName,
                        'full_name' => $user['full_name'],
                        'email' => $user['email'],
                        'role' => $roleName,
                        'role_name' => $user['role_name'],
                    ];
                    $_SESSION['last_activity'] = time();

                    $stmt = getDb()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                    $stmt->execute([$user['id']]);
                    header('Location: ' . roleToDashboardPath($roleName));
                    exit;
                }
            }

            $message = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AIML AcademicHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
</head>
<body class="auth-page">
    <div class="container-fluid vh-100">
        <div class="row h-100 g-0">
            <div class="col-lg-7 d-none d-lg-flex align-items-center justify-content-center auth-hero">
                <div class="hero-content text-white p-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="hero-icon">
                            <img src="<?= url('/assets/images/image.png') ?>" alt="AIML AcademicHub Logo">
                        </div>
                        <div>
                            <h3 class="fw-semibold mb-0">AIML AcademicHub</h3>
                            <p class="mb-0 text-white-50">Department Management Portal</p>
                        </div>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">Secure academic operations for modern departments.</h1>
                    <p class="lead text-white-50">Manage students, faculty, assessments, and institutional workflows from one corporate-grade portal.</p>
                </div>
            </div>
            <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white">
                <div class="card auth-card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <div class="brand-logo mx-auto mb-3">
                                <img src="<?= url('/assets/images/image.png') ?>" alt="AIML AcademicHub Logo" class="img-fluid">
                            </div>
                            <h2 class="fw-bold mt-3 mb-1">Welcome Back</h2>
                            <p class="text-muted">Sign in to continue to your workspace.</p>
                        </div>
                        <?php if ($message !== ''): ?>
                            <div class="alert alert-danger" role="alert"><?= escape($message) ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Enter a Email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
