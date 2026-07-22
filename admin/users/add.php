<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('users');

$pdo = getDbConnection();
$roles = $pdo->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $roleId = (int)($_POST['role_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'active');
    $joiningDate = trim($_POST['joining_date'] ?? '');

    if ($fullName === '' || $email === '' || $password === '' || $roleId <= 0) {
        $message = 'Please fill the required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $message = 'Password should be at least 8 characters.';
    } else {
        $existing = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $existing->execute([$email]);
        if ($existing->fetch()) {
            $message = 'A user with this email already exists.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, mobile, password_hash, department, designation, role_id, status, joining_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$fullName, $email, $mobile, password_hash($password, PASSWORD_DEFAULT), $department, $designation, $roleId, $status, $joiningDate]);
            logActivity('Created new user: ' . $fullName);
            header('Location: ' . BASE_URL . '/admin/users/list.php');
            exit;
        }
    }
}
?>
<div class="card shadow-sm border-0 p-4">
    <h3 class="mb-3">Add User</h3>
    <?php if ($message !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Mobile Number</label><input type="text" name="mobile" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Department</label><input type="text" name="department" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Designation</label><input type="text" name="designation" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Role</label><select name="role_id" class="form-select" required><option value="">Select role</option><?php foreach ($roles as $role): ?><option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <div class="col-md-6"><label class="form-label">Joining Date</label><input type="date" name="joining_date" class="form-control"></div>
        </div>
        <button class="btn btn-primary mt-4">Create User</button>
        <a class="btn btn-outline-secondary mt-4" href="list.php">Back</a>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
