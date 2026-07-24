<?php
/**
 * Add New Internship - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$csrf_token = generate_csrf_token();
$errors = [];

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
    if (empty($start_date)) $errors[] = "Start Date is required.";
    if (empty($end_date)) $errors[] = "End Date is required.";

    // File Uploads
    $offer_letter_path = upload_file('offer_letter', 'documents');
    $certificate_path = upload_file('certificate', 'certificates');

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO internships 
            (student_id, company_name, role, duration, location, mode, stipend, start_date, end_date, offer_letter_path, certificate_path, company_website, supervisor_name, supervisor_contact, remarks, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("isssssssssssssss", 
            $student_id, $company_name, $role, $duration, $location, $mode, $stipend, 
            $start_date, $end_date, $offer_letter_path, $certificate_path, $company_website, 
            $supervisor_name, $supervisor_contact, $remarks, $status
        );

        if ($stmt->execute()) {
            $stmt->close();
            set_flash_message('success', "Internship at '$company_name' registered successfully!");
            header("Location: view_internships.php");
            exit;
        } else {
            $errors[] = "Database insertion error: " . $conn->error;
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
            <h1><i class="fas fa-briefcase"></i> Add New Internship</h1>
            <p>Register your corporate, industrial, or research internship records.</p>
        </div>
        <div>
            <a href="view_internships.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Internships</a>
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
                    <input type="text" id="company_name" name="company_name" class="form-control" placeholder="e.g. Google, TechCorp" required value="<?= htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="role">Role / Position *</label>
                    <input type="text" id="role" name="role" class="form-control" placeholder="e.g. Software Engineer Intern" required value="<?= htmlspecialchars($_POST['role'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="duration">Duration *</label>
                    <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g. 3 Months, 6 Months" required value="<?= htmlspecialchars($_POST['duration'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-control" placeholder="e.g. Bengaluru, Remote" required value="<?= htmlspecialchars($_POST['location'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="mode">Mode *</label>
                    <select id="mode" name="mode" class="form-control" required>
                        <option value="Offline">Offline (On-site)</option>
                        <option value="Online">Online (Remote)</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stipend">Stipend Details</label>
                    <input type="text" id="stipend" name="stipend" class="form-control" placeholder="e.g. ₹20,000/month or Unpaid" value="<?= htmlspecialchars($_POST['stipend'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= $_POST['start_date'] ?? date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="end_date">End Date *</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required value="<?= $_POST['end_date'] ?? date('Y-m-d', strtotime('+3 months')); ?>">
                </div>

                <div class="form-group">
                    <label for="company_website">Company Website</label>
                    <input type="url" id="company_website" name="company_website" class="form-control" placeholder="https://company.example.com" value="<?= htmlspecialchars($_POST['company_website'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Initial Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Pending">Pending Verification</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Approved">Approved</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supervisor_name">Supervisor / Industry Mentor Name</label>
                    <input type="text" id="supervisor_name" name="supervisor_name" class="form-control" placeholder="e.g. John Doe" value="<?= htmlspecialchars($_POST['supervisor_name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="supervisor_contact">Supervisor Email / Phone</label>
                    <input type="text" id="supervisor_contact" name="supervisor_contact" class="form-control" placeholder="e.g. john@company.com" value="<?= htmlspecialchars($_POST['supervisor_contact'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="offer_letter">Offer Letter Document Upload</label>
                    <input type="file" id="offer_letter" name="offer_letter" class="form-control" accept=".pdf,.doc,.docx">
                    <small style="color: var(--text-muted);">Upload official offer letter (PDF/Doc)</small>
                </div>

                <div class="form-group">
                    <label for="certificate">Completion Certificate Upload</label>
                    <input type="file" id="certificate" name="certificate" class="form-control" accept=".pdf,.doc,.docx,.png,.jpg">
                    <small style="color: var(--text-muted);">Upload completion certificate (If completed)</small>
                </div>

                <div class="form-group full-width">
                    <label for="remarks">Additional Remarks / Learnings</label>
                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Briefly describe key learnings or project responsibilities..."><?= htmlspecialchars($_POST['remarks'] ?? ''); ?></textarea>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-navy"><i class="fas fa-save"></i> Save Internship</button>
            </div>
        </form>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
