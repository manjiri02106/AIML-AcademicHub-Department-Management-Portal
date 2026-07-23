<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('dashboard');

$pdo = getDbConnection();
$totalStudents = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalFaculty = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE designation LIKE '%Faculty%' OR role_id = 3")->fetchColumn();
$totalProjects = 18;
$totalNotices = 6;
$totalUsers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$settings = $pdo->query('SELECT * FROM department_settings ORDER BY id DESC LIMIT 1')->fetch();
$activities = $pdo->query('SELECT * FROM activity_logs ORDER BY id DESC LIMIT 5')->fetchAll();
?>
<div class="dashboard-hero mb-4 p-4 rounded-4 bg-white shadow-sm">
    <div class="d-flex justify-content-between align-items-start gap-4 flex-column flex-md-row">
        <div>
            <p class="text-muted mb-1">Welcome back</p>
            <h1 class="display-6 fw-bold mb-2">Hello, <?= htmlspecialchars($user['full_name'] ?? $user['name'] ?? 'System Administrator', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-muted">Monitor academic performance, attendance, placements, and institutional reports from one polished workspace.</p>
        </div>
        <div class="search-card p-3 rounded-4 bg-light">
            <div class="d-flex align-items-center gap-3">
                <input class="form-control border-0 bg-white" type="text" placeholder="Search modules">
                <button class="btn btn-primary">Search</button>
            </div>
            <div class="d-flex align-items-center gap-3 mt-3">
                <div class="avatar rounded-circle d-flex align-items-center justify-content-center bg-primary text-white">A</div>
                <div>
                    <p class="mb-0 fw-semibold">Administrator</p>
                    <small class="text-muted">Department Control</small>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-6 col-xl-4">
        <div class="card stat-card tile-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Students</p>
                    <h3 class="mb-0"><?= $totalStudents ?></h3>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3"><i class="bi bi-mortarboard fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card stat-card tile-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Faculty</p>
                    <h3 class="mb-0"><?= $totalFaculty ?></h3>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3"><i class="bi bi-person-workspace fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card stat-card tile-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Projects</p>
                    <h3 class="mb-0"><?= $totalProjects ?></h3>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3"><i class="bi bi-kanban fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card stat-card tile-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Notices</p>
                    <h3 class="mb-0"><?= $totalNotices ?></h3>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3"><i class="bi bi-megaphone fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card stat-card tile-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Users</p>
                    <h3 class="mb-0"><?= $totalUsers ?></h3>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-circle p-3"><i class="bi bi-people fs-4"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card stat-card tile-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Storage Usage</p>
                    <h3 class="mb-0">74%</h3>
                </div>
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-3"><i class="bi bi-hdd fs-4"></i></div>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mt-1">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-4">
            <p class="text-muted mb-2">Students</p>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0"><?= $totalStudents ?></h2>
                <span class="rounded-circle p-3 bg-primary bg-opacity-15 text-primary"><i class="bi bi-people fs-4"></i></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-4">
            <p class="text-muted mb-2">Faculty</p>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0"><?= $totalFaculty ?></h2>
                <span class="rounded-circle p-3 bg-success bg-opacity-15 text-success"><i class="bi bi-person-workspace fs-4"></i></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-4">
            <p class="text-muted mb-2">Attendance</p>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0">92%</h2>
                <span class="rounded-circle p-3 bg-warning bg-opacity-15 text-warning"><i class="bi bi-check2-square fs-4"></i></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card p-4">
            <p class="text-muted mb-2">Projects</p>
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0"><?= $totalProjects ?></h2>
                <span class="rounded-circle p-3 bg-info bg-opacity-15 text-info"><i class="bi bi-kanban fs-4"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card p-4">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a class="btn btn-outline-primary" href="<?= url('/admin/users/add.php') ?>">Add New User</a>
                <a class="btn btn-outline-secondary" href="<?= url('/admin/roles/add.php') ?>">Create Role</a>
                <a class="btn btn-outline-success" href="<?= url('/admin/backup/') ?>">Create Backup</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-4">
            <h5 class="mb-3">Recent Activities</h5>
            <ul class="list-group list-group-flush">
                <?php foreach ($activities as $activity): ?>
                    <li class="list-group-item"><?= htmlspecialchars($activity['message'], ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
