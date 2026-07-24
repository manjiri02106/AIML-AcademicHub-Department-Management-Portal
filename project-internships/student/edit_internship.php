<?php
/**
 * Edit Internship - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$csrf_token = generate_csrf_token();
$errors = [];

$internship_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch internship
$stmt = $conn->prepare("SELECT * FROM internships WHERE id = ? AND student_id = ?");
$stmt->bind_param("ii", $internship_id, $student_id);
$stmt->execute();
$internship = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$internship) {
    set_flash_message('error', 'Internship record not found.');
    header("Location: view_internships.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $company_name = sanitize_input($_POST['company_name'] ?? '');
    $role = sanitize_input($_POST['role'] ?? '');
    $duration = sanitize_input($_POST['duration'] ?? '');
    $location = sanitize_input($_POST['location'] ?? '');
    $mode = sanitize_input($_POST['mode'] ?? 'Offline');
    $stipend = sanitize_input($_POST['stipend'] ?? 'Unpaid');
    $start_date = sanitize_input($_POST['start_date'] ?? '');
    $end_date = sanitize_input($_POST['end_date'] ?? '');
    $company_website = sanitize_input($_POST['company_website'] ?? '');
    $supervisor_name = sanitize_input($_POST['supervisor_name'] ?? '');
    $supervisor_contact = sanitize_input($_POST['supervisor_contact'] ?? '');
    $remarks = sanitize_input($_POST['remarks'] ?? '');
    $status = sanitize_input($_POST['status'] ?? 'Pending');

    if (empty($company_name)) $errors[] = "Company Name is required.";
    if (empty($role)) $errors[] = "Role is required.";

    // File Uploads
    $offer_letter_path = upload_file('offer_letter', 'documents') ?: $internship['offer_letter_path'];
    $certificate_path = upload_file('certificate', 'certificates') ?: $internship['certificate_path'];

    if (empty($errors)) {
        $update_stmt = $conn->prepare("UPDATE internships SET 
            company_name = ?, role = ?, duration = ?, location = ?, mode = ?, stipend = ?, 
            start_date = ?, end_date = ?, offer_letter_path = ?, certificate_path = ?, 
            company_website = ?, supervisor_name = ?, supervisor_contact = ?, remarks = ?, status = ? 
            WHERE id = ? AND student_id = ?");
        
        $update_stmt->bind_param("sssssssssssssssii", 
            $company_name, $role, $duration, $location, $mode, $stipend, 
            $start_date, $end_date, $offer_letter_path, $certificate_path, 
            $company_website, $supervisor_name, $supervisor_contact, $remarks, $status, 
            $internship_id, $student_id
        );

        if ($update_stmt->execute()) {
            $update_stmt->close();
            set_flash_message('success', "Internship record for '$company_name' updated successfully!");
            header("Location: view_internships.php");
            exit;
        } else {
            $errors[] = "Database update error: " . $conn->error;
        }
    }
}

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-edit"></i> Edit Internship: <?= htmlspecialchars($internship['company_name']); ?></h1>
            <p>Update company parameters, stipend, uploaded offer letter or certificate.</p>
        </div>
        <div>
            <a href="view_internships.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancel</a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div style="background: #ffebee; border: 1px solid #ef9a9a; color: #b71c1c; padding: 14px; border-radius: 10px; margin-bottom: 20px;">
            <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
                <?php foreach($errors as $err): ?>
                    <li><?= htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="glass-card">
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="company_name">Company Name *</label>
                    <input type="text" id="company_name" name="company_name" class="form-control" required value="<?= htmlspecialchars($_POST['company_name'] ?? $internship['company_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="role">Role / Position *</label>
                    <input type="text" id="role" name="role" class="form-control" required value="<?= htmlspecialchars($_POST['role'] ?? $internship['role']); ?>">
                </div>

                <div class="form-group">
                    <label for="duration">Duration *</label>
                    <input type="text" id="duration" name="duration" class="form-control" required value="<?= htmlspecialchars($_POST['duration'] ?? $internship['duration']); ?>">
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" required value="<?= htmlspecialchars($_POST['location'] ?? $internship['location']); ?>">
                </div>

                <div class="form-group">
                    <label for="mode">Mode *</label>
                    <select id="mode" name="mode" class="form-control" required>
                        <option value="Offline" <?= $internship['mode'] === 'Offline' ? 'selected' : ''; ?>>Offline (On-site)</option>
                        <option value="Online" <?= $internship['mode'] === 'Online' ? 'selected' : ''; ?>>Online (Remote)</option>
                        <option value="Hybrid" <?= $internship['mode'] === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stipend">Stipend Details</label>
                    <input type="text" id="stipend" name="stipend" class="form-control" value="<?= htmlspecialchars($_POST['stipend'] ?? $internship['stipend']); ?>">
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= $_POST['start_date'] ?? $internship['start_date']; ?>">
                </div>

                <div class="form-group">
                    <label for="end_date">End Date *</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required value="<?= $_POST['end_date'] ?? $internship['end_date']; ?>">
                </div>

                <div class="form-group">
                    <label for="company_website">Company Website</label>
                    <input type="url" id="company_website" name="company_website" class="form-control" value="<?= htmlspecialchars($_POST['company_website'] ?? $internship['company_website']); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Pending" <?= $internship['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?= $internship['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Ongoing" <?= $internship['status'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="Completed" <?= $internship['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supervisor_name">Supervisor Name</label>
                    <input type="text" id="supervisor_name" name="supervisor_name" class="form-control" value="<?= htmlspecialchars($_POST['supervisor_name'] ?? $internship['supervisor_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="supervisor_contact">Supervisor Contact</label>
                    <input type="text" id="supervisor_contact" name="supervisor_contact" class="form-control" value="<?= htmlspecialchars($_POST['supervisor_contact'] ?? $internship['supervisor_contact']); ?>">
                </div>

                <div class="form-group">
                    <label for="offer_letter">Replace Offer Letter</label>
                    <input type="file" id="offer_letter" name="offer_letter" class="form-control" accept=".pdf,.doc,.docx">
                    <?php if ($internship['offer_letter_path']): ?>
                        <small style="color: var(--secondary-teal);">Current: <a href="../<?= htmlspecialchars($internship['offer_letter_path']); ?>" target="_blank">Download Offer Letter</a></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="certificate">Replace Completion Certificate</label>
                    <input type="file" id="certificate" name="certificate" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg">
                    <?php if ($internship['certificate_path']): ?>
                        <small style="color: var(--secondary-teal);">Current: <a href="../<?= htmlspecialchars($internship['certificate_path']); ?>" target="_blank">Download Certificate</a></small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" class="form-control"><?= htmlspecialchars($_POST['remarks'] ?? $internship['remarks']); ?></textarea>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-navy"><i class="fas fa-sync"></i> Update Internship Record</button>
            </div>
        </form>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
