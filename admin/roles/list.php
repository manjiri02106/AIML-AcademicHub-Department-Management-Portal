<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('roles');

$pdo = getDbConnection();
$roles = $pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Roles & Permissions</h3>
        <p class="text-muted mb-0">Create and assign permissions for each role.</p>
    </div>
    <a class="btn btn-primary" href="<?= url('/admin/roles/add.php') ?>">Create Role</a>
</div>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td>#<?= $role['id'] ?></td>
                        <td><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><a class="btn btn-sm btn-outline-secondary" href="edit.php?id=<?= $role['id'] ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
