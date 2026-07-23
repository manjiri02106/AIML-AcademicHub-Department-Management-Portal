<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . roleToDashboardPath($_SESSION['user']['role']));
    exit;
}

if (empty($_SESSION['otp_verified']) || empty($_SESSION['otp_email'])) {
    header('Location: forgot_password.php');
    exit;
}

$message = '';
$type = 'danger';

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($requestMethod === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $strength = passwordStrength($password);
        if ($password !== $confirmPassword) {
            $message = 'Passwords do not match.';
        } elseif ($strength['score'] < 4) {
            $message = 'Password must be at least 8 characters with uppercase, lowercase, number, and symbol.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = getDb()->prepare('UPDATE users SET password = ?, updated_at = NOW() WHERE email = ?');
            $stmt->execute([$hashed, $_SESSION['otp_email']]);
            session_regenerate_id(true);
            unset($_SESSION['otp_verified'], $_SESSION['otp_email'], $_SESSION['otp_code'], $_SESSION['otp_expires']);
            $message = 'Password updated successfully. Please login.';
            $type = 'success';
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully. Please login.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | AIML AcademicHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
</head>
<body class="auth-page">
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card auth-card shadow-sm border-0 w-100" style="max-width: 520px;">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <div class="brand-logo mx-auto mb-3">
                        <img src="<?= url('/assets/images/image.png') ?>" alt="AIML AcademicHub Logo" class="img-fluid">
                    </div>
                    <h2 class="fw-bold mt-3 mb-1">Reset Password</h2>
                    <p class="text-muted">Create a strong new password for your account.</p>
                </div>
                <?php if ($message !== ''): ?>
                    <div class="alert alert-<?= escape($type) ?>" role="alert"><?= escape($message) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm password</label>
                        <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="mb-4">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted d-block mt-2">Use 8+ chars with uppercase, lowercase, number, and symbol.</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
