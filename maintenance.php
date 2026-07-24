<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Module 4: Maintenance Logs
// ====================================================================
require_once 'includes/db.php';

$pageTitle = "Maintenance Logs | AIML AcademicHub";
$message = "";
$messageType = "info";

// Handle Add / Edit / Delete Maintenance Logs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_maintenance') {
        $equipment_id  = intval($_POST['equipment_id'] ?? 0);
        $issue         = trim($_POST['issue'] ?? '');
        $reported_date = $_POST['reported_date'] ?? date('Y-m-d');
        $repair_status = $_POST['repair_status'] ?? 'Pending';
        $cost          = floatval($_POST['cost'] ?? 0.00);
        $remarks       = trim($_POST['remarks'] ?? '');

        if ($equipment_id > 0 && !empty($issue)) {
            try {
                // Get equipment name
                $stmtEq = $pdo->prepare("SELECT equipment_name FROM equipment WHERE id = ?");
                $stmtEq->execute([$equipment_id]);
                $eqName = $stmtEq->fetchColumn() ?: 'Equipment';

                $stmt = $pdo->prepare("INSERT INTO maintenance_logs (equipment_id, equipment_name, issue, reported_date, repair_status, cost, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$equipment_id, $eqName, $issue, $reported_date, $repair_status, $cost, $remarks]);

                // Also update equipment status if under active maintenance
                if (in_array($repair_status, ['Pending', 'In Progress'])) {
                    $pdo->prepare("UPDATE equipment SET status = 'Under Maintenance' WHERE id = ?")->execute([$equipment_id]);
                } elseif ($repair_status === 'Completed' || $repair_status === 'Replaced') {
                    $pdo->prepare("UPDATE equipment SET status = 'Available' WHERE id = ?")->execute([$equipment_id]);
                }

                $message = "Maintenance log created for '$eqName'!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error creating log: " . $e->getMessage();
                $messageType = "error";
            }
        }
    } elseif ($action === 'edit_maintenance') {
        $log_id        = intval($_POST['log_id'] ?? 0);
        $issue         = trim($_POST['issue'] ?? '');
        $reported_date = $_POST['reported_date'] ?? date('Y-m-d');
        $repair_status = $_POST['repair_status'] ?? 'Pending';
        $cost          = floatval($_POST['cost'] ?? 0.00);
        $remarks       = trim($_POST['remarks'] ?? '');

        if ($log_id > 0 && !empty($issue)) {
            try {
                $stmt = $pdo->prepare("UPDATE maintenance_logs SET issue=?, reported_date=?, repair_status=?, cost=?, remarks=? WHERE id=?");
                $stmt->execute([$issue, $reported_date, $repair_status, $cost, $remarks, $log_id]);

                // Sync equipment status
                $stmtGet = $pdo->prepare("SELECT equipment_id FROM maintenance_logs WHERE id = ?");
                $stmtGet->execute([$log_id]);
                $eqId = $stmtGet->fetchColumn();

                if ($eqId) {
                    if (in_array($repair_status, ['Pending', 'In Progress'])) {
                        $pdo->prepare("UPDATE equipment SET status = 'Under Maintenance' WHERE id = ?")->execute([$eqId]);
                    } elseif ($repair_status === 'Completed' || $repair_status === 'Replaced') {
                        $pdo->prepare("UPDATE equipment SET status = 'Available' WHERE id = ?")->execute([$eqId]);
                    }
                }

                $message = "Maintenance log updated successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error updating log: " . $e->getMessage();
                $messageType = "error";
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM maintenance_logs WHERE id = ?");
        $stmt->execute([$deleteId]);
        $message = "Maintenance log deleted successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Could not delete log: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch maintenance logs with lab details
$logs = [];
$equipmentOptions = [];
try {
    $stmt = $pdo->query("SELECT m.*, e.equipment_code, l.lab_name FROM maintenance_logs m JOIN equipment e ON m.equipment_id = e.id JOIN labs l ON e.lab_id = l.id ORDER BY m.id DESC");
    $logs = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, equipment_name, equipment_code FROM equipment ORDER BY equipment_name ASC");
    $equipmentOptions = $stmt->fetchAll();
} catch (PDOException $e) {
    //
}

$commonIssues = [
    "PC Not Booting",
    "Slow System Performance",
    "Projector Not Working",
    "Printer Paper Jam",
    "Monitor Flickering",
    "Keyboard Not Working",
    "Mouse Not Working",
    "Internet Connectivity Issue",
    "UPS Battery Failure",
    "Electrical Equipment Fault",
    "Glassware Damaged"
];

include('header.php');
include('sidebar.php');
?>

<div class="section-title-wrapper">
    <div>
        <h2 class="section-title"><i class="fa-solid fa-wrench"></i> Equipment Maintenance Logs</h2>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-top:0.2rem;">Track equipment repairs, technician diagnostics, costs, and hardware status workflows.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addMaintenanceModal')">
        <i class="fa-solid fa-plus"></i> New Maintenance Request
    </button>
</div>

<?php if (!empty($message)): ?>
<div class="toast toast-<?php echo $messageType; ?>" style="position:relative; bottom:0; right:0; margin-bottom:1.5rem;">
    <i class="fa-solid fa-circle-check"></i>
    <span><?php echo htmlspecialchars($message); ?></span>
</div>
<?php endif; ?>

<!-- Logs Table -->
<div class="table-responsive">
    <table class="custom-table">
        <thead>
            <tr>
                <th>Log ID</th>
                <th>Equipment Name</th>
                <th>Assigned Lab</th>
                <th>Reported Issue</th>
                <th>Reported Date</th>
                <th>Repair Status</th>
                <th>Est. Cost</th>
                <th>Remarks / Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td><strong>#LOG-<?php echo StringPad($log['id']); ?></strong></td>
                <td>
                    <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($log['equipment_name']); ?></div>
                    <span style="font-size:0.75rem; color:var(--text-muted);"><?php echo htmlspecialchars($log['equipment_code']); ?></span>
                </td>
                <td><i class="fa-solid fa-flask" style="color:var(--secondary-teal);"></i> <?php echo htmlspecialchars($log['lab_name']); ?></td>
                <td><strong><?php echo htmlspecialchars($log['issue']); ?></strong></td>
                <td><?php echo htmlspecialchars($log['reported_date']); ?></td>
                <td>
                    <?php if ($log['repair_status'] === 'Pending'): ?>
                        <span class="badge badge-danger"><i class="fa-solid fa-clock"></i> Pending</span>
                    <?php elseif ($log['repair_status'] === 'In Progress'): ?>
                        <span class="badge badge-warning"><i class="fa-solid fa-gears"></i> In Progress</span>
                    <?php elseif ($log['repair_status'] === 'Completed'): ?>
                        <span class="badge badge-success"><i class="fa-solid fa-check-double"></i> Completed</span>
                    <?php else: ?>
                        <span class="badge badge-info"><i class="fa-solid fa-rotate"></i> Replaced</span>
                    <?php endif; ?>
                </td>
                <td><strong>₹<?php echo number_format($log['cost'], 2); ?></strong></td>
                <td><span style="font-size:0.82rem; color:var(--text-muted);"><?php echo htmlspecialchars($log['remarks']); ?></span></td>
                <td>
                    <div class="action-btns-group">
                        <button class="btn btn-sm btn-outline" onclick='populateEditMaintenanceModal(<?php echo json_encode($log, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Edit Log">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDeleteLog(<?php echo $log['id']; ?>, '<?php echo addslashes($log['equipment_name']); ?>')" title="Delete Log">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
function StringPad($num) {
    return str_pad($num, 4, '0', STR_PAD_LEFT);
}
?>

<!-- Modal: Add New Maintenance Log -->
<div class="modal-backdrop" id="addMaintenanceModal">
    <div class="modal-dialog">
        <form action="maintenance.php" method="POST">
            <input type="hidden" name="action" value="add_maintenance">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-wrench"></i> Log Maintenance Request</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('addMaintenanceModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Select Equipment</label>
                    <select name="equipment_id" class="form-control" required>
                        <?php foreach ($equipmentOptions as $eqOpt): ?>
                            <option value="<?php echo $eqOpt['id']; ?>"><?php echo htmlspecialchars($eqOpt['equipment_name'] . ' (' . $eqOpt['equipment_code'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Common Issue / Symptom</label>
                    <select name="issue" class="form-control" required>
                        <?php foreach ($commonIssues as $ci): ?>
                            <option value="<?php echo htmlspecialchars($ci); ?>"><?php echo htmlspecialchars($ci); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Reported Date</label>
                    <input type="date" name="reported_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Repair Status</label>
                    <select name="repair_status" class="form-control">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Replaced">Replaced</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Repair Cost (Optional ₹)</label>
                    <input type="number" step="0.01" name="cost" class="form-control" placeholder="0.00" value="0.00">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Remarks & Diagnostic Notes</label>
                    <textarea name="remarks" class="form-control" placeholder="Enter technician notes or replacement details..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addMaintenanceModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Log</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Maintenance Log -->
<div class="modal-backdrop" id="editMaintenanceModal">
    <div class="modal-dialog">
        <form action="maintenance.php" method="POST">
            <input type="hidden" name="action" value="edit_maintenance">
            <input type="hidden" name="log_id" id="editLogId">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Update Maintenance Log</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('editMaintenanceModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Issue</label>
                    <select name="issue" id="editLogIssue" class="form-control" required>
                        <?php foreach ($commonIssues as $ci): ?>
                            <option value="<?php echo htmlspecialchars($ci); ?>"><?php echo htmlspecialchars($ci); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Reported Date</label>
                    <input type="date" name="reported_date" id="editLogDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Repair Status</label>
                    <select name="repair_status" id="editLogStatus" class="form-control">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Replaced">Replaced</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Repair Cost (₹)</label>
                    <input type="number" step="0.01" name="cost" id="editLogCost" class="form-control">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Remarks & Diagnostic Notes</label>
                    <textarea name="remarks" id="editLogRemarks" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editMaintenanceModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Log</button>
            </div>
        </form>
    </div>
</div>

<script>
function populateEditMaintenanceModal(log) {
    document.getElementById('editLogId').value = log.id;
    document.getElementById('editLogIssue').value = log.issue;
    document.getElementById('editLogDate').value = log.reported_date;
    document.getElementById('editLogStatus').value = log.repair_status;
    document.getElementById('editLogCost').value = log.cost;
    document.getElementById('editLogRemarks').value = log.remarks;
    openModal('editMaintenanceModal');
}

function confirmDeleteLog(id, eqName) {
    if (confirm(`Are you sure you want to delete maintenance log #${id} for "${eqName}"?`)) {
        window.location.href = `maintenance.php?action=delete&id=${id}`;
    }
}
</script>

<?php include('footer.php'); ?>
