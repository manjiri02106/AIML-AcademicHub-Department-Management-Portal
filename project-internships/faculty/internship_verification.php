<?php
/**
 * Internship Verification - Faculty Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$faculty_id = get_active_faculty_id();
$csrf_token = generate_csrf_token();

// Handle Internship Verification Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_internship'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $internship_id = (int)$_POST['internship_id'];
    $status = sanitize_input($_POST['status'] ?? 'Approved');
    $remarks = sanitize_input($_POST['remarks'] ?? '');

    $stmt = $conn->prepare("UPDATE internships SET status = ?, remarks = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $remarks, $internship_id);
    
    if ($stmt->execute()) {
        $stmt->close();

        // Fetch student id for notification
        $i_res = mysqli_query($conn, "SELECT student_id, company_name FROM internships WHERE id = '$internship_id'");
        $i_info = mysqli_fetch_assoc($i_res);

        if ($i_info) {
            $student_id = $i_info['student_id'];
            $company = $i_info['company_name'];
            $msg = "Your internship at '$company' verification status is now '$status'. Remarks: $remarks";
            add_notification($conn, 'student', $student_id, "Internship Verification Update", $msg);
        }

        set_flash_message('success', "Internship verification status updated to '$status'!");
        header("Location: internship_verification.php");
        exit;
    } else {
        set_flash_message('error', "Verification update failed: " . $conn->error);
    }
}

// Fetch all student internships for verification
$sql = "SELECT i.*, s.name as student_name, s.roll_number, s.department as student_dept, s.email as student_email 
        FROM internships i 
        JOIN students s ON i.student_id = s.id 
        ORDER BY FIELD(i.status, 'Pending', 'Ongoing', 'Approved', 'Completed', 'Rejected'), i.id DESC";
$res = mysqli_query($conn, $sql);

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <?php display_flash_message(); ?>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-file-signature"></i> Internship Document Verification</h1>
            <p>Verify corporate offer letters, completion certificates, and student internship details.</p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control table-search-input" data-table="verificationTable" placeholder="Search student, roll number, company name or location...">
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="verificationTable" data-col="5">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="custom-table" id="verificationTable">
                <thead>
                    <tr>
                        <th>Student Name & Roll</th>
                        <th>Company & Role</th>
                        <th>Mode & Duration</th>
                        <th>Supervisor Details</th>
                        <th>Uploaded Docs</th>
                        <th>Status</th>
                        <th class="no-export">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['student_name']); ?></strong>
                                    <br><small style="color: var(--text-muted);"><?= htmlspecialchars($row['roll_number']); ?> (<?= htmlspecialchars($row['student_dept']); ?>)</small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['company_name']); ?></strong>
                                    <br><small style="color: var(--text-muted);"><?= htmlspecialchars($row['role']); ?></small>
                                </td>
                                <td>
                                    <span style="background: var(--light-sky-blue); color: var(--primary-navy); padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;"><?= htmlspecialchars($row['mode']); ?></span>
                                    <br><small><?= htmlspecialchars($row['duration']); ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($row['supervisor_name'] ?: 'N/A'); ?><br><?= htmlspecialchars($row['supervisor_contact'] ?: ''); ?></small>
                                </td>
                                <td>
                                    <?php if ($row['offer_letter_path']): ?>
                                        <a href="../<?= htmlspecialchars($row['offer_letter_path']); ?>" target="_blank" style="font-size: 11px;"><i class="fas fa-file-pdf"></i> Offer Letter</a>
                                    <?php endif; ?>
                                    <?php if ($row['certificate_path']): ?>
                                        <br><a href="../<?= htmlspecialchars($row['certificate_path']); ?>" target="_blank" style="font-size: 11px; color: var(--secondary-teal);"><i class="fas fa-certificate"></i> Certificate</a>
                                    <?php endif; ?>
                                </td>
                                <td><?= render_status_badge($row['status']); ?></td>
                                <td class="no-export">
                                    <button class="btn btn-sm btn-teal" onclick="verifyInternship(<?= htmlspecialchars(json_encode($row)); ?>);"><i class="fas fa-user-check"></i> Verify</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">No internship submissions requiring verification.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal-overlay" id="verifyModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-user-check"></i> Internship Verification</h3>
            <button class="modal-close" onclick="closeModal('verifyModal');">&times;</button>
        </div>
        <form action="" method="POST">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                <input type="hidden" name="internship_id" id="modal_internship_id">

                <div style="background: var(--bg-beige); padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                    <strong style="color: var(--primary-navy);" id="modal_company_title"></strong>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;" id="modal_student_info"></p>
                </div>

                <div class="form-group">
                    <label for="modal_verification_status">Verification Status *</label>
                    <select id="modal_verification_status" name="status" class="form-control" required>
                        <option value="Approved">Approved</option>
                        <option value="Completed">Completed</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="remarks">Faculty Remarks / Verification Notes</label>
                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter remarks regarding offer letter authenticity, stipend, or completion status..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('verifyModal');">Cancel</button>
                <button type="submit" name="verify_internship" class="btn btn-navy"><i class="fas fa-check"></i> Save Verification</button>
            </div>
        </form>
    </div>
</div>

<script>
function verifyInternship(data) {
    document.getElementById('modal_internship_id').value = data.id;
    document.getElementById('modal_company_title').textContent = data.company_name + ' - ' + data.role;
    document.getElementById('modal_student_info').textContent = 'Student: ' + data.student_name + ' (' + data.roll_number + ')';
    document.getElementById('modal_verification_status').value = data.status;
    document.getElementById('remarks').value = data.remarks || '';
    openModal('verifyModal');
}
</script>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
