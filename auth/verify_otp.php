<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . roleToDashboardPath($_SESSION['user']['role']));
    exit;
}

if (empty($_SESSION['otp_email']) || empty($_SESSION['otp_code']) || empty($_SESSION['otp_expires']) || time() > $_SESSION['otp_expires']) {
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
        $otp = trim(implode('', $_POST['otp'] ?? []));
        if ($otp === $_SESSION['otp_code']) {
            $_SESSION['otp_verified'] = true;
            header('Location: reset_password.php');
            exit;
        }
        $message = 'The OTP you entered is incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | AIML AcademicHub</title>
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
                    <h2 class="fw-bold mt-3 mb-1">Verify OTP</h2>
                    <p class="text-muted">Enter the 6-digit code sent to your inbox.</p>
                </div>
                <?php if ($message !== ''): ?>
                    <div class="alert alert-danger" role="alert"><?= escape($message) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                    <div class="d-flex justify-content-between gap-2 mb-4">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                            <input type="text" class="form-control otp-input text-center" maxlength="1" name="otp[]" autocomplete="one-time-code" required>
                        <?php endfor; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mb-4">
                        <span>Code expires in 5 minutes</span>
                        <a href="forgot_password.php" class="text-decoration-none">Resend OTP</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">Verify</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
