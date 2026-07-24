<?php
/**
 * View Projects List - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();

// Fetch student projects
$sql = "SELECT p.*, f.name as guide_name FROM projects p 
        LEFT JOIN faculty f ON p.guide_id = f.id 
        WHERE p.created_by_student_id = '$student_id' 
        ORDER BY p.id DESC";
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
            <h1><i class="fas fa-folder-open"></i> Student Projects</h1>
            <p>View, manage, and update all academic projects assigned to you.</p>
        </div>
        <div>
            <a href="add_project.php" class="btn btn-navy"><i class="fas fa-plus"></i> Add New Project</a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control table-search-input" data-table="projectsTable" placeholder="Search by project title, tech stack, or guide...">
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="projectsTable" data-col="3">
                <option value="">All Statuses</option>
                <option value="Completed">Completed</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="custom-table" id="projectsTable">
                <thead>
                    <tr>
                        <th>Project Title</th>
                        <th>Department</th>
                        <th>Tech Stack</th>
                        <th>Status</th>
                        <th>Guide</th>
                        <th>Dates</th>
                        <th class="no-export">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['title']); ?></strong>
                                    <?php if ($row['github_link']): ?>
                                        <br><a href="<?= htmlspecialchars($row['github_link']); ?>" target="_blank" style="font-size: 11px;"><i class="fab fa-github"></i> GitHub Repo</a>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['department']); ?></td>
                                <td><span style="background: var(--light-sky-blue); color: var(--primary-navy); padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;"><?= htmlspecialchars($row['technology_stack']); ?></span></td>
                                <td><?= render_status_badge($row['status']); ?></td>
                                <td><?= htmlspecialchars($row['guide_name'] ?: 'Unassigned'); ?></td>
                                <td>
                                    <small><i class="far fa-calendar-alt"></i> <?= format_date($row['start_date']); ?><br>to <?= format_date($row['expected_completion_date']); ?></small>
                                </td>
                                <td class="no-export">
                                    <a href="project_details.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-navy" title="View Details"><i class="fas fa-eye"></i></a>
                                    <a href="edit_project.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-teal" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="delete_project.php?id=<?= $row['id']; ?>&token=<?= generate_csrf_token(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this project?');" title="Delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 24px;">No projects registered yet. Click 'Add New Project' to get started!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
