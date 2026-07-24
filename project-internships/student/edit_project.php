<?php
/**
 * Edit Project - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$csrf_token = generate_csrf_token();
$errors = [];

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch project record
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ? AND created_by_student_id = ?");
$stmt->bind_param("ii", $project_id, $student_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    set_flash_message('error', 'Project not found or unauthorized access.');
    header("Location: view_projects.php");
    exit;
}

// Fetch faculty list
$faculty_sql = "SELECT id, name, department FROM faculty ORDER BY name ASC";
$faculty_res = mysqli_query($conn, $faculty_sql);

// Handle form submission
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

    if (empty($title)) $errors[] = "Project Title is required.";
    if (empty($abstract)) $errors[] = "Project Abstract is required.";
    if (empty($technology_stack)) $errors[] = "Technology Stack is required.";

    // Handle file upload replacements if provided
    $doc_path = upload_file('project_file', 'documents') ?: $project['document_path'];
    $img_path = upload_file('images_file', 'images') ?: $project['images_path'];

    // Guide name fetch
    $guide_name = null;
    if ($guide_id) {
        $g_stmt = $conn->prepare("SELECT name FROM faculty WHERE id = ?");
        $g_stmt->bind_param("i", $guide_id);
        $g_stmt->execute();
        $g_res = $g_stmt->get_result()->fetch_assoc();
        if ($g_res) $guide_name = $g_res['name'];
        $g_stmt->close();
    }

    if (empty($errors)) {
        $update_stmt = $conn->prepare("UPDATE projects SET 
            title = ?, abstract = ?, department = ?, technology_stack = ?, guide_name = ?, guide_id = ?, 
            github_link = ?, document_path = ?, images_path = ?, start_date = ?, expected_completion_date = ?, status = ? 
            WHERE id = ? AND created_by_student_id = ?");
        
        $update_stmt->bind_param("sssssissssssii", 
            $title, $abstract, $department, $technology_stack, $guide_name, $guide_id, 
            $github_link, $doc_path, $img_path, $start_date, $expected_completion_date, $status, 
            $project_id, $student_id
        );

        if ($update_stmt->execute()) {
            $update_stmt->close();
            set_flash_message('success', "Project '$title' updated successfully!");
            header("Location: view_projects.php");
            exit;
        } else {
            $errors[] = "Database update error: " . $conn->error;
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
            <h1><i class="fas fa-edit"></i> Edit Project: <?= htmlspecialchars($project['title']); ?></h1>
            <p>Update your project parameters, technology stack, guide, or attachments.</p>
        </div>
        <div>
            <a href="view_projects.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancel</a>
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
                    <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($_POST['title'] ?? $project['title']); ?>">
                </div>

                <div class="form-group full-width">
                    <label for="abstract">Project Abstract *</label>
                    <textarea id="abstract" name="abstract" class="form-control" required><?= htmlspecialchars($_POST['abstract'] ?? $project['abstract']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="department">Department *</label>
                    <select id="department" name="department" class="form-control" required>
                        <option value="AI & Machine Learning" <?= $project['department'] === 'AI & Machine Learning' ? 'selected' : ''; ?>>AI & Machine Learning</option>
                        <option value="Computer Science" <?= $project['department'] === 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
                        <option value="Information Technology" <?= $project['department'] === 'Information Technology' ? 'selected' : ''; ?>>Information Technology</option>
                        <option value="Electronics & Telecom" <?= $project['department'] === 'Electronics & Telecom' ? 'selected' : ''; ?>>Electronics & Telecom</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="technology_stack">Technology Stack *</label>
                    <input type="text" id="technology_stack" name="technology_stack" class="form-control" required value="<?= htmlspecialchars($_POST['technology_stack'] ?? $project['technology_stack']); ?>">
                </div>

                <div class="form-group">
                    <label for="guide_id">Project Guide (Faculty)</label>
                    <select id="guide_id" name="guide_id" class="form-control">
                        <option value="">Select Faculty Guide</option>
                        <?php while($f = mysqli_fetch_assoc($faculty_res)): ?>
                            <option value="<?= $f['id']; ?>" <?= $project['guide_id'] == $f['id'] ? 'selected' : ''; ?>><?= htmlspecialchars($f['name']); ?> (<?= htmlspecialchars($f['department']); ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="github_link">GitHub Repository Link</label>
                    <input type="url" id="github_link" name="github_link" class="form-control" value="<?= htmlspecialchars($_POST['github_link'] ?? $project['github_link']); ?>">
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date *</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= $_POST['start_date'] ?? $project['start_date']; ?>">
                </div>

                <div class="form-group">
                    <label for="expected_completion_date">Expected Completion Date *</label>
                    <input type="date" id="expected_completion_date" name="expected_completion_date" class="form-control" required value="<?= $_POST['expected_completion_date'] ?? $project['expected_completion_date']; ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="Pending" <?= $project['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?= $project['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Ongoing" <?= $project['status'] === 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="project_file">Replace Project Document/Zip</label>
                    <input type="file" id="project_file" name="project_file" class="form-control" accept=".pdf,.doc,.docx,.zip,.rar">
                    <?php if ($project['document_path']): ?>
                        <small style="color: var(--secondary-teal);">Current File: <a href="../<?= htmlspecialchars($project['document_path']); ?>" target="_blank">Download File</a></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="images_file">Replace Screenshots / Diagrams</label>
                    <input type="file" id="images_file" name="images_file" class="form-control" accept=".png,.jpg,.jpeg">
                    <?php if ($project['images_path']): ?>
                        <small style="color: var(--secondary-teal);">Current Image: <a href="../<?= htmlspecialchars($project['images_path']); ?>" target="_blank">View Preview</a></small>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 24px; text-align: right;">
                <button type="submit" class="btn btn-navy"><i class="fas fa-sync"></i> Update Project</button>
            </div>
        </form>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
