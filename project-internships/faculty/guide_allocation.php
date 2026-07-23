<?php
/**
 * Guide Allocation - Faculty Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$faculty_id = get_active_faculty_id();
$csrf_token = generate_csrf_token();
$errors = [];

// Handle Guide Allocation / Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_guide'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $student_id = (int)$_POST['student_id'];
    $selected_faculty_id = (int)$_POST['faculty_id'];
    $project_id = !empty($_POST['project_id']) ? (int)$_POST['project_id'] : null;
    $department = sanitize_input($_POST['department'] ?? '');
    $allocation_date = sanitize_input($_POST['allocation_date'] ?? date('Y-m-d'));
    $status = sanitize_input($_POST['status'] ?? 'Active');

    if ($student_id <= 0) $errors[] = "Please select a student.";
    if ($selected_faculty_id <= 0) $errors[] = "Please select a guide.";

    if (empty($errors)) {
        // Check if allocation already exists
        $check_stmt = $conn->prepare("SELECT id FROM guide_allocations WHERE student_id = ?");
        $check_stmt->bind_param("i", $student_id);
        $check_stmt->execute();
        $existing = $check_stmt->get_result()->fetch_assoc();
        $check_stmt->close();

        if ($existing) {
            // Update existing allocation
            $stmt = $conn->prepare("UPDATE guide_allocations SET faculty_id = ?, project_id = ?, department = ?, allocation_date = ?, status = ? WHERE student_id = ?");
            $stmt->bind_param("iisssi", $selected_faculty_id, $project_id, $department, $allocation_date, $status, $student_id);
        } else {
            // Insert new allocation
            $stmt = $conn->prepare("INSERT INTO guide_allocations (student_id, project_id, faculty_id, department, allocation_date, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiisss", $student_id, $project_id, $selected_faculty_id, $department, $allocation_date, $status);
        }

        if ($stmt->execute()) {
            $stmt->close();

            // Also update guide_id on projects table if project selected
            if ($project_id) {
                // Fetch guide name
                $fn_res = mysqli_query($conn, "SELECT name FROM faculty WHERE id = '$selected_faculty_id'");
                $fn_name = mysqli_fetch_assoc($fn_res)['name'] ?? '';
                
                $p_stmt = $conn->prepare("UPDATE projects SET guide_id = ?, guide_name = ? WHERE id = ?");
                $p_stmt->bind_param("isi", $selected_faculty_id, $fn_name, $project_id);
                $p_stmt->execute();
                $p_stmt->close();
            }

            // Send notification to student
            add_notification($conn, 'student', $student_id, "Guide Allocated", "A faculty guide has been allocated for your academic project.");

            set_flash_message('success', 'Faculty guide allocation saved successfully!');
            header("Location: guide_allocation.php");
            exit;
        } else {
            $errors[] = "Database operation error: " . $conn->error;
        }
    }
}

// Fetch list of all allocations
$alloc_sql = "SELECT ga.*, s.name as student_name, s.roll_number, s.department as student_dept, 
              f.name as guide_name, f.designation as guide_desig, p.title as project_title 
              FROM guide_allocations ga 
              JOIN students s ON ga.student_id = s.id 
              JOIN faculty f ON ga.faculty_id = f.id 
              LEFT JOIN projects p ON ga.project_id = p.id 
              ORDER BY ga.id DESC";
$alloc_res = mysqli_query($conn, $alloc_sql);

// Fetch unallocated/all students dropdown
$students_res = mysqli_query($conn, "SELECT id, name, roll_number, department FROM students ORDER BY name ASC");

// Fetch faculty dropdown
$faculty_list_res = mysqli_query($conn, "SELECT id, name, department, designation FROM faculty ORDER BY name ASC");

// Fetch projects dropdown
$projects_list_res = mysqli_query($conn, "SELECT id, title, created_by_student_id FROM projects ORDER BY title ASC");

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="../assets/css/style.css">
<script src="../assets/js/script.js" defer></script>

<div class="module-container">
    <?php display_flash_message(); ?>

    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-check"></i> Project Guide Allocation</h1>
            <p>Assign faculty mentors to students and monitor project guidance mappings.</p>
        </div>
        <div>
            <button class="btn btn-navy" onclick="openModal('allocationModal');"><i class="fas fa-plus"></i> New Allocation</button>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control table-search-input" data-table="allocationTable" placeholder="Search by student name, roll number, project or guide...">
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="allocationTable" data-col="2">
                <option value="">All Departments</option>
                <option value="AI & Machine Learning">AI & Machine Learning</option>
                <option value="Computer Science">Computer Science</option>
                <option value="Information Technology">Information Technology</option>
            </select>
        </div>
        <div style="min-width: 160px;">
            <select class="form-control table-filter-select" data-table="allocationTable" data-col="5">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Completed">Completed</option>
                <option value="Pending">Pending</option>
            </select>
        </div>
    </div>

    <!-- Guide Allocations Table -->
    <div class="glass-card">
        <div class="table-responsive">
            <table class="custom-table" id="allocationTable">
                <thead>
                    <tr>
                        <th>Student Name & Roll</th>
                        <th>Project Title</th>
                        <th>Department</th>
                        <th>Assigned Guide</th>
                        <th>Allocation Date</th>
                        <th>Status</th>
                        <th class="no-export">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($alloc_res && mysqli_num_rows($alloc_res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($alloc_res)): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['student_name']); ?></strong>
                                    <br><small style="color: var(--text-muted);"><?= htmlspecialchars($row['roll_number']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['project_title'] ?: 'No Project Assigned Yet'); ?></td>
                                <td><?= htmlspecialchars($row['student_dept']); ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['guide_name']); ?></strong>
                                    <br><small style="color: var(--text-muted);"><?= htmlspecialchars($row['guide_desig']); ?></small>
                                </td>
                                <td><small><i class="far fa-calendar-alt"></i> <?= format_date($row['allocation_date']); ?></small></td>
                                <td><?= render_status_badge($row['status']); ?></td>
                                <td class="no-export">
                                    <button class="btn btn-sm btn-teal" onclick="editAllocation(<?= htmlspecialchars(json_encode($row)); ?>);"><i class="fas fa-edit"></i> Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">No guide allocations recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form for Guide Allocation -->
<div class="modal-overlay" id="allocationModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Guide Allocation</h3>
            <button class="modal-close" onclick="closeModal('allocationModal');">&times;</button>
        </div>
        <form action="" method="POST">
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token; ?>">

                <div class="form-group">
                    <label for="student_id">Select Student *</label>
                    <select id="student_id" name="student_id" class="form-control" required>
                        <option value="">Choose Student</option>
                        <?php while($st = mysqli_fetch_assoc($students_res)): ?>
                            <option value="<?= $st['id']; ?>" data-dept="<?= htmlspecialchars($st['department']); ?>">
                                <?= htmlspecialchars($st['name']); ?> (<?= htmlspecialchars($st['roll_number']); ?>) - <?= htmlspecialchars($st['department']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="faculty_id">Select Faculty Guide *</label>
                    <select id="faculty_id" name="faculty_id" class="form-control" required>
                        <option value="">Choose Faculty Guide</option>
                        <?php while($fac = mysqli_fetch_assoc($faculty_list_res)): ?>
                            <option value="<?= $fac['id']; ?>"><?= htmlspecialchars($fac['name']); ?> (<?= htmlspecialchars($fac['department']); ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="project_id">Assign Project (Optional)</label>
                    <select id="project_id" name="project_id" class="form-control">
                        <option value="">Select Project</option>
                        <?php while($prj = mysqli_fetch_assoc($projects_list_res)): ?>
                            <option value="<?= $prj['id']; ?>"><?= htmlspecialchars($prj['title']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" class="form-control" value="AI & Machine Learning" required>
                </div>

                <div class="form-group">
                    <label for="allocation_date">Allocation Date</label>
                    <input type="date" id="allocation_date" name="allocation_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Allocation Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Active" selected>Active</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('allocationModal');">Cancel</button>
                <button type="submit" name="assign_guide" class="btn btn-navy"><i class="fas fa-save"></i> Save Allocation</button>
            </div>
        </form>
    </div>
</div>

<script>
function editAllocation(data) {
    document.getElementById('student_id').value = data.student_id;
    document.getElementById('faculty_id').value = data.faculty_id;
    if (data.project_id) document.getElementById('project_id').value = data.project_id;
    document.getElementById('department').value = data.department;
    document.getElementById('allocation_date').value = data.allocation_date;
    document.getElementById('status').value = data.status;
    openModal('allocationModal');
}
</script>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
