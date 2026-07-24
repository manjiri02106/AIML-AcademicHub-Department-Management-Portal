<?php
require_once __DIR__ . '/../../includes/header_admin.php';
requirePermission('settings');

$pdo = getDbConnection();
$settings = $pdo->query('SELECT * FROM department_settings ORDER BY id DESC LIMIT 1')->fetch();
if (!$settings) {
    $settings = [];
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO department_settings (department_name, department_code, institute_name, address, email, phone, academic_year, semester, divisions, sections, subjects, course_types, theme_color, footer_text, smtp_email, sms_enabled, push_enabled) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE department_name = VALUES(department_name), department_code = VALUES(department_code), institute_name = VALUES(institute_name), address = VALUES(address), email = VALUES(email), phone = VALUES(phone), academic_year = VALUES(academic_year), semester = VALUES(semester), divisions = VALUES(divisions), sections = VALUES(sections), subjects = VALUES(subjects), course_types = VALUES(course_types), theme_color = VALUES(theme_color), footer_text = VALUES(footer_text), smtp_email = VALUES(smtp_email), sms_enabled = VALUES(sms_enabled), push_enabled = VALUES(push_enabled)');
    $stmt->execute([
        $_POST['department_name'] ?? '',
        $_POST['department_code'] ?? '',
        $_POST['institute_name'] ?? '',
        $_POST['address'] ?? '',
        $_POST['email'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['academic_year'] ?? '',
        $_POST['semester'] ?? '',
        $_POST['divisions'] ?? '',
        $_POST['sections'] ?? '',
        $_POST['subjects'] ?? '',
        $_POST['course_types'] ?? '',
        $_POST['theme_color'] ?? '',
        $_POST['footer_text'] ?? '',
        $_POST['smtp_email'] ?? '',
        (int)($_POST['sms_enabled'] ?? 0),
        (int)($_POST['push_enabled'] ?? 0),
    ]);
    $message = 'Settings saved successfully.';
    $settings = $pdo->query('SELECT * FROM department_settings ORDER BY id DESC LIMIT 1')->fetch();
}
?>
<div class="card shadow-sm border-0 p-4">
    <h3 class="mb-3">Master Settings</h3>
    <?php if ($message !== ''): ?><div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Department Name</label><input type="text" name="department_name" class="form-control" value="<?= htmlspecialchars($settings['department_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Department Code</label><input type="text" name="department_code" class="form-control" value="<?= htmlspecialchars($settings['department_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Institute Name</label><input type="text" name="institute_name" class="form-control" value="<?= htmlspecialchars($settings['institute_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Address</label><input type="text" name="address" class="form-control" value="<?= htmlspecialchars($settings['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?= htmlspecialchars($settings['academic_year'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Semester</label><input type="text" name="semester" class="form-control" value="<?= htmlspecialchars($settings['semester'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Divisions</label><input type="text" name="divisions" class="form-control" value="<?= htmlspecialchars($settings['divisions'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Sections</label><input type="text" name="sections" class="form-control" value="<?= htmlspecialchars($settings['sections'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Subjects</label><input type="text" name="subjects" class="form-control" value="<?= htmlspecialchars($settings['subjects'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Course Types</label><input type="text" name="course_types" class="form-control" value="<?= htmlspecialchars($settings['course_types'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Theme Color</label><input type="color" name="theme_color" class="form-control form-control-color" value="<?= htmlspecialchars($settings['theme_color'] ?? '#0ea5e9', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">Footer Text</label><input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($settings['footer_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-6"><label class="form-label">SMTP Email</label><input type="email" name="smtp_email" class="form-control" value="<?= htmlspecialchars($settings['smtp_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
            <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="sms_enabled" value="1" <?= !empty($settings['sms_enabled']) ? 'checked' : '' ?>><label class="form-check-label">SMS Toggle</label></div></div>
            <div class="col-md-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="push_enabled" value="1" <?= !empty($settings['push_enabled']) ? 'checked' : '' ?>><label class="form-check-label">Push Notification</label></div></div>
        </div>
        <button class="btn btn-primary mt-4">Save Changes</button>
    </form>
</div>
<?php require_once __DIR__ . '/../../includes/footer_admin.php'; ?>
