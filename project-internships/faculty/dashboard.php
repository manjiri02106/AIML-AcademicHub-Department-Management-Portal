<?php
/**
 * Faculty Dashboard - Projects & Internships
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$faculty_id = get_active_faculty_id();

// Fetch faculty details safely
$fac_query = "SELECT * FROM faculty WHERE id = '$faculty_id'";
$faculty = safe_fetch_assoc($conn, $fac_query);

// Faculty Statistics
$stats = [
    'allocated_students' => 0,
    'pending_project_approvals' => 0,
    'pending_internship_verifications' => 0,
    'progress_reviews_due' => 0
];

// Allocated students count
$a_res = safe_query($conn, "SELECT COUNT(*) as cnt FROM guide_allocations WHERE faculty_id = '$faculty_id' AND status = 'Active'");
if ($a_res && $row = mysqli_fetch_assoc($a_res)) $stats['allocated_students'] = (int)$row['cnt'];

// Pending project approvals
$p_res = safe_query($conn, "SELECT COUNT(*) as cnt FROM projects WHERE (guide_id = '$faculty_id' OR guide_id IS NULL) AND status = 'Pending'");
if ($p_res && $row = mysqli_fetch_assoc($p_res)) $stats['pending_project_approvals'] = (int)$row['cnt'];

// Pending internship verifications
$i_res = safe_query($conn, "SELECT COUNT(*) as cnt FROM internships WHERE status = 'Pending'");
if ($i_res && $row = mysqli_fetch_assoc($i_res)) $stats['pending_internship_verifications'] = (int)$row['cnt'];

// Progress reviews due
$pr_res = safe_query($conn, "SELECT COUNT(*) as cnt FROM progress_tracking pt JOIN projects p ON pt.project_id = p.id WHERE p.guide_id = '$faculty_id' AND pt.status = 'Pending'");
if ($pr_res && $row = mysqli_fetch_assoc($pr_res)) $stats['progress_reviews_due'] = (int)$row['cnt'];

// Fetch recent projects awaiting approval safely
$projects_sql = "SELECT p.*, s.name as student_name, s.roll_number FROM projects p 
                 JOIN students s ON p.created_by_student_id = s.id 
                 WHERE (p.guide_id = '$faculty_id' OR p.guide_id IS NULL) 
                 ORDER BY p.id DESC LIMIT 5";
$projects_res = safe_query($conn, $projects_sql);

// Fetch recent internship submissions safely
$internships_sql = "SELECT i.*, s.name as student_name, s.roll_number, s.department FROM internships i 
                    JOIN students s ON i.student_id = s.id 
                    ORDER BY i.id DESC LIMIT 5";
$internships_res = safe_query($conn, $internships_sql);

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <?php display_flash_message(); ?>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-chalkboard-teacher"></i> Faculty Management Portal</h1>
            <p>Welcome, <strong><?= htmlspecialchars($faculty['name'] ?? 'Dr. Rajesh Deshmukh'); ?></strong> (<?= htmlspecialchars($faculty['designation'] ?? 'Professor & Head'); ?>) | <?= htmlspecialchars($faculty['department'] ?? 'AI & Machine Learning'); ?></p>
        </div>
        <div class="quick-actions">
            <a href="guide_allocation.php" class="btn btn-navy"><i class="fas fa-user-check"></i> Assign Guides</a>
            <a href="project_approval.php" class="btn btn-teal"><i class="fas fa-check-double"></i> Review Projects</a>
            <a href="internship_verification.php" class="btn btn-outline"><i class="fas fa-file-signature"></i> Verify Internships</a>
            <a href="reports.php" class="btn btn-navy"><i class="fas fa-chart-bar"></i> Generate Reports</a>
        </div>
    </div>

    <!-- Faculty Metrics Grid -->
    <div class="metrics-grid">
        <div class="metric-card approved">
            <div class="metric-icon"><i class="fas fa-user-friends"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['allocated_students']; ?></div>
                <div class="metric-label">Allocated Mentees</div>
            </div>
        </div>

        <div class="metric-card pending">
            <div class="metric-icon"><i class="fas fa-clock"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['pending_project_approvals']; ?></div>
                <div class="metric-label">Pending Projects</div>
            </div>
        </div>

        <div class="metric-card ongoing">
            <div class="metric-icon"><i class="fas fa-file-invoice"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['pending_internship_verifications']; ?></div>
                <div class="metric-label">Internships To Verify</div>
            </div>
        </div>

        <div class="metric-card completed">
            <div class="metric-icon"><i class="fas fa-tasks"></i></div>
            <div class="metric-info">
                <div class="metric-value"><?= $stats['progress_reviews_due']; ?></div>
                <div class="metric-label">Progress Reviews Due</div>
            </div>
        </div>
    </div>

    <!-- Tables Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Projects Needing Approval -->
        <div class="glass-card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-project-diagram"></i> Projects Awaiting Review</span>
                <a href="project_approval.php" class="btn btn-sm btn-outline">Manage All</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Project Title</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($projects_res && mysqli_num_rows($projects_res) > 0): ?>
                            <?php while($p = mysqli_fetch_assoc($projects_res)): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($p['student_name']); ?></strong>
                                        <br><small><?= htmlspecialchars($p['roll_number']); ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($p['title']); ?></td>
                                    <td><?= render_status_badge($p['status']); ?></td>
                                    <td>
                                        <a href="project_approval.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-navy">Review</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No pending project reviews.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Internship Applications -->
        <div class="glass-card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-building"></i> Recent Internship Submissions</span>
                <a href="internship_verification.php" class="btn btn-sm btn-outline">Verify All</a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Company & Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($internships_res && mysqli_num_rows($internships_res) > 0): ?>
                            <?php while($i = mysqli_fetch_assoc($internships_res)): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($i['student_name']); ?></strong>
                                        <br><small><?= htmlspecialchars($i['roll_number']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($i['company_name']); ?></strong>
                                        <br><small><?= htmlspecialchars($i['role']); ?></small>
                                    </td>
                                    <td><?= render_status_badge($i['status']); ?></td>
                                    <td>
                                        <a href="internship_verification.php?id=<?= $i['id']; ?>" class="btn btn-sm btn-teal">Verify</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No internship submissions.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
