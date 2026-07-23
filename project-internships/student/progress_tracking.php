<?php
/**
 * Student Progress Tracking & Weekly Submission
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$csrf_token = generate_csrf_token();
$errors = [];

// Get selected project or default to student's active project
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

if ($project_id === 0) {
    $p_stmt = $conn->prepare("SELECT id FROM projects WHERE created_by_student_id = ? ORDER BY id DESC LIMIT 1");
    $p_stmt->bind_param("i", $student_id);
    $p_stmt->execute();
    $p_res = $p_stmt->get_result()->fetch_assoc();
    if ($p_res) {
        $project_id = (int)$p_res['id'];
    }
    $p_stmt->close();
}

// Fetch all student projects for selector dropdown
$my_projects_res = mysqli_query($conn, "SELECT id, title FROM projects WHERE created_by_student_id = '$student_id'");

// Fetch current project details
$project = null;
if ($project_id > 0) {
    $stmt = $conn->prepare("SELECT p.*, f.id as guide_id, f.name as guide_name FROM projects p LEFT JOIN faculty f ON p.guide_id = f.id WHERE p.id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch milestones for project
$milestones_res = null;
if ($project_id > 0) {
    $milestones_res = mysqli_query($conn, "SELECT * FROM milestones WHERE project_id = '$project_id' ORDER BY order_sequence ASC");
}

// Handle Form Submission for Weekly Progress Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_progress'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $post_project_id = (int)$_POST['project_id'];
    $milestone_id = !empty($_POST['milestone_id']) ? (int)$_POST['milestone_id'] : null;
    $week_number = (int)$_POST['week_number'];
    $progress_percent = (int)$_POST['progress_percent'];
    $work_submitted = sanitize_input($_POST['work_submitted'] ?? '');
    $comments = sanitize_input($_POST['comments'] ?? '');

    if ($week_number <= 0) $errors[] = "Valid Week Number is required.";
    if (empty($work_submitted)) $errors[] = "Work description is required.";

    $file_path = upload_file('progress_file', 'documents');

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO progress_tracking 
            (project_id, student_id, milestone_id, week_number, progress_percent, work_submitted, file_path, comments, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        
        $stmt->bind_param("iiiissss", 
            $post_project_id, $student_id, $milestone_id, $week_number, 
            $progress_percent, $work_submitted, $file_path, $comments
        );

        if ($stmt->execute()) {
            $stmt->close();

            // Notify Guide if assigned
            if ($project && $project['guide_id']) {
                add_notification($conn, 'faculty', $project['guide_id'], "New Progress Submission", "Student submitted Week $week_number progress for review.");
            }

            set_flash_message('success', "Week $week_number progress submitted successfully!");
            header("Location: progress_tracking.php?project_id=$post_project_id");
            exit;
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}

// Fetch past progress submissions
$submissions_res = null;
$avg_percent = 0;
if ($project_id > 0) {
    $submissions_res = mysqli_query($conn, "SELECT pt.*, m.milestone_name FROM progress_tracking pt 
                                           LEFT JOIN milestones m ON pt.milestone_id = m.id 
                                           WHERE pt.project_id = '$project_id' 
                                           ORDER BY pt.week_number DESC");
    
    $avg_sql = "SELECT AVG(progress_percent) as avg_p FROM progress_tracking WHERE project_id = '$project_id'";
    $avg_res = mysqli_query($conn, $avg_sql);
    $avg_percent = (int)(mysqli_fetch_assoc($avg_res)['avg_p'] ?? 0);
}

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <?php display_flash_message(); ?>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-tasks"></i> Student Progress Tracking</h1>
            <p>Submit weekly logs, track milestone progress percentage, and view faculty feedback.</p>
        </div>
        <div>
            <!-- Select Project Selector -->
            <form action="" method="GET" style="display: inline-block;">
                <select name="project_id" class="form-control" onchange="this.form.submit();" style="font-weight: 600; min-width: 250px;">
                    <?php if (mysqli_num_rows($my_projects_res) > 0): ?>
                        <?php while($p = mysqli_fetch_assoc($my_projects_res)): ?>
                            <option value="<?= $p['id']; ?>" <?= $project_id == $p['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($p['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No Projects Found</option>
                    <?php endif; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if ($project): ?>
        <!-- Project Progress Bar Card -->
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div>
                    <h3 style="color: var(--primary-navy); font-size: 18px;"><?= htmlspecialchars($project['title']); ?></h3>
                    <small style="color: var(--text-muted);">Guide: <strong><?= htmlspecialchars($project['guide_name'] ?: 'Not Assigned'); ?></strong></small>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 22px; font-weight: 700; color: var(--secondary-teal);"><?= $avg_percent; ?>%</span>
                    <span style="display: block; font-size: 11px; color: var(--text-muted);">Completion</span>
                </div>
            </div>
            <div class="progress-container">
                <div class="progress-bar" style="width: <?= $avg_percent; ?>%;"></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Left Column: Submit New Progress Log -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-edit"></i> Submit Weekly Progress Log</span>
                </div>

                <?php if (!empty($errors)): ?>
                    <div style="background: #ffebee; border: 1px solid #ef9a9a; color: #b71c1c; padding: 10px; border-radius: 8px; margin-bottom: 14px;">
                        <ul style="margin-left: 16px;">
                            <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e); ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">
                    <input type="hidden" name="project_id" value="<?= $project_id; ?>">

                    <div class="form-group">
                        <label for="week_number">Week Number *</label>
                        <input type="number" id="week_number" name="week_number" class="form-control" min="1" max="52" required value="<?= (mysqli_num_rows($submissions_res ?? []) + 1); ?>">
                    </div>

                    <div class="form-group">
                        <label for="milestone_id">Associated Milestone</label>
                        <select id="milestone_id" name="milestone_id" class="form-control">
                            <option value="">Select Milestone (Optional)</option>
                            <?php if ($milestones_res): ?>
                                <?php mysqli_data_seek($milestones_res, 0); ?>
                                <?php while($m = mysqli_fetch_assoc($milestones_res)): ?>
                                    <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['milestone_name']); ?> (<?= $m['status']; ?>)</option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="progress_range">Overall Estimated Completion: <span id="progress_value" style="color: var(--secondary-teal); font-weight: 700;">50%</span></label>
                        <input type="range" id="progress_range" name="progress_percent" min="0" max="100" value="50" style="width: 100%; accent-color: var(--primary-navy);">
                    </div>

                    <div class="form-group">
                        <label for="work_submitted">Work Description & Accomplishments *</label>
                        <textarea id="work_submitted" name="work_submitted" class="form-control" placeholder="Describe the modules coded, bugs fixed, or documentation written this week..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="comments">Additional Comments / Challenges Faced</label>
                        <input type="text" id="comments" name="comments" class="form-control" placeholder="e.g. Encountered issue with database connection pool.">
                    </div>

                    <div class="form-group">
                        <label for="progress_file">Attachment File (Code zip / Document)</label>
                        <input type="file" id="progress_file" name="progress_file" class="form-control" accept=".pdf,.doc,.docx,.zip">
                    </div>

                    <div style="text-align: right; margin-top: 18px;">
                        <button type="submit" name="submit_progress" class="btn btn-navy"><i class="fas fa-paper-plane"></i> Submit Log</button>
                    </div>
                </form>
            </div>

            <!-- Right Column: Submission History & Faculty Feedback -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-history"></i> Submission History & Feedback</span>
                </div>

                <div style="max-height: 550px; overflow-y: auto;">
                    <?php if ($submissions_res && mysqli_num_rows($submissions_res) > 0): ?>
                        <?php while($sub = mysqli_fetch_assoc($submissions_res)): ?>
                            <div style="background: var(--white); border: 1px solid var(--light-sky-blue); border-radius: var(--radius-md); padding: 16px; margin-bottom: 14px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="color: var(--primary-navy);">Week <?= $sub['week_number']; ?> Submission</strong>
                                    <?= render_status_badge($sub['status']); ?>
                                </div>

                                <?php if ($sub['milestone_name']): ?>
                                    <small style="color: var(--secondary-teal); display: block; margin-top: 2px;">Milestone: <strong><?= htmlspecialchars($sub['milestone_name']); ?></strong></small>
                                <?php endif; ?>

                                <p style="color: var(--text-dark); margin: 8px 0; font-size: 13px;"><?= htmlspecialchars($sub['work_submitted']); ?></p>

                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--text-muted);">
                                    <span><i class="far fa-clock"></i> <?= format_date($sub['submitted_at']); ?></span>
                                    <span>Progress: <strong><?= $sub['progress_percent']; ?>%</strong></span>
                                </div>

                                <?php if ($sub['file_path']): ?>
                                    <div style="margin-top: 6px;">
                                        <a href="../<?= htmlspecialchars($sub['file_path']); ?>" target="_blank" style="font-size: 11px;"><i class="fas fa-paperclip"></i> Download Submitted File</a>
                                    </div>
                                <?php endif; ?>

                                <?php if ($sub['faculty_remarks']): ?>
                                    <div style="background: #f1f5f9; border-left: 3px solid var(--primary-navy); padding: 8px 12px; margin-top: 10px; border-radius: 0 6px 6px 0;">
                                        <small style="font-weight: 600; color: var(--primary-navy); display: block;"><i class="fas fa-comment-dots"></i> Faculty Remarks:</small>
                                        <small style="color: var(--text-dark);"><?= htmlspecialchars($sub['faculty_remarks']); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 24px;">No progress logs submitted yet for this project.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-card" style="text-align: center; padding: 40px;">
            <h3>No Active Projects Found</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Please create a project first before submitting weekly progress.</p>
            <a href="add_project.php" class="btn btn-navy"><i class="fas fa-plus"></i> Add Project</a>
        </div>
    <?php endif; ?>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
