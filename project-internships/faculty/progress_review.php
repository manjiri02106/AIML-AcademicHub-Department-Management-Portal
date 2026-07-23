<?php
/**
 * Faculty Progress Review & Milestone Evaluation
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$faculty_id = get_active_faculty_id();
$csrf_token = generate_csrf_token();

// Handle Review Submission Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_progress'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $progress_id = (int)$_POST['progress_id'];
    $status = sanitize_input($_POST['status'] ?? 'Approved');
    $faculty_remarks = sanitize_input($_POST['faculty_remarks'] ?? '');
    $milestone_status = sanitize_input($_POST['milestone_status'] ?? '');
    $milestone_id = !empty($_POST['milestone_id']) ? (int)$_POST['milestone_id'] : null;

    $stmt = $conn->prepare("UPDATE progress_tracking SET status = ?, faculty_remarks = ?, reviewed_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $status, $faculty_remarks, $progress_id);
    
    if ($stmt->execute()) {
        $stmt->close();

        // Update associated milestone status if selected
        if ($milestone_id && !empty($milestone_status)) {
            $m_stmt = $conn->prepare("UPDATE milestones SET status = ? WHERE id = ?");
            $m_stmt->bind_param("si", $milestone_status, $milestone_id);
            $m_stmt->execute();
            $m_stmt->close();
        }

        // Send notification to student
        $st_res = mysqli_query($conn, "SELECT student_id, week_number FROM progress_tracking WHERE id = '$progress_id'");
        $st_info = mysqli_fetch_assoc($st_res);
        if ($st_info) {
            $w_num = $st_info['week_number'];
            $msg = "Your Week $w_num progress submission has been $status by your guide. Feedback: $faculty_remarks";
            add_notification($conn, 'student', $st_info['student_id'], "Progress Log Reviewed", $msg);
        }

        set_flash_message('success', "Progress log review saved successfully!");
        header("Location: progress_review.php");
        exit;
    } else {
        set_flash_message('error', "Review update failed: " . $conn->error);
    }
}

// Fetch all progress tracking logs
$sql = "SELECT pt.*, p.title as project_title, s.name as student_name, s.roll_number, m.milestone_name, m.id as m_id, m.status as m_status 
        FROM progress_tracking pt 
        JOIN projects p ON pt.project_id = p.id 
        JOIN students s ON pt.student_id = s.id 
        LEFT JOIN milestones m ON pt.milestone_id = m.id 
        ORDER BY FIELD(pt.status, 'Pending', 'Approved', 'Rejected'), pt.id DESC";
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
            <h1><i class="fas fa-tasks"></i> Student Progress Review Portal</h1>
            <p>Review weekly student progress logs, evaluate milestones, and provide mentor feedback.</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control table-search-input" data-table="progressReviewTable" placeholder="Search student name, roll number, or project title...">
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="progressReviewTable" data-col="4">
                <option value="">All Review Statuses</option>
                <option value="Pending">Pending Review</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="custom-table" id="progressReviewTable">
                <thead>
                    <tr>
                        <th>Student & Roll</th>
                        <th>Project & Milestone</th>
                        <th>Week</th>
                        <th>Work Submitted</th>
                        <th>Status</th>
                        <th>Submitted On</th>
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
                                    <strong><?= htmlspecialchars($row['project_title']); ?></strong>
                                    <?php if ($row['milestone_name']): ?>
                                        <br><small style="color: var(--secondary-teal);">Milestone: <?= htmlspecialchars($row['milestone_name']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span style="background: var(--light-sky-blue); color: var(--primary-navy); padding: 2px 8px; border-radius: 4px; font-weight: 600;">Week <?= $row['week_number']; ?></span></td>
                                <td>
                                    <p style="font-size: 12px; color: var(--text-dark); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= htmlspecialchars($row['work_submitted']); ?>
                                    </p>
                                    <?php if ($row['file_path']): ?>
                                        <a href="../<?= htmlspecialchars($row['file_path']); ?>" target="_blank" style="font-size: 11px;"><i class="fas fa-paperclip"></i> Attachment</a>
                                    <?php endif; ?>
                                </td>
                                <td><?= render_status_badge($row['status']); ?></td>
                                <td><small><i class="far fa-clock"></i> <?= format_date($row['submitted_at']); ?></small></td>
                                <td class="no-export">
                                    <button class="btn btn-sm btn-navy" onclick="reviewLog(<?= htmlspecialchars(json_encode($row)); ?>);"><i class="fas fa-comment-dots"></i> Review</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">No weekly progress submissions found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Progress Review Modal -->
<div class="modal-overlay" id="progressReviewModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-clipboard-list"></i> Progress Review & Feedback</h3>
            <button class="modal-close" onclick="closeModal('progressReviewModal');">&times;</button>
        </div>
        <form action="" method="POST">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                <input type="hidden" name="progress_id" id="modal_progress_id">
                <input type="hidden" name="milestone_id" id="modal_milestone_id">

                <div style="background: var(--bg-beige); padding: 14px; border-radius: 8px; margin-bottom: 16px;">
                    <strong style="color: var(--primary-navy);" id="modal_student_proj"></strong>
                    <div style="margin-top: 8px; font-size: 13px; color: var(--text-dark);" id="modal_work_body"></div>
                </div>

                <div class="form-group">
                    <label for="modal_review_status">Weekly Log Status *</label>
                    <select id="modal_review_status" name="status" class="form-control" required>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_milestone_status">Update Milestone Status</label>
                    <select id="modal_milestone_status" name="milestone_status" class="form-control">
                        <option value="">No Change</option>
                        <option value="Completed">Completed</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Approved">Approved</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty_remarks">Faculty Guidance & Remarks</label>
                    <textarea id="faculty_remarks" name="faculty_remarks" class="form-control" placeholder="Provide feedback, suggestions or corrections for next week..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('progressReviewModal');">Cancel</button>
                <button type="submit" name="review_progress" class="btn btn-navy"><i class="fas fa-save"></i> Save Feedback</button>
            </div>
        </form>
    </div>
</div>

<script>
function reviewLog(data) {
    document.getElementById('modal_progress_id').value = data.id;
    document.getElementById('modal_milestone_id').value = data.m_id || '';
    document.getElementById('modal_student_proj').textContent = 'Student: ' + data.student_name + ' | Week ' + data.week_number;
    document.getElementById('modal_work_body').textContent = 'Submitted Work: ' + data.work_submitted;
    document.getElementById('modal_review_status').value = data.status;
    if (data.m_status) document.getElementById('modal_milestone_status').value = data.m_status;
    document.getElementById('faculty_remarks').value = data.faculty_remarks || '';
    openModal('progressReviewModal');
}
</script>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
