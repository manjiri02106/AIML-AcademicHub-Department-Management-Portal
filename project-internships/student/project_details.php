<?php
/**
 * Project Details - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch project
$stmt = $conn->prepare("SELECT p.*, f.name as guide_name, f.email as guide_email, f.department as guide_dept 
                        FROM projects p 
                        LEFT JOIN faculty f ON p.guide_id = f.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    set_flash_message('error', 'Project not found.');
    header("Location: view_projects.php");
    exit;
}

// Fetch team members
$tm_sql = "SELECT ptm.*, s.name, s.roll_number, s.email, s.department FROM project_team_members ptm 
           JOIN students s ON ptm.student_id = s.id 
           WHERE ptm.project_id = '$project_id'";
$tm_res = mysqli_query($conn, $tm_sql);

// Fetch milestones
$m_sql = "SELECT * FROM milestones WHERE project_id = '$project_id' ORDER BY order_sequence ASC";
$m_res = mysqli_query($conn, $m_sql);

// Fetch progress history
$prog_sql = "SELECT pt.*, m.milestone_name FROM progress_tracking pt 
             LEFT JOIN milestones m ON pt.milestone_id = m.id 
             WHERE pt.project_id = '$project_id' ORDER BY pt.week_number DESC";
$prog_res = mysqli_query($conn, $prog_sql);

// Calculate overall progress percentage
$calc_sql = "SELECT AVG(progress_percent) as avg_prog FROM progress_tracking WHERE project_id = '$project_id'";
$calc_res = mysqli_query($conn, $calc_sql);
$avg_prog = (int)(mysqli_fetch_assoc($calc_res)['avg_prog'] ?? 0);

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-microchip"></i> <?= htmlspecialchars($project['title']); ?></h1>
            <p>Project ID: #PRJ-<?= sprintf('%04d', $project['id']); ?> | Created on <?= format_date($project['created_at']); ?></p>
        </div>
        <div class="quick-actions">
            <a href="progress_tracking.php?project_id=<?= $project['id']; ?>" class="btn btn-teal"><i class="fas fa-tasks"></i> Submit Weekly Progress</a>
            <a href="edit_project.php?id=<?= $project['id']; ?>" class="btn btn-navy"><i class="fas fa-edit"></i> Edit Project</a>
            <a href="view_projects.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <!-- Overall Progress Header -->
    <div class="glass-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-weight: 600; color: var(--primary-navy);">Overall Project Completion Progress</span>
            <span style="font-weight: 700; color: var(--secondary-teal); font-size: 16px;"><?= $avg_prog; ?>%</span>
        </div>
        <div class="progress-container">
            <div class="progress-bar" style="width: <?= $avg_prog; ?>%;"></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Main Details -->
        <div>
            <!-- Abstract & Overview -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-align-left"></i> Project Abstract</span>
                    <?= render_status_badge($project['status']); ?>
                </div>
                <p style="color: var(--text-dark); line-height: 1.8; font-size: 14px; white-space: pre-line;">
                    <?= htmlspecialchars($project['abstract']); ?>
                </p>
                <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <span style="background: var(--light-sky-blue); color: var(--primary-navy); padding: 6px 14px; border-radius: 20px; font-weight: 600;">
                        <i class="fas fa-code"></i> Stack: <?= htmlspecialchars($project['technology_stack']); ?>
                    </span>
                    <span style="background: var(--bg-beige); color: var(--text-dark); padding: 6px 14px; border-radius: 20px; font-weight: 600; border: 1px solid var(--light-sky-blue);">
                        <i class="fas fa-building"></i> Dept: <?= htmlspecialchars($project['department']); ?>
                    </span>
                </div>
            </div>

            <!-- Documents & Resources -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-paperclip"></i> Deliverables & External Links</span>
                </div>
                <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                    <?php if ($project['github_link']): ?>
                        <a href="<?= htmlspecialchars($project['github_link']); ?>" target="_blank" class="btn btn-navy">
                            <i class="fab fa-github"></i> GitHub Repository
                        </a>
                    <?php endif; ?>

                    <?php if ($project['document_path']): ?>
                        <a href="../<?= htmlspecialchars($project['document_path']); ?>" target="_blank" class="btn btn-teal">
                            <i class="fas fa-file-pdf"></i> Download Project Doc / Zip
                        </a>
                    <?php endif; ?>

                    <?php if ($project['images_path']): ?>
                        <a href="../<?= htmlspecialchars($project['images_path']); ?>" target="_blank" class="btn btn-outline">
                            <i class="fas fa-image"></i> View Diagram Preview
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Milestones Timeline -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-tasks"></i> Project Milestones Timeline</span>
                </div>
                <div class="timeline">
                    <?php if ($m_res && mysqli_num_rows($m_res) > 0): ?>
                        <?php while($m = mysqli_fetch_assoc($m_res)): ?>
                            <div class="timeline-item <?= strtolower($m['status']) === 'completed' ? 'completed' : ''; ?>">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <strong style="color: var(--primary-navy); font-size: 15px;"><?= htmlspecialchars($m['milestone_name']); ?></strong>
                                        <?= render_status_badge($m['status']); ?>
                                    </div>
                                    <p style="color: var(--text-muted); font-size: 13px; margin: 6px 0;"><?= htmlspecialchars($m['description']); ?></p>
                                    <small style="color: var(--secondary-teal); font-weight: 600;"><i class="far fa-calendar-alt"></i> Due Date: <?= format_date($m['due_date']); ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Details: Guide & Team Members -->
        <div>
            <!-- Faculty Guide Info -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-user-tie"></i> Assigned Faculty Guide</span>
                </div>
                <?php if ($project['guide_name']): ?>
                    <div style="text-align: center; padding: 10px;">
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary-navy); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 12px;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4 style="color: var(--primary-navy);"><?= htmlspecialchars($project['guide_name']); ?></h4>
                        <p style="color: var(--text-muted); font-size: 12px;"><?= htmlspecialchars($project['guide_dept']); ?></p>
                        <small style="color: var(--secondary-teal); display: block; margin-top: 6px;"><i class="fas fa-envelope"></i> <?= htmlspecialchars($project['guide_email']); ?></small>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-muted); text-align: center;">No faculty guide assigned yet.</p>
                <?php endif; ?>
            </div>

            <!-- Team Members -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-users"></i> Project Team</span>
                </div>
                <?php if ($tm_res && mysqli_num_rows($tm_res) > 0): ?>
                    <?php while($tm = mysqli_fetch_assoc($tm_res)): ?>
                        <div style="padding: 10px 0; border-bottom: 1px solid var(--light-sky-blue); display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="display: block; color: var(--primary-navy); font-size: 13px;"><?= htmlspecialchars($tm['name']); ?></strong>
                                <small style="color: var(--text-muted);"><?= htmlspecialchars($tm['roll_number']); ?></small>
                            </div>
                            <span style="font-size: 11px; background: var(--light-sky-blue); color: var(--primary-navy); padding: 2px 8px; border-radius: 10px; font-weight: 600;">
                                <?= htmlspecialchars($tm['role']); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
