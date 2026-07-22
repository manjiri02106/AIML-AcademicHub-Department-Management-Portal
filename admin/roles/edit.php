<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('roles');

$pdo = getDbConnection();
$id = (int)($_GET['id'] ?? 0);
$permissions = $pdo->query('SELECT * FROM permissions ORDER BY id')->fetchAll();
$message = '';

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$roleStmt = $pdo->prepare('SELECT * FROM roles WHERE id = ? LIMIT 1');
$roleStmt->execute([$id]);
$role = $roleStmt->fetch();
if (!$role) {
    header('Location: list.php');
    exit;
}

$assignedStmt = $pdo->prepare('SELECT permission_id FROM role_permissions WHERE role_id = ?');
$assignedStmt->execute([$id]);
$assigned = array_column($assignedStmt->fetchAll(), 'permission_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $selected = $_POST['permissions'] ?? [];

    if ($name === '') {
        $message = 'Role name is required.';
    } else {
        $pdo->prepare('UPDATE roles SET name = ? WHERE id = ?')->execute([$name, $id]);
        $pdo->prepare('DELETE FROM role_permissions WHERE role_id = ?')->execute([$id]);
        $insert = $pdo->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)');
        foreach ($selected as $permId) {
            $insert->execute([$id, (int)$permId]);
        }
        logActivity('Updated role: ' . $name);
        header('Location: list.php');
        exit;
    }
}
?>
<div class="card shadow-sm border-0 p-4">
    <h3 class="mb-3">Edit Role</h3>
    <?php if ($message !== ''): ?><div class="alert alert-danger"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
        <div class="mb-3"><label class="form-label">Role Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="mb-3">
            <label class="form-label">Assign Permissions</label>
            <div class="row">
                <?php foreach ($permissions as $permission): ?>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>" <?= in_array((int)$permission['id'], $assigned, true) ? 'checked' : '' ?>>
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
