<?php
/**
 * View Internships List - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();

// Fetch student internships
$sql = "SELECT * FROM internships WHERE student_id = '$student_id' ORDER BY id DESC";
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
            <h1><i class="fas fa-briefcase"></i> My Internships</h1>
            <p>Track your corporate internships, uploaded offer letters, and certificates.</p>
        </div>
        <div>
            <a href="add_internship.php" class="btn btn-navy"><i class="fas fa-plus"></i> Add Internship</a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control table-search-input" data-table="internshipsTable" placeholder="Search by company name, role, or location...">
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="internshipsTable" data-col="3">
                <option value="">All Modes</option>
                <option value="Online">Online</option>
                <option value="Offline">Offline</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>
        <div style="min-width: 180px;">
            <select class="form-control table-filter-select" data-table="internshipsTable" data-col="5">
                <option value="">All Statuses</option>
                <option value="Completed">Completed</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Approved">Approved</option>
                <option value="Pending">Pending</option>
            </select>
        </div>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="custom-table" id="internshipsTable">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Role / Position</th>
                        <th>Location</th>
                        <th>Mode</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Documents</th>
                        <th class="no-export">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['company_name']); ?></strong>
                                    <?php if ($row['company_website']): ?>
                                        <br><a href="<?= htmlspecialchars($row['company_website']); ?>" target="_blank" style="font-size: 11px;"><i class="fas fa-globe"></i> Website</a>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['role']); ?></td>
                                <td><?= htmlspecialchars($row['location']); ?></td>
                                <td><span style="background: var(--light-sky-blue); color: var(--primary-navy); padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 500;"><?= htmlspecialchars($row['mode']); ?></span></td>
                                <td><?= htmlspecialchars($row['duration']); ?></td>
                                <td><?= render_status_badge($row['status']); ?></td>
                                <td>
                                    <?php if ($row['offer_letter_path']): ?>
                                        <a href="../<?= htmlspecialchars($row['offer_letter_path']); ?>" target="_blank" title="Offer Letter"><i class="fas fa-file-pdf text-navy"></i> Offer</a>
                                    <?php endif; ?>
                                    <?php if ($row['certificate_path']): ?>
                                        &nbsp;|&nbsp;<a href="../<?= htmlspecialchars($row['certificate_path']); ?>" target="_blank" title="Certificate"><i class="fas fa-certificate text-teal"></i> Cert</a>
                                    <?php endif; ?>
                                    <?php if (!$row['offer_letter_path'] && !$row['certificate_path']): ?>
                                        <small style="color: var(--text-muted);">None</small>
                                    <?php endif; ?>
                                </td>
                                <td class="no-export">
                                    <a href="edit_internship.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-teal" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="delete_internship.php?id=<?= $row['id']; ?>&token=<?= generate_csrf_token(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this internship record?');" title="Delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 24px;">No internships registered yet. Click 'Add Internship' to get started!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
