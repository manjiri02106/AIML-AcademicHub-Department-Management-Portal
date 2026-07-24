<?php
/**
 * Project Approval & Review - Faculty Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$faculty_id = get_active_faculty_id();
$csrf_token = generate_csrf_token();

// Handle Project Approval/Rejection Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $project_id = (int)$_POST['project_id'];
    $status = sanitize_input($_POST['status'] ?? 'Approved');
    $guide_id = !empty($_POST['guide_id']) ? (int)$_POST['guide_id'] : $faculty_id;
    $remarks = sanitize_input($_POST['remarks'] ?? '');

    // Get guide name
    $g_res = mysqli_query($conn, "SELECT name FROM faculty WHERE id = '$guide_id'");
    $guide_name = mysqli_fetch_assoc($g_res)['name'] ?? null;

    $stmt = $conn->prepare("UPDATE projects SET status = ?, guide_id = ?, guide_name = ? WHERE id = ?");
    $stmt->bind_param("sisi", $status, $guide_id, $guide_name, $project_id);
    
    if ($stmt->execute()) {
        $stmt->close();

        // Get student id of project creator
        $st_res = mysqli_query($conn, "SELECT created_by_student_id, title FROM projects WHERE id = '$project_id'");
        $p_info = mysqli_fetch_assoc($st_res);

        if ($p_info) {
            $student_id = $p_info['created_by_student_id'];
            $title = $p_info['title'];
            $msg = "Your project '$title' status has been updated to '$status'. Remarks: $remarks";
            add_notification($conn, 'student', $student_id, "Project Review Update", $msg);
        }

        set_flash_message('success', "Project status updated to '$status' successfully!");
        header("Location: project_approval.php");
        exit;
    } else {
        set_flash_message('error', "Failed to update status: " . $conn->error);
    }
}

// Fetch all projects for review
$sql = "SELECT p.*, s.name as student_name, s.roll_number, s.email as student_email, f.name as guide_name 
        FROM projects p 
        JOIN students s ON p.created_by_student_id = s.id 
        LEFT JOIN faculty f ON p.guide_id = f.id 
        ORDER BY FIELD(p.status, 'Pending', 'Ongoing', 'Approved', 'Completed', 'Rejected'), p.id DESC";
$res = mysqli_query($conn, $sql);

// Faculty list dropdown for reassignment
$fac_list_res = mysqli_query($conn, "SELECT id, name, department FROM faculty ORDER BY name ASC");

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <?php display_flash_message(); ?>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-check-double"></i> Student Project Approvals</h1>
            <p>Review submitted student project proposals, assign faculty guides, and approve/reject submissions.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control table-search-input" data-table="projectApprovalTable" placeholder="Search student name, roll number, project title or tech stack...">
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="projectApprovalTable" data-col="3">
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
            <table class="custom-table" id="projectApprovalTable">
                <thead>
                    <tr>
                        <th>Student Name & Roll</th>
                        <th>Project Title & Abstract</th>
                        <th>Tech Stack</th>
                        <th>Status</th>
                        <th>Assigned Guide</th>
                        <th>Deliverables</th>
                        <th class="no-export">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['student_name']); ?></strong>
                                    <br><small style="color: var(--text-muted);"><?= htmlspecialchars($row['roll_number']); ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['title']); ?></strong>
                                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 2px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= htmlspecialchars($row['abstract']); ?>
                                    </p>
                                </td>
                                <td><span style="background: var(--light-sky-blue); color: var(--primary-navy); padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 500;"><?= htmlspecialchars($row['technology_stack']); ?></span></td>
                                <td><?= render_status_badge($row['status']); ?></td>
                                <td><?= htmlspecialchars($row['guide_name'] ?: 'Unassigned'); ?></td>
                                <td>
                                    <?php if ($row['document_path']): ?>
                                        <a href="../<?= htmlspecialchars($row['document_path']); ?>" target="_blank" style="font-size: 11px;"><i class="fas fa-file-pdf"></i> Doc</a>
                                    <?php endif; ?>
                                    <?php if ($row['github_link']): ?>
                                        &nbsp;|&nbsp;<a href="<?= htmlspecialchars($row['github_link']); ?>" target="_blank" style="font-size: 11px;"><i class="fab fa-github"></i> Git</a>
                                    <?php endif; ?>
                                </td>
                                <td class="no-export">
                                    <button class="btn btn-sm btn-navy" onclick="reviewProject(<?= htmlspecialchars(json_encode($row)); ?>);"><i class="fas fa-edit"></i> Review</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">No projects submitted for review.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal-overlay" id="reviewModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-clipboard-check"></i> Review Project Proposal</h3>
            <button class="modal-close" onclick="closeModal('reviewModal');">&times;</button>
        </div>
        <form action="" method="POST">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                <input type="hidden" name="project_id" id="modal_project_id">

                <div style="background: var(--bg-beige); padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                    <strong style="color: var(--primary-navy);" id="modal_project_title"></strong>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;" id="modal_student_name"></p>
                </div>

                <div class="form-group">
                    <label for="modal_status">Project Decision / Status *</label>
                    <select id="modal_status" name="status" class="form-control" required>
                        <option value="Approved">Approved</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_guide_id">Assign / Reassign Faculty Guide</label>
                    <select id="modal_guide_id" name="guide_id" class="form-control">
                        <option value="">Select Faculty Guide</option>
                        <?php while($f = mysqli_fetch_assoc($fac_list_res)): ?>
                            <option value="<?= $f['id']; ?>"><?= htmlspecialchars($f['name']); ?> (<?= htmlspecialchars($f['department']); ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="remarks">Faculty Remarks / Feedback for Student</label>
                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Provide constructive comments or required modifications..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('reviewModal');">Cancel</button>
                <button type="submit" name="update_status" class="btn btn-navy"><i class="fas fa-save"></i> Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
function reviewProject(data) {
    document.getElementById('modal_project_id').value = data.id;
    document.getElementById('modal_project_title').textContent = data.title;
    document.getElementById('modal_student_name').textContent = 'Submitted by: ' + data.student_name + ' (' + data.roll_number + ')';
    document.getElementById('modal_status').value = data.status;
    if (data.guide_id) document.getElementById('modal_guide_id').value = data.guide_id;
    openModal('reviewModal');
}
</script>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
