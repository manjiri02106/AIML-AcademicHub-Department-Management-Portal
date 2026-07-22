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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
        } else {
            $stmt = getDb()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp_code'] = $otp;
                $_SESSION['otp_expires'] = time() + 300;
                header('Location: verify_otp.php');
                exit;
            } else {
                $message = 'No account found for that email.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | AIML AcademicHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="auth-page">
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card auth-card shadow-sm border-0 w-100" style="max-width: 480px;">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <div class="brand-logo mx-auto mb-3">
                        <img src="/assets/images/image.png" alt="AIML AcademicHub Logo" class="img-fluid">
                    </div>
                    <h2 class="fw-bold mt-3 mb-1">Forgot Password</h2>
                    <p class="text-muted">Enter your registered email to receive a one-time verification code.</p>
                </div>
                <?php if ($message !== ''): ?>
                    <div class="alert alert-<?= escape($type) ?>" role="alert"><?= escape($message) ?></div>
                <?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrfToken()) ?>">
                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Continue</button>
                    <div class="text-center">
                        <a href="login.php" class="text-decoration-none">Back to login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
