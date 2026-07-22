<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('users');

$pdo = getDbConnection();
$roles = $pdo->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
$id = (int)($_GET['id'] ?? 0);
$message = '';

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $roleId = (int)($_POST['role_id'] ?? 0);
    $status = trim($_POST['status'] ?? 'active');
    $joiningDate = trim($_POST['joining_date'] ?? '');

    if ($fullName === '' || $email === '' || $roleId <= 0) {
        $message = 'Please fill the required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } else {
        $update = $pdo->prepare('UPDATE users SET full_name = ?, email = ?, mobile = ?, department = ?, designation = ?, role_id = ?, status = ?, joining_date = ? WHERE id = ?');
        $update->execute([$fullName, $email, $mobile, $department, $designation, $roleId, $status, $joiningDate, $id]);
        logActivity('Updated user: ' . $fullName);
        header('Location: list.php');
        exit;
    }
}
?>
<div class="card shadow-sm border-0 p-4">
    <h3 class="mb-3">Edit User</h3>
    <?php if ($message !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="col-md-6"><label class="form-label">Mobile Number</label><input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Department</label><input type="text" name="department" class="form-control" value="<?= htmlspecialchars($user['department'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Designation</label><input type="text" name="designation" class="form-control" value="<?= htmlspecialchars($user['designation'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Role</label><select name="role_id" class="form-select" required><option value="">Select role</option><?php foreach ($roles as $role): ?><option value="<?= $role['id'] ?>" <?= (int)$user['role_id'] === (int)$role['id'] ? 'selected' : '' ?>><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="col-md-6"><label class="form-label">Joining Date</label><input type="date" name="joining_date" class="form-control" value="<?= htmlspecialchars($user['joining_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
        </div>
        <button class="btn btn-primary mt-4">Update User</button>
        <a class="btn btn-outline-secondary mt-4" href="list.php">Back</a>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
