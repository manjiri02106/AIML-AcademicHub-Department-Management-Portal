<?php
/**
 * Dynamic Reports Generator - Faculty Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$report_type = sanitize_input($_GET['report_type'] ?? 'project');
$filter_dept = sanitize_input($_GET['department'] ?? '');
$filter_status = sanitize_input($_GET['status'] ?? '');
$filter_year = sanitize_input($_GET['academic_year'] ?? '');
$filter_guide = sanitize_input($_GET['guide_id'] ?? '');
$filter_company = sanitize_input($_GET['company_name'] ?? '');

// Fetch dropdown data
$depts_res = mysqli_query($conn, "SELECT DISTINCT department FROM students WHERE department != ''");
$faculty_res = mysqli_query($conn, "SELECT id, name FROM faculty ORDER BY name ASC");
$companies_res = mysqli_query($conn, "SELECT DISTINCT company_name FROM internships WHERE company_name != ''");

// Build dynamic report queries based on report_type
$report_title = "Academic Projects Report";
$report_rows = [];

if ($report_type === 'project') {
    $report_title = "Student Projects Master Report";
    $where = [];
    if ($filter_dept) $where[] = "p.department = '$filter_dept'";
    if ($filter_status) $where[] = "p.status = '$filter_status'";
    if ($filter_guide) $where[] = "p.guide_id = '$filter_guide'";

    $w_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $sql = "SELECT p.*, s.name as student_name, s.roll_number, s.academic_year, f.name as guide_name 
            FROM projects p 
            JOIN students s ON p.created_by_student_id = s.id 
            LEFT JOIN faculty f ON p.guide_id = f.id 
            $w_clause ORDER BY p.id DESC";
    $report_rows = mysqli_query($conn, $sql);

} else if ($report_type === 'internship') {
    $report_title = "Student Internships Master Report";
    $where = [];
    if ($filter_dept) $where[] = "s.department = '$filter_dept'";
    if ($filter_status) $where[] = "i.status = '$filter_status'";
    if ($filter_company) $where[] = "i.company_name = '$filter_company'";

    $w_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $sql = "SELECT i.*, s.name as student_name, s.roll_number, s.department as student_dept, s.academic_year 
            FROM internships i 
            JOIN students s ON i.student_id = s.id 
            $w_clause ORDER BY i.id DESC";
    $report_rows = mysqli_query($conn, $sql);

} else if ($report_type === 'guide_allocation') {
    $report_title = "Faculty Guide Allocation Report";
    $where = [];
    if ($filter_dept) $where[] = "ga.department = '$filter_dept'";
    if ($filter_status) $where[] = "ga.status = '$filter_status'";
    if ($filter_guide) $where[] = "ga.faculty_id = '$filter_guide'";

    $w_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $sql = "SELECT ga.*, s.name as student_name, s.roll_number, f.name as guide_name, f.designation, p.title as project_title 
            FROM guide_allocations ga 
            JOIN students s ON ga.student_id = s.id 
            JOIN faculty f ON ga.faculty_id = f.id 
            LEFT JOIN projects p ON ga.project_id = p.id 
            $w_clause ORDER BY ga.id DESC";
    $report_rows = mysqli_query($conn, $sql);

} else if ($report_type === 'progress') {
    $report_title = "Student Weekly Progress Report";
    $where = [];
    if ($filter_status) $where[] = "pt.status = '$filter_status'";
    if ($filter_guide) $where[] = "p.guide_id = '$filter_guide'";

    $w_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $sql = "SELECT pt.*, p.title as project_title, s.name as student_name, s.roll_number, m.milestone_name 
            FROM progress_tracking pt 
            JOIN projects p ON pt.project_id = p.id 
            JOIN students s ON pt.student_id = s.id 
            LEFT JOIN milestones m ON pt.milestone_id = m.id 
            $w_clause ORDER BY pt.id DESC";
    $report_rows = mysqli_query($conn, $sql);
}

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-bar"></i> Reports & Analytics Generator</h1>
            <p>Export comprehensive project, internship, guide allocation, and weekly progress reports.</p>
        </div>
        <div class="quick-actions">
            <button onclick="exportTableToCSV('reportResultTable', '<?= $report_type; ?>_report.csv');" class="btn btn-teal"><i class="fas fa-file-excel"></i> Export Excel (CSV)</button>
            <button onclick="printReport();" class="btn btn-navy"><i class="fas fa-print"></i> Print / Save PDF</button>
        </div>
    </div>

    <!-- Filter Generator Bar -->
    <div class="glass-card">
        <form action="" method="GET">
            <div class="form-grid">
                <div class="form-group">
                    <label for="report_type">Report Type *</label>
                    <select id="report_type" name="report_type" class="form-control" onchange="this.form.submit();">
                        <option value="project" <?= $report_type === 'project' ? 'selected' : ''; ?>>Project Master Report</option>
                        <option value="internship" <?= $report_type === 'internship' ? 'selected' : ''; ?>>Internship Report</option>
                        <option value="guide_allocation" <?= $report_type === 'guide_allocation' ? 'selected' : ''; ?>>Guide Allocation Report</option>
                        <option value="progress" <?= $report_type === 'progress' ? 'selected' : ''; ?>>Progress Tracking Report</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" class="form-control">
                        <option value="">All Departments</option>
                        <?php while($d = mysqli_fetch_assoc($depts_res)): ?>
                            <option value="<?= htmlspecialchars($d['department']); ?>" <?= $filter_dept === $d['department'] ? 'selected' : ''; ?>><?= htmlspecialchars($d['department']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Approved" <?= $filter_status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Completed" <?= $filter_status === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="Ongoing" <?= $filter_status === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Rejected" <?= $filter_status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="guide_id">Faculty Guide</label>
                    <select id="guide_id" name="guide_id" class="form-control">
                        <option value="">All Faculty</option>
                        <?php while($fac = mysqli_fetch_assoc($faculty_res)): ?>
                            <option value="<?= $fac['id']; ?>" <?= $filter_guide == $fac['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($fac['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div style="text-align: right; margin-top: 14px;">
                <button type="submit" class="btn btn-navy"><i class="fas fa-filter"></i> Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Generated Report Result Table -->
    <div class="glass-card">
        <div class="card-header">
            <span class="card-title"><i class="fas fa-table"></i> <?= $report_title; ?></span>
            <small style="color: var(--text-muted);">Generated on <?= date('d M, Y H:i'); ?></small>
        </div>

        <div class="table-responsive">
            <table class="custom-table" id="reportResultTable">
                <?php if ($report_type === 'project'): ?>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Project Title</th>
                            <th>Department</th>
                            <th>Tech Stack</th>
                            <th>Assigned Guide</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($report_rows && mysqli_num_rows($report_rows) > 0): ?>
                            <?php while($r = mysqli_fetch_assoc($report_rows)): ?>
                                <tr>
                                    <td>#PRJ-<?= $r['id']; ?></td>
                                    <td><strong><?= htmlspecialchars($r['student_name']); ?></strong></td>
                                    <td><?= htmlspecialchars($r['roll_number']); ?></td>
                                    <td><?= htmlspecialchars($r['title']); ?></td>
                                    <td><?= htmlspecialchars($r['department']); ?></td>
                                    <td><?= htmlspecialchars($r['technology_stack']); ?></td>
                                    <td><?= htmlspecialchars($r['guide_name'] ?: 'Unassigned'); ?></td>
                                    <td><?= render_status_badge($r['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center; color: var(--text-muted);">No records found matching criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>

                <?php elseif ($report_type === 'internship'): ?>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Company Name</th>
                            <th>Role</th>
                            <th>Mode</th>
                            <th>Duration</th>
                            <th>Stipend</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($report_rows && mysqli_num_rows($report_rows) > 0): ?>
                            <?php while($r = mysqli_fetch_assoc($report_rows)): ?>
                                <tr>
                                    <td>#INT-<?= $r['id']; ?></td>
                                    <td><strong><?= htmlspecialchars($r['student_name']); ?></strong></td>
                                    <td><?= htmlspecialchars($r['roll_number']); ?></td>
                                    <td><?= htmlspecialchars($r['company_name']); ?></td>
                                    <td><?= htmlspecialchars($r['role']); ?></td>
                                    <td><?= htmlspecialchars($r['mode']); ?></td>
                                    <td><?= htmlspecialchars($r['duration']); ?></td>
                                    <td><?= htmlspecialchars($r['stipend']); ?></td>
                                    <td><?= render_status_badge($r['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9" style="text-align: center; color: var(--text-muted);">No records found matching criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>

                <?php elseif ($report_type === 'guide_allocation'): ?>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Project Title</th>
                            <th>Allocated Faculty Guide</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Allocation Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($report_rows && mysqli_num_rows($report_rows) > 0): ?>
                            <?php while($r = mysqli_fetch_assoc($report_rows)): ?>
                                <tr>
                                    <td>#ALC-<?= $r['id']; ?></td>
                                    <td><strong><?= htmlspecialchars($r['student_name']); ?></strong></td>
                                    <td><?= htmlspecialchars($r['roll_number']); ?></td>
                                    <td><?= htmlspecialchars($r['project_title'] ?: 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($r['guide_name']); ?></td>
                                    <td><?= htmlspecialchars($r['designation']); ?></td>
                                    <td><?= htmlspecialchars($r['department']); ?></td>
                                    <td><?= format_date($r['allocation_date']); ?></td>
                                    <td><?= render_status_badge($r['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="9" style="text-align: center; color: var(--text-muted);">No records found matching criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>

                <?php elseif ($report_type === 'progress'): ?>
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Student Name</th>
                            <th>Project Title</th>
                            <th>Week #</th>
                            <th>Milestone</th>
                            <th>Progress %</th>
                            <th>Work Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($report_rows && mysqli_num_rows($report_rows) > 0): ?>
                            <?php while($r = mysqli_fetch_assoc($report_rows)): ?>
                                <tr>
                                    <td>#LOG-<?= $r['id']; ?></td>
                                    <td><strong><?= htmlspecialchars($r['student_name']); ?></strong></td>
                                    <td><?= htmlspecialchars($r['project_title']); ?></td>
                                    <td>Week <?= $r['week_number']; ?></td>
                                    <td><?= htmlspecialchars($r['milestone_name'] ?: 'General Log'); ?></td>
                                    <td><strong><?= $r['progress_percent']; ?>%</strong></td>
                                    <td><?= htmlspecialchars($r['work_submitted']); ?></td>
                                    <td><?= render_status_badge($r['status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align: center; color: var(--text-muted);">No records found matching criteria.</td></tr>
                        <?php endif; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
