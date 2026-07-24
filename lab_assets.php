<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Module 2: Lab Assets
// ====================================================================
require_once 'includes/db.php';

$pageTitle = "Lab Assets | AIML AcademicHub";
$message = "";
$messageType = "info";

// Handle Add / Edit / Delete Actions via POST/GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_lab') {
        $lab_name = trim($_POST['lab_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $capacity = trim($_POST['capacity'] ?? '');
        $incharge_name = trim($_POST['incharge_name'] ?? '');
        $incharge_email = trim($_POST['incharge_email'] ?? '');
        $installed_software = trim($_POST['installed_software'] ?? '');
        $facilities = trim($_POST['facilities'] ?? '');
        $status = $_POST['status'] ?? 'Active';

        if (!empty($lab_name) && !empty($incharge_name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO labs (lab_name, location, capacity, incharge_name, incharge_email, installed_software, facilities, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$lab_name, $location, $capacity, $incharge_name, $incharge_email, $installed_software, $facilities, $status]);
                $message = "Laboratory '$lab_name' added successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $messageType = "error";
            }
        }
    } elseif ($action === 'edit_lab') {
        $lab_id = intval($_POST['lab_id'] ?? 0);
        $lab_name = trim($_POST['lab_name'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $capacity = trim($_POST['capacity'] ?? '');
        $incharge_name = trim($_POST['incharge_name'] ?? '');
        $incharge_email = trim($_POST['incharge_email'] ?? '');
        $installed_software = trim($_POST['installed_software'] ?? '');
        $facilities = trim($_POST['facilities'] ?? '');
        $status = $_POST['status'] ?? 'Active';

        if ($lab_id > 0 && !empty($lab_name)) {
            try {
                $stmt = $pdo->prepare("UPDATE labs SET lab_name=?, location=?, capacity=?, incharge_name=?, incharge_email=?, installed_software=?, facilities=?, status=? WHERE id=?");
                $stmt->execute([$lab_name, $location, $capacity, $incharge_name, $incharge_email, $installed_software, $facilities, $status, $lab_id]);
                $message = "Laboratory '$lab_name' updated successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $messageType = "error";
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM labs WHERE id = ?");
        $stmt->execute([$deleteId]);
        $message = "Laboratory deleted successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Could not delete lab: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch all labs
$labs = [];
try {
    $stmt = $pdo->query("SELECT * FROM labs ORDER BY id ASC");
    $labs = $stmt->fetchAll();
} catch (PDOException $e) {
    //
}

include('header.php');
include('sidebar.php');
?>

<div class="section-title-wrapper">
    <div>
        <h2 class="section-title"><i class="fa-solid fa-flask"></i> Laboratory Assets</h2>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-top:0.2rem;">Departmental laboratory facilities, faculty incharges, software packages, and technical assets.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addLabModal')">
        <i class="fa-solid fa-plus"></i> Add New Lab
    </button>
</div>

<?php if (!empty($message)): ?>
<div class="toast toast-<?php echo $messageType; ?>" style="position:relative; bottom:0; right:0; margin-bottom:1.5rem;">
    <i class="fa-solid fa-circle-check"></i>
    <span><?php echo htmlspecialchars($message); ?></span>
</div>
<?php endif; ?>

<!-- Lab Assets Cards Grid -->
<div class="labs-grid">
    <?php foreach ($labs as $lab): ?>
    <?php
        $softwareList = array_map('trim', explode(',', $lab['installed_software'] ?? ''));
        $facilitiesList = array_map('trim', explode(',', $lab['facilities'] ?? ''));
    ?>
    <div class="lab-card">
        <div class="lab-card-header">
            <h3 class="lab-name-title"><?php echo htmlspecialchars($lab['lab_name']); ?></h3>
            <span class="lab-location-tag"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($lab['location']); ?></span>
        </div>

        <div class="lab-card-body">
            <!-- Capacity & Incharge -->
            <div class="lab-meta-row">
                <div class="meta-item">
                    <i class="fa-solid fa-users"></i>
                    <span>Capacity: <strong><?php echo htmlspecialchars($lab['capacity']); ?></strong></span>
                </div>
                <div class="meta-item">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Incharge: <strong><?php echo htmlspecialchars($lab['incharge_name']); ?></strong></span>
                </div>
            </div>

            <!-- Installed Software -->
            <div>
                <div class="lab-section-title"><i class="fa-solid fa-laptop-code"></i> Installed Software</div>
                <div class="software-pills">
                    <?php foreach (array_slice($softwareList, 0, 7) as $sw): ?>
                        <?php if (!empty($sw)): ?>
                            <span class="software-pill"><?php echo htmlspecialchars($sw); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (count($softwareList) > 7): ?>
                        <span class="software-pill">+<?php echo (count($softwareList) - 7); ?> more</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Facilities -->
            <div>
                <div class="lab-section-title"><i class="fa-solid fa-layer-group"></i> Facilities</div>
                <div class="facilities-pills">
                    <?php foreach (array_slice($facilitiesList, 0, 6) as $fac): ?>
                        <?php if (!empty($fac)): ?>
                            <span class="facility-badge"><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($fac); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (count($facilitiesList) > 6): ?>
                        <span class="facility-badge">+<?php echo (count($facilitiesList) - 6); ?> more</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="lab-card-footer">
            <span class="badge badge-success"><i class="fa-solid fa-circle"></i> <?php echo htmlspecialchars($lab['status']); ?></span>
            
            <div class="action-btns-group">
                <a href="lab_details.php?id=<?php echo $lab['id']; ?>" class="btn btn-sm btn-primary" title="View Full Details">
                    <i class="fa-solid fa-eye"></i> View
                </a>
                <button class="btn btn-sm btn-outline" onclick='populateEditLabModal(<?php echo json_encode($lab, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Edit Lab">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="confirmDeleteLab(<?php echo $lab['id']; ?>, '<?php echo addslashes($lab['lab_name']); ?>')" title="Delete Lab">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Add New Lab -->
<div class="modal-backdrop" id="addLabModal">
    <div class="modal-dialog">
        <form action="lab_assets.php" method="POST">
            <input type="hidden" name="action" value="add_lab">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-flask"></i> Add New Laboratory</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('addLabModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <div class="form-group">
                    <label class="form-label">Laboratory Name</label>
                    <input type="text" name="lab_name" class="form-control" placeholder="e.g. Computer Lab 5" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Location (Room & Floor)</label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. B-405, 4th Floor" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Student Capacity</label>
                    <input type="text" name="capacity" class="form-control" placeholder="e.g. 35-40 Students" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Lab Incharge Name</label>
                    <input type="text" name="incharge_name" class="form-control" placeholder="e.g. Dipika Bhatt" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Incharge Email</label>
                    <input type="email" name="incharge_email" class="form-control" placeholder="faculty@academichub.edu" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Installed Software (Comma Separated)</label>
                    <textarea name="installed_software" class="form-control" placeholder="Windows 11, Python, VS Code, MySQL Workbench..."></textarea>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Facilities (Comma Separated)</label>
                    <textarea name="facilities" class="form-control" placeholder="Air Conditioning, High Speed Internet, LCD Projector, UPS Backup..."></textarea>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addLabModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Laboratory</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Lab -->
<div class="modal-backdrop" id="editLabModal">
    <div class="modal-dialog">
        <form action="lab_assets.php" method="POST">
            <input type="hidden" name="action" value="edit_lab">
            <input type="hidden" name="lab_id" id="edit_lab_id">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Laboratory</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('editLabModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <div class="form-group">
                    <label class="form-label">Laboratory Name</label>
                    <input type="text" name="lab_name" id="edit_lab_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" id="edit_location" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Capacity</label>
                    <input type="text" name="capacity" id="edit_capacity" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Lab Incharge Name</label>
                    <input type="text" name="incharge_name" id="edit_incharge_name" class="form-control" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Incharge Email</label>
                    <input type="email" name="incharge_email" id="edit_incharge_email" class="form-control" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Installed Software</label>
                    <textarea name="installed_software" id="edit_installed_software" class="form-control"></textarea>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Facilities</label>
                    <textarea name="facilities" id="edit_facilities" class="form-control"></textarea>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editLabModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Laboratory</button>
            </div>
        </form>
    </div>
</div>

<script>
function populateEditLabModal(lab) {
    document.getElementById('edit_lab_id').value = lab.id;
    document.getElementById('edit_lab_name').value = lab.lab_name;
    document.getElementById('edit_location').value = lab.location;
    document.getElementById('edit_capacity').value = lab.capacity;
    document.getElementById('edit_incharge_name').value = lab.incharge_name;
    document.getElementById('edit_incharge_email').value = lab.incharge_email;
    document.getElementById('edit_installed_software').value = lab.installed_software;
    document.getElementById('edit_facilities').value = lab.facilities;
    document.getElementById('edit_status').value = lab.status;
    openModal('editLabModal');
}

function confirmDeleteLab(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? All assigned equipment records will also be removed.`)) {
        window.location.href = `lab_assets.php?action=delete&id=${id}`;
    }
}
</script>

<?php include('footer.php'); ?>
