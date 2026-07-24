<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('roles');

$pdo = getDbConnection();
$permissions = $pdo->query('SELECT * FROM permissions ORDER BY id')->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $selected = $_POST['permissions'] ?? [];

    if ($name === '') {
        $message = 'Role name is required.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO roles (name) VALUES (?)');
        $stmt->execute([$name]);
        $roleId = (int)$pdo->lastInsertId();

        if ($roleId > 0) {
            $insert = $pdo->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)');
            foreach ($selected as $permId) {
                $insert->execute([$roleId, (int)$permId]);
            }
        }

        logActivity('Created role: ' . $name);
        header('Location: list.php');
        exit;
    }
}
?>
<div class="card shadow-sm border-0 p-4">
    <h3 class="mb-3">Create Role</h3>
    <?php if ($message !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3"><label class="form-label">Role Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3">
            <label class="form-label">Assign Permissions</label>
            <div class="row">
                <?php foreach ($permissions as $permission): ?>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>">
                            <label class="form-check-label"><?= htmlspecialchars($permission['label'], ENT_QUOTES, 'UTF-8') ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="btn btn-primary">Save Role</button>
        <a class="btn btn-outline-secondary" href="list.php">Back</a>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
