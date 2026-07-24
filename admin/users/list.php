<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('users');

$pdo = getDbConnection();
$search = trim($_GET['search'] ?? '');
$roleFilter = (int)($_GET['role'] ?? 0);
$statusFilter = trim($_GET['status'] ?? '');

$query = 'SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON r.id = u.role_id WHERE 1=1';
$params = [];

if ($search !== '') {
    $query .= ' AND (u.full_name LIKE ? OR u.email LIKE ? OR u.department LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($roleFilter > 0) {
    $query .= ' AND u.role_id = ?';
    $params[] = $roleFilter;
}

if ($statusFilter !== '') {
    $query .= ' AND u.status = ?';
    $params[] = $statusFilter;
}

$query .= ' ORDER BY u.id DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
$roles = $pdo->query('SELECT id, name FROM roles ORDER BY id')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">User Management</h3>
        <p class="text-muted mb-0">Manage users, roles, department access, and account status.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('/admin/users/export.php') ?>">Export CSV</a>
        <a class="btn btn-primary" href="<?= url('/admin/users/add.php') ?>">Add User</a>
    </div>
</div>
<form class="row g-2 align-items-end mb-4" method="get">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search name, email or department" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="col-md-3">
        <select name="role" class="form-select">
            <option value="0">All Roles</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id'] ?>" <?= $roleFilter === (int)$role['id'] ? 'selected' : '' ?>><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100">Filter</button>
    </div>
</form>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['department'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($user['role_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>"><?= htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <a class="btn btn-sm btn-outline-primary" href="view.php?id=<?= $user['id'] ?>" title="View profile">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                                <a class="btn btn-sm btn-outline-secondary" href="edit.php?id=<?= $user['id'] ?>" title="Edit user">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <?php if (($user['status'] ?? 'active') === 'active'): ?>
                                    <a class="btn btn-sm btn-outline-warning" href="status.php?id=<?= $user['id'] ?>&status=inactive" title="Deactivate">
                                        <i class="bi bi-person-x me-1"></i>Deactivate
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-success" href="status.php?id=<?= $user['id'] ?>&status=active" title="Activate">
                                        <i class="bi bi-person-check me-1"></i>Activate
                                    </a>
                                <?php endif; ?>
                                <a class="btn btn-sm btn-outline-info" href="reset_password.php?id=<?= $user['id'] ?>" title="Reset password">
                                    <i class="bi bi-key me-1"></i>Reset
                                </a>
                                <a class="btn btn-sm btn-outline-danger btn-delete" href="delete.php?id=<?= $user['id'] ?>" title="Delete user">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
