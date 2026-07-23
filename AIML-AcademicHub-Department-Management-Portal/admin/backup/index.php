<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('backup');

$pdo = getDbConnection();
$backups = $pdo->query('SELECT * FROM backup_logs ORDER BY id DESC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $backupName = 'backup_' . date('Ymd_His') . '.sql';
    $backupPath = __DIR__ . '/../../backups/' . $backupName;
    $dir = dirname($backupPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $command = 'C:/xampp/mysql/bin/mysqldump.exe --user=root --password= --database aiml_academichub > "' . $backupPath . '"';
    shell_exec($command);

    $size = file_exists($backupPath) ? filesize($backupPath) : 0;
    $status = file_exists($backupPath) && $size > 0 ? 'Completed' : 'Failed';
    $stmt = $pdo->prepare('INSERT INTO backup_logs (backup_name, file_path, size_bytes, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$backupName, $backupPath, $size, $status]);
    header('Location: index.php');
    exit;
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Backup Management</h3>
        <p class="text-muted mb-0">Manual and automatic backup controls for the portal database.</p>
    </div>
    <form method="post"><button class="btn btn-primary">Create Backup</button></form>
</div>
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Backup Name</th><th>Date</th><th>Size</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $backup): ?>
                    <tr>
                        <td><?= htmlspecialchars($backup['backup_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($backup['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format((int)$backup['size_bytes']) ?> bytes</td>
                        <td><span class="badge bg-<?= $backup['status'] === 'Completed' ? 'success' : 'warning' ?>"><?= htmlspecialchars($backup['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= url('/backups/' . basename($backup['file_path'])) ?>">Download</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
