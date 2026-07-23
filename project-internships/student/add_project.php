<?php
/**
 * Add New Project - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$csrf_token = generate_csrf_token();
$errors = [];

// Fetch faculty list for guide dropdown
$faculty_sql = "SELECT id, name, department, designation FROM faculty ORDER BY name ASC";
$faculty_res = mysqli_query($conn, $faculty_sql);

// Fetch students list for team member selection
$students_sql = "SELECT id, name, roll_number FROM students WHERE id != '$student_id' ORDER BY name ASC";
$students_res = mysqli_query($conn, $students_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $title = sanitize_input($_POST['title'] ?? '');
    $abstract = sanitize_input($_POST['abstract'] ?? '');
    $department = sanitize_input($_POST['department'] ?? '');
    $technology_stack = sanitize_input($_POST['technology_stack'] ?? '');
    $guide_id = !empty($_POST['guide_id']) ? (int)$_POST['guide_id'] : null;
    $github_link = sanitize_input($_POST['github_link'] ?? '');
    $start_date = sanitize_input($_POST['start_date'] ?? '');
    $expected_completion_date = sanitize_input($_POST['expected_completion_date'] ?? '');
    $status = sanitize_input($_POST['status'] ?? 'Pending');
    $team_members = $_POST['team_members'] ?? [];

    // Form Validations
    if (empty($title)) $errors[] = "Project Title is required.";
    if (empty($abstract)) $errors[] = "Project Abstract is required.";
    if (empty($department)) $errors[] = "Department is required.";
    if (empty($technology_stack)) $errors[] = "Technology Stack is required.";
    if (empty($start_date)) $errors[] = "Start Date is required.";
    if (empty($expected_completion_date)) $errors[] = "Expected Completion Date is required.";

    // File Uploads
    $doc_path = upload_file('project_file', 'documents');
    $img_path = upload_file('images_file', 'images');

    // Get guide name
    $guide_name = null;
    if ($guide_id) {
        $g_stmt = $conn->prepare("SELECT name FROM faculty WHERE id = ?");
        $g_stmt->bind_param("i", $guide_id);
        $g_stmt->execute();
        $g_res = $g_stmt->get_result()->fetch_assoc();
        if ($g_res) {
            $guide_name = $g_res['name'];
        }
        $g_stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO projects 
            (title, abstract, department, technology_stack, guide_name, guide_id, github_link, document_path, images_path, start_date, expected_completion_date, status, created_by_student_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssssissssssi", 
            $title, 
            $abstract, 
            $department, 
            $technology_stack, 
            $guide_name, 
            $guide_id, 
            $github_link, 
            $doc_path, 
            $img_path, 
            $start_date, 
            $expected_completion_date, 
            $status, 
            $student_id
        );

        if ($stmt->execute()) {
            $project_id = $stmt->insert_id;
            $stmt->close();

            // Insert primary student as team lead
            $lead_stmt = $conn->prepare("INSERT INTO project_team_members (project_id, student_id, role) VALUES (?, ?, 'Team Lead')");
            $lead_stmt->bind_param("ii", $project_id, $student_id);
            $lead_stmt->execute();
            $lead_stmt->close();

            // Insert selected team members
            if (!empty($team_members) && is_array($team_members)) {
                $tm_stmt = $conn->prepare("INSERT INTO project_team_members (project_id, student_id, role) VALUES (?, ?, 'Team Member')");
                foreach ($team_members as $tm_id) {
                    $tm_id = (int)$tm_id;
                    $tm_stmt->bind_param("ii", $project_id, $tm_id);
                    $tm_stmt->execute();
                }
                $tm_stmt->close();
            }

            // Create default initial milestones
            $milestones = [
                ['Proposal & Requirements', 'Submission of problem statement and scope.', date('Y-m-d', strtotime('+14 days')), 1],
                ['Design & Architecture', 'DB Schema, UI Wireframes, and System Architecture.', date('Y-m-d', strtotime('+30 days')), 2],
                ['Implementation & Testing', 'Core logic implementation and unit testing.', date('Y-m-d', strtotime('+60 days')), 3],
                ['Documentation & Final Review', 'Project report presentation.', date('Y-m-d', strtotime('+90 days')), 4]
            ];

            $m_stmt = $conn->prepare("INSERT INTO milestones (project_id, milestone_name, description, due_date, status, order_sequence) VALUES (?, ?, ?, ?, 'Pending', ?)");
            foreach ($milestones as $m) {
                $m_stmt->bind_param("isssi", $project_id, $m[0], $m[1], $m[2], $m[3]);
                $m_stmt->execute();
            }
            $m_stmt->close();

            // Notify faculty if guide selected
            if ($guide_id) {
                add_notification($conn, 'faculty', $guide_id, 'New Project Approval Required', "Student project '$title' requires guide review.");
            }

            set_flash_message('success', "Project '$title' created successfully!");
            header("Location: view_projects.php");
            exit;
        } else {
            $errors[] = "Database insertion error: " . $stmt->error;
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
            <h1><i class="fas fa-plus-circle"></i> Add New Student Project</h1>
            <p>Fill in details to register your academic project and assign guide & team members.</p>
        </div>
        <div>
            <a href="view_projects.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Projects</a>
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
                <div class="form-group full-width">
                    <label for="title">Project Title *</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="e.g. AI-Powered Academic Hub Portal" required value="<?= htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>

                <div class="form-group full-width">
                    <label for="abstract">Project Abstract *</label>
                    <textarea id="abstract" name="abstract" class="form-control" placeholder="Brief explanation of project scope, objectives, and deliverables..." required><?= htmlspecialchars($_POST['abstract'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="department">Department *</label>
                    <select id="department" name="department" class="form-control" required>
                        <option value="">Select Department</option>
                        <option value="AI & Machine Learning" selected>AI & Machine Learning</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Electronics & Telecom">Electronics & Telecom</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="technology_stack">Technology Stack *</label>
                    <input type="text" id="technology_stack" name="technology_stack" class="form-control" placeholder="e.g. Python, PHP, MySQL, React" required value="<?= htmlspecialchars($_POST['technology_stack'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="guide_id">Project Guide (Faculty)</label>
                    <select id="guide_id" name="guide_id" class="form-control">
                        <option value="">Select Faculty Guide</option>
                        <?php while($f = mysqli_fetch_assoc($faculty_res)): ?>
                            <option value="<?= $f['id']; ?>"><?= htmlspecialchars($f['name']); ?> (<?= htmlspecialchars($f['department']); ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="github_link">GitHub Repository Link</label>
                    <input type="url" id="github_link" name="github_link" class="form-control" placeholder="https://github.com/username/repo" value="<?= htmlspecialchars($_POST['github_link'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= $_POST['start_date'] ?? date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="expected_completion_date">Expected Completion Date *</label>
                    <input type="date" id="expected_completion_date" name="expected_completion_date" class="form-control" required value="<?= $_POST['expected_completion_date'] ?? date('Y-m-d', strtotime('+3 months')); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Initial Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Pending" selected>Pending Approval</option>
                        <option value="Ongoing">Ongoing</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="project_file">Project Document / Zip Upload</label>
                    <input type="file" id="project_file" name="project_file" class="form-control" accept=".pdf,.doc,.docx,.zip,.rar">
                    <small style="color: var(--text-muted);">Allowed: PDF, DOC, ZIP (Max 25MB)</small>
                </div>

                <div class="form-group">
                    <label for="images_file">Project Screenshots / Diagrams</label>
                    <input type="file" id="images_file" name="images_file" class="form-control" accept=".png,.jpg,.jpeg">
                    <small style="color: var(--text-muted);">Allowed: PNG, JPG, JPEG</small>
                </div>

                <div class="form-group full-width">
                    <label>Select Team Members</label>
                    <div style="max-height: 150px; overflow-y: auto; border: 1px solid var(--light-sky-blue); padding: 10px; border-radius: var(--radius-sm); background: #fff;">
                        <?php while($st = mysqli_fetch_assoc($students_res)): ?>
                            <label style="display: block; margin-bottom: 6px; font-weight: normal; cursor: pointer;">
                                <input type="checkbox" name="team_members[]" value="<?= $st['id']; ?>">
                                <?= htmlspecialchars($st['name']); ?> (<?= htmlspecialchars($st['roll_number']); ?>)
                            </label>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-navy"><i class="fas fa-save"></i> Submit Project</button>
            </div>
        </form>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
