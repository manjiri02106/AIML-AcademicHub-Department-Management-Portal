<?php
require_once __DIR__ . '/../../includes/functions.php';
requirePermission('users');

$pdo = getDbConnection();
$stmt = $pdo->query('SELECT id, full_name, email, mobile, department, designation, status FROM users ORDER BY id');
$users = $stmt->fetchAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=users.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Full Name', 'Email', 'Mobile', 'Department', 'Designation', 'Status']);
foreach ($users as $user) {
    fputcsv($output, [$user['id'], $user['full_name'], $user['email'], $user['mobile'], $user['department'], $user['designation'], $user['status']]);
}
fclose($output);
exit;
