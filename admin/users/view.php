<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('users');

$pdo = getDbConnection();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE u.id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: list.php');
    exit;
}
?>
<div class="card shadow-sm border-0 p-4">
    <h3 class="mb-3">User Details</h3>
    <dl class="row">
        <dt class="col-sm-3">Full Name</dt><dd class="col-sm-9"><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Mobile</dt><dd class="col-sm-9"><?= htmlspecialchars($user['mobile'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Department</dt><dd class="col-sm-9"><?= htmlspecialchars($user['department'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Designation</dt><dd class="col-sm-9"><?= htmlspecialchars($user['designation'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Role</dt><dd class="col-sm-9"><?= htmlspecialchars($user['role_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><?= htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8') ?></dd>
        <dt class="col-sm-3">Joining Date</dt><dd class="col-sm-9"><?= htmlspecialchars($user['joining_date'] ?? '-', ENT_QUOTES, 'UTF-8') ?></dd>
    </dl>
    <a class="btn btn-outline-secondary" href="list.php">Back</a>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
