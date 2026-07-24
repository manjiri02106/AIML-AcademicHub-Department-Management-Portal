<?php
/**
 * Student Dashboard - Projects & Internships
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();

// Fetch summary metrics
$stats = get_student_dashboard_stats($conn, $student_id);

// Fetch student details safely
$student_query = "SELECT * FROM students WHERE id = '$student_id'";
$student = safe_fetch_assoc($conn, $student_query);

// Fetch recent projects safely
$projects_sql = "SELECT p.*, f.name as guide_name FROM projects p 
                 LEFT JOIN faculty f ON p.guide_id = f.id 
                 WHERE p.created_by_student_id = '$student_id' 
                 ORDER BY p.id DESC LIMIT 5";
$projects_res = safe_query($conn, $projects_sql);

// Fetch recent internships safely
$internships_sql = "SELECT * FROM internships 
                    WHERE student_id = '$student_id' 
                    ORDER BY id DESC LIMIT 5";
$internships_res = safe_query($conn, $internships_sql);

// Fetch upcoming milestones safely
$milestones_sql = "SELECT m.*, p.title as project_title FROM milestones m 
                   JOIN projects p ON m.project_id = p.id 
                   WHERE p.created_by_student_id = '$student_id' AND m.status != 'Completed' 
                   ORDER BY m.due_date ASC LIMIT 4";
$milestones_res = safe_query($conn, $milestones_sql);

// Fetch notifications safely
$notif_sql = "SELECT * FROM notifications 
              WHERE user_type = 'student' AND user_id = '$student_id' 
              ORDER BY id DESC LIMIT 5";
$notif_res = safe_query($conn, $notif_sql);

// Include standard theme layout files
if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <?php display_flash_message(); ?>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> Projects & Internships Dashboard</h1>
            <p>Welcome back, <strong><?= htmlspecialchars($student['name'] ?? 'Aarav Sharma'); ?></strong> (<?= htmlspecialchars($student['roll_number'] ?? 'CS2026-001'); ?>) | <?= htmlspecialchars($student['department'] ?? 'AI & Machine Learning'); ?></p>
        </div>
        <div class="quick-actions">
            <a href="add_project.php" class="btn btn-navy"><i class="fas fa-plus-circle"></i> Add Project</a>
            <a href="add_internship.php" class="btn btn-teal"><i class="fas fa-briefcase"></i> Add Internship</a>
            <a href="progress_tracking.php" class="btn btn-outline"><i class="fas fa-tasks"></i> Track Progress</a>
        </div>
    </div>

    <!-- 8 Summary Cards Grid -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon"><i class="fas fa-folder-open"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['total_projects']; ?></div>
                <div class="metric-label">Total Projects</div>
            </div>
        </div>

        <div class="metric-card completed">
            <div class="metric-icon"><i class="fas fa-check-circle"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['completed_projects']; ?></div>
                <div class="metric-label">Completed Projects</div>
            </div>
        </div>

        <div class="metric-card ongoing">
            <div class="metric-icon"><i class="fas fa-sync-alt"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['ongoing_projects']; ?></div>
                <div class="metric-label">Ongoing Projects</div>
            </div>
        </div>

        <div class="metric-card pending">
            <div class="metric-icon"><i class="fas fa-clock"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['pending_approvals']; ?></div>
                <div class="metric-label">Pending Approvals</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['total_internships']; ?></div>
                <div class="metric-label">Total Internships</div>
            </div>
        </div>

        <div class="metric-card completed">
            <div class="metric-icon"><i class="fas fa-award"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['completed_internships']; ?></div>
                <div class="metric-label">Completed Internships</div>
            </div>
        </div>

        <div class="metric-card approved">
            <div class="metric-icon"><i class="fas fa-user-tie"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['guide_allocations']; ?></div>
                <div class="metric-label">Guide Allocations</div>
            </div>
        </div>

        <div class="metric-card ongoing">
            <div class="metric-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['upcoming_reviews']; ?></div>
                <div class="metric-label">Upcoming Reviews</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left Section: Tables -->
        <div>
            <!-- Recent Projects Table -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-project-diagram"></i> Recent Projects</span>
                    <a href="view_projects.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Project Title</th>
                                <th>Tech Stack</th>
                                <th>Guide</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($projects_res && mysqli_num_rows($projects_res) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($projects_res)): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['title']); ?></strong></td>
                                        <td><small><?= htmlspecialchars($row['technology_stack']); ?></small></td>
                                        <td><?= htmlspecialchars($row['guide_name'] ?: 'Not Assigned'); ?></td>
                                        <td><?= render_status_badge($row['status']); ?></td>
                                        <td>
                                            <a href="project_details.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-navy"><i class="fas fa-eye"></i> View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No projects found. Add your first project!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Internships Table -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-building"></i> Recent Internships</span>
                    <a href="view_internships.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Role</th>
                                <th>Mode</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($internships_res && mysqli_num_rows($internships_res) > 0): ?>
                                <?php while($irow = mysqli_fetch_assoc($internships_res)): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($irow['company_name']); ?></strong></td>
                                        <td><?= htmlspecialchars($irow['role']); ?></td>
                                        <td><?= htmlspecialchars($irow['mode']); ?></td>
                                        <td><?= htmlspecialchars($irow['duration']); ?></td>
                                        <td><?= render_status_badge($irow['status']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No internships registered.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Section: Milestones & Notifications -->
        <div>
            <!-- Upcoming Milestones -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-flag-checkered"></i> Upcoming Milestones</span>
                </div>
                <div>
                    <?php if ($milestones_res && mysqli_num_rows($milestones_res) > 0): ?>
                        <?php while($m = mysqli_fetch_assoc($milestones_res)): ?>
                            <div style="padding: 12px; border-bottom: 1px solid var(--light-sky-blue); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="display: block; color: var(--primary-navy);"><?= htmlspecialchars($m['milestone_name']); ?></strong>
                                    <small style="color: var(--text-muted);"><?= htmlspecialchars($m['project_title']); ?></small>
                                </div>
                                <div style="text-align: right;">
                                    <small style="display: block; font-weight: 600; color: var(--secondary-teal);"><i class="far fa-calendar"></i> <?= format_date($m['due_date']); ?></small>
                                    <?= render_status_badge($m['status']); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 14px;">No pending milestones.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notifications Panel -->
            <div class="glass-card">
                <div class="card-header">
                    <span class="card-title"><i class="fas fa-bell"></i> Recent Notifications</span>
                </div>
                <div>
                    <?php if ($notif_res && mysqli_num_rows($notif_res) > 0): ?>
                        <?php while($n = mysqli_fetch_assoc($notif_res)): ?>
                            <div class="notification-item">
                                <div class="notification-icon"><i class="fas fa-info"></i></div>
                                <div>
                                    <strong style="font-size: 13px; color: var(--primary-navy);"><?= htmlspecialchars($n['title']); ?></strong>
                                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;"><?= htmlspecialchars($n['message']); ?></p>
                                    <small style="color: #94a3b8; font-size: 10px;"><?= format_date($n['created_at']); ?></small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: var(--text-muted); text-align: center; padding: 14px;">No new notifications.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
