<?php
require_once __DIR__ . '/includes/functions.php';

ensureSession();

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/dashboard/');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = 'Please enter your email and password.';
    } else {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare('SELECT u.id, u.full_name, u.email, u.password_hash, u.status, u.role_id, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
                $permStmt = $pdo->prepare('SELECT p.slug FROM role_permissions rp JOIN permissions p ON p.id = rp.permission_id WHERE rp.role_id = ?');
                $permStmt->execute([$user['role_id']]);
                $permissions = array_column($permStmt->fetchAll(), 'slug');

                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role_name' => $user['role_name'],
                ];
                $_SESSION['permissions'] = $permissions;

                logActivity('Signed in successfully', $user['id']);
                header('Location: ' . BASE_URL . '/admin/dashboard/');
                exit;
            }

            $message = 'Invalid email or password.';
        } catch (Throwable $e) {
            $message = 'We could not sign you in right now. Please try again in a moment.';
        }
    }
}

include __DIR__ . '/includes/header_public.php';
?>
<div class="container-fluid py-4">
    <div class="row min-vh-100 align-items-center">
        <div class="col-lg-6 d-none d-lg-flex p-5">
            <div class="rounded-4 p-5 w-100 h-100 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%); color: white;">
                <div>
                    <h1 class="display-6 fw-bold">AIML AcademicHub</h1>
                    <p class="lead opacity-75">Modern department operations, students, faculty, and research collaboration in one elegant console.</p>
                </div>
                <div class="rounded-4 p-4 bg-white bg-opacity-10 backdrop-blur">
                    <h5 class="fw-semibold">Trusted by departments</h5>
                    <p class="mb-0">Secure, intuitive, and ready for enterprise administration.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 p-4 p-lg-5">
            <div class="card shadow border-0 mx-auto" style="max-width: 480px;">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h4 fw-semibold mb-1">Administrator Login</h2>
                    <p class="text-muted">Secure access to the AIML AcademicHub admin module.</p>
                    <?php if ($message !== ''): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3 position-relative">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control ps-4" required>
                        </div>
                        <div class="mb-3 position-relative">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control ps-4" required>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                                <label class="form-check-label">Remember me</label>
                            </div>
                            <a href="#" class="text-decoration-none">Forgot password?</a>
                        </div>
                        <button class="btn btn-primary w-100">Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer_public.php'; ?>
