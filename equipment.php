<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Module 3: Equipment Records
// ====================================================================
require_once 'includes/db.php';

$pageTitle = "Equipment Records | AIML AcademicHub";
$message = "";
$messageType = "info";

// Handle Add / Edit / Delete Actions via POST/GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_equipment') {
        $equipment_code = trim($_POST['equipment_code'] ?? '');
        $equipment_name = trim($_POST['equipment_name'] ?? '');
        $category       = $_POST['category'] ?? '';
        $lab_id         = intval($_POST['lab_id'] ?? 0);
        $purchase_date  = $_POST['purchase_date'] ?? date('Y-m-d');
        $warranty       = trim($_POST['warranty'] ?? '3 Years');
        $quantity       = intval($_POST['quantity'] ?? 1);
        $status         = $_POST['status'] ?? 'Available';

        if (!empty($equipment_code) && !empty($equipment_name) && $lab_id > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO equipment (equipment_code, equipment_name, category, lab_id, purchase_date, warranty, quantity, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$equipment_code, $equipment_name, $category, $lab_id, $purchase_date, $warranty, $quantity, $status]);
                $message = "Equipment '$equipment_name' ($equipment_code) added successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error adding equipment: " . $e->getMessage();
                $messageType = "error";
            }
        }
    } elseif ($action === 'edit_equipment') {
        $eq_id          = intval($_POST['equipment_id'] ?? 0);
        $equipment_code = trim($_POST['equipment_code'] ?? '');
        $equipment_name = trim($_POST['equipment_name'] ?? '');
        $category       = $_POST['category'] ?? '';
        $lab_id         = intval($_POST['lab_id'] ?? 0);
        $purchase_date  = $_POST['purchase_date'] ?? date('Y-m-d');
        $warranty       = trim($_POST['warranty'] ?? '3 Years');
        $quantity       = intval($_POST['quantity'] ?? 1);
        $status         = $_POST['status'] ?? 'Available';

        if ($eq_id > 0 && !empty($equipment_name)) {
            try {
                $stmt = $pdo->prepare("UPDATE equipment SET equipment_code=?, equipment_name=?, category=?, lab_id=?, purchase_date=?, warranty=?, quantity=?, status=? WHERE id=?");
                $stmt->execute([$equipment_code, $equipment_name, $category, $lab_id, $purchase_date, $warranty, $quantity, $status, $eq_id]);
                $message = "Equipment '$equipment_name' updated successfully!";
                $messageType = "success";
            } catch (PDOException $e) {
                $message = "Error updating equipment: " . $e->getMessage();
                $messageType = "error";
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM equipment WHERE id = ?");
        $stmt->execute([$deleteId]);
        $message = "Equipment record deleted successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Could not delete equipment: " . $e->getMessage();
        $messageType = "error";
    }
}

// Fetch all equipment with lab info
$equipments = [];
$labsList = [];
try {
    $stmt = $pdo->query("SELECT e.*, l.lab_name FROM equipment e JOIN labs l ON e.lab_id = l.id ORDER BY e.id DESC");
    $equipments = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT id, lab_name FROM labs ORDER BY id ASC");
    $labsList = $stmt->fetchAll();
} catch (PDOException $e) {
    //
}

include('header.php');
include('sidebar.php');
?>

<div class="section-title-wrapper">
    <div>
        <h2 class="section-title"><i class="fa-solid fa-desktop"></i> Equipment Records Management</h2>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-top:0.2rem;">Comprehensive catalog of Computer, Physics & BXEE, and Chemistry equipment.</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addEquipmentModal')">
        <i class="fa-solid fa-plus"></i> Add New Equipment
    </button>
</div>

<?php if (!empty($message)): ?>
<div class="toast toast-<?php echo $messageType; ?>" style="position:relative; bottom:0; right:0; margin-bottom:1.5rem;">
    <i class="fa-solid fa-circle-check"></i>
    <span><?php echo htmlspecialchars($message); ?></span>
</div>
<?php endif; ?>

<!-- Table Toolbar with Filters & Search -->
<div class="table-toolbar">
    <div class="toolbar-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchEquipmentInput" placeholder="Search equipment by name or ID...">
    </div>

    <div class="toolbar-filters">
        <select id="filterCategorySelect" class="custom-select">
            <option value="">All Categories</option>
            <option value="Computer Lab Equipment">Computer Lab Equipment</option>
            <option value="Physics & BXEE Equipment">Physics & BXEE Equipment</option>
            <option value="Chemistry Equipment">Chemistry Equipment</option>
        </select>

        <select id="filterLabSelect" class="custom-select">
            <option value="">All Assigned Labs</option>
            <?php foreach ($labsList as $l): ?>
                <option value="<?php echo htmlspecialchars($l['lab_name']); ?>"><?php echo htmlspecialchars($l['lab_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <select id="filterStatusSelect" class="custom-select">
            <option value="">All Statuses</option>
            <option value="Available">Available</option>
            <option value="In Use">In Use</option>
            <option value="Under Maintenance">Under Maintenance</option>
        </select>
    </div>
</div>

<!-- Table Card -->
<div class="table-responsive">
    <table class="custom-table" id="equipmentTable">
        <thead>
            <tr>
                <th>Equipment ID</th>
                <th>Equipment Name</th>
                <th>Category</th>
                <th>Assigned Lab</th>
                <th>Purchase Date</th>
                <th>Warranty</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $eq): ?>
            <tr data-name="<?php echo htmlspecialchars(strtolower($eq['equipment_name'])); ?>"
                data-code="<?php echo htmlspecialchars(strtolower($eq['equipment_code'])); ?>"
                data-category="<?php echo htmlspecialchars($eq['category']); ?>"
                data-lab="<?php echo htmlspecialchars($eq['lab_name']); ?>"
                data-status="<?php echo htmlspecialchars($eq['status']); ?>">
                
                <td><strong><?php echo htmlspecialchars($eq['equipment_code']); ?></strong></td>
                <td>
                    <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($eq['equipment_name']); ?></div>
                </td>
                <td><span class="badge badge-info"><?php echo htmlspecialchars($eq['category']); ?></span></td>
                <td><i class="fa-solid fa-flask" style="color:var(--secondary-teal);"></i> <?php echo htmlspecialchars($eq['lab_name']); ?></td>
                <td><?php echo htmlspecialchars($eq['purchase_date']); ?></td>
                <td><?php echo htmlspecialchars($eq['warranty']); ?></td>
                <td><strong><?php echo htmlspecialchars($eq['quantity']); ?></strong></td>
                <td>
                    <?php if ($eq['status'] === 'Available'): ?>
                        <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Available</span>
                    <?php elseif ($eq['status'] === 'Under Maintenance'): ?>
                        <span class="badge badge-warning"><i class="fa-solid fa-wrench"></i> Maintenance</span>
                    <?php else: ?>
                        <span class="badge badge-info"><i class="fa-solid fa-play"></i> In Use</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="action-btns-group">
                        <button class="btn btn-sm btn-outline" onclick='viewEquipmentDetails(<?php echo json_encode([
                            "code" => $eq["equipment_code"],
                            "name" => $eq["equipment_name"],
                            "category" => $eq["category"],
                            "lab" => $eq["lab_name"],
                            "purchase" => $eq["purchase_date"],
                            "warranty" => $eq["warranty"],
                            "quantity" => $eq["quantity"],
                            "status" => $eq["status"]
                        ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline" onclick='openEditEquipmentModal(<?php echo json_encode([
                            "id" => $eq["id"],
                            "code" => $eq["equipment_code"],
                            "name" => $eq["equipment_name"],
                            "category" => $eq["category"],
                            "labId" => $eq["lab_id"],
                            "purchase" => $eq["purchase_date"],
                            "warranty" => $eq["warranty"],
                            "quantity" => $eq["quantity"],
                            "status" => $eq["status"]
                        ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' title="Edit Equipment">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="confirmDeleteEquipment(<?php echo $eq['id']; ?>, '<?php echo addslashes($eq['equipment_name']); ?>')" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Table Pagination controls -->
<div class="table-pagination">
    <div class="pagination-info" id="paginationInfo">Showing equipment records...</div>
    <div class="pagination-controls" id="paginationControls"></div>
</div>

<!-- Modal: View Equipment Details -->
<div class="modal-backdrop" id="viewEquipmentModal">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-circle-info"></i> Equipment Details</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('viewEquipmentModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.2rem;">
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Equipment Code</span><h4 id="viewEqCode" style="color:var(--primary-navy);"></h4></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Equipment Name</span><h4 id="viewEqName"></h4></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Category</span><p id="viewEqCategory" style="font-weight:600;"></p></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Assigned Lab</span><p id="viewEqLab" style="font-weight:600;"></p></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Purchase Date</span><p id="viewEqPurchase"></p></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Warranty</span><p id="viewEqWarranty"></p></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Quantity</span><p id="viewEqQty" style="font-weight:700;"></p></div>
                <div><span style="color:var(--text-muted); font-size:0.8rem;">Current Status</span><p id="viewEqStatus" style="font-weight:600;"></p></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="closeModal('viewEquipmentModal')">Close</button>
        </div>
    </div>
</div>

<!-- Modal: Add New Equipment -->
<div class="modal-backdrop" id="addEquipmentModal">
    <div class="modal-dialog">
        <form action="equipment.php" method="POST">
            <input type="hidden" name="action" value="add_equipment">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-desktop"></i> Add New Equipment</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('addEquipmentModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <div class="form-group">
                    <label class="form-label">Equipment ID Code</label>
                    <input type="text" name="equipment_code" class="form-control" placeholder="e.g. EQ-1011" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Equipment Name</label>
                    <input type="text" name="equipment_name" class="form-control" placeholder="e.g. GPU Workstation" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control" required>
                        <option value="Computer Lab Equipment">Computer Lab Equipment</option>
                        <option value="Physics & BXEE Equipment">Physics & BXEE Equipment</option>
                        <option value="Chemistry Equipment">Chemistry Equipment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigned Laboratory</label>
                    <select name="lab_id" class="form-control" required>
                        <?php foreach ($labsList as $l): ?>
                            <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['lab_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Warranty Period</label>
                    <input type="text" name="warranty" class="form-control" placeholder="e.g. 3 Years" value="3 Years" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Under Maintenance">Under Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addEquipmentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Equipment</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Equipment -->
<div class="modal-backdrop" id="editEquipmentModal">
    <div class="modal-dialog">
        <form action="equipment.php" method="POST">
            <input type="hidden" name="action" value="edit_equipment">
            <input type="hidden" name="equipment_id" id="editEqId">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-pen-to-square"></i> Edit Equipment</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('editEquipmentModal')"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body form-grid">
                <div class="form-group">
                    <label class="form-label">Equipment Code</label>
                    <input type="text" name="equipment_code" id="editEqCode" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Equipment Name</label>
                    <input type="text" name="equipment_name" id="editEqName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" id="editEqCategory" class="form-control" required>
                        <option value="Computer Lab Equipment">Computer Lab Equipment</option>
                        <option value="Physics & BXEE Equipment">Physics & BXEE Equipment</option>
                        <option value="Chemistry Equipment">Chemistry Equipment</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigned Lab</label>
                    <select name="lab_id" id="editEqLab" class="form-control" required>
                        <?php foreach ($labsList as $l): ?>
                            <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['lab_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Purchase Date</label>
                    <input type="date" name="purchase_date" id="editEqPurchase" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Warranty</label>
                    <input type="text" name="warranty" id="editEqWarranty" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="editEqQuantity" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editEqStatus" class="form-control">
                        <option value="Available">Available</option>
                        <option value="In Use">In Use</option>
                        <option value="Under Maintenance">Under Maintenance</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editEquipmentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Equipment</button>
            </div>
        </form>
    </div>
</div>

<?php include('footer.php'); ?>
