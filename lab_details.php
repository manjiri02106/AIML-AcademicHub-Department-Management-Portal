<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Laboratory Details View Page
// ====================================================================
require_once 'includes/db.php';

$labId = intval($_GET['id'] ?? 1);
$lab = null;
$equipmentList = [];
$maintenanceList = [];
$schedulesList = [];

try {
    // Fetch Lab details
    $stmt = $pdo->prepare("SELECT * FROM labs WHERE id = ?");
    $stmt->execute([$labId]);
    $lab = $stmt->fetch();

    if (!$lab) {
        header("Location: lab_assets.php");
        exit;
    }

    // Fetch equipment assigned to this lab
    $stmt = $pdo->prepare("SELECT * FROM equipment WHERE lab_id = ? ORDER BY category ASC, equipment_name ASC");
    $stmt->execute([$labId]);
    $equipmentList = $stmt->fetchAll();

    // Fetch maintenance records for equipment in this lab
    $stmt = $pdo->prepare("SELECT m.*, e.equipment_code FROM maintenance_logs m JOIN equipment e ON m.equipment_id = e.id WHERE e.lab_id = ? ORDER BY m.reported_date DESC");
    $stmt->execute([$labId]);
    $maintenanceList = $stmt->fetchAll();

    // Fetch schedule sessions in this lab
    $stmt = $pdo->prepare("SELECT * FROM schedules WHERE lab_id = ? ORDER BY FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), time_slot ASC");
    $stmt->execute([$labId]);
    $schedulesList = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$pageTitle = htmlspecialchars($lab['lab_name']) . " Details | AIML AcademicHub";

include('header.php');
include('sidebar.php');

$softwareList = array_map('trim', explode(',', $lab['installed_software'] ?? ''));
$facilitiesList = array_map('trim', explode(',', $lab['facilities'] ?? ''));
?>

<div style="margin-bottom: 1.5rem;">
    <a href="lab_assets.php" class="btn btn-outline btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Back to Lab Assets
    </a>
</div>

<!-- Lab Header Banner -->
<div class="card" style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary-navy) 0%, var(--secondary-teal) 100%); color: #FFFFFF;">
    <div class="card-body" style="padding: 2rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <span class="badge badge-success" style="margin-bottom:0.5rem;"><i class="fa-solid fa-circle"></i> <?php echo htmlspecialchars($lab['status']); ?></span>
                <h1 style="font-size: 1.8rem; font-weight:700; color:#FFFFFF; margin-bottom:0.4rem;"><?php echo htmlspecialchars($lab['lab_name']); ?></h1>
                <p style="color:var(--light-sky-blue); font-size:0.95rem;">
                    <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($lab['location']); ?> &nbsp;|&nbsp;
                    <i class="fa-solid fa-users"></i> Capacity: <?php echo htmlspecialchars($lab['capacity']); ?>
                </p>
            </div>
            <div style="background: rgba(255,255,255,0.12); padding: 1rem 1.5rem; border-radius: var(--radius-md); border:1px solid rgba(255,255,255,0.2);">
                <div style="font-size:0.8rem; color:var(--light-sky-blue); font-weight:600; text-transform:uppercase;">Lab Incharge</div>
                <div style="font-size:1.1rem; font-weight:700; color:#FFFFFF; margin-top:0.2rem;"><?php echo htmlspecialchars($lab['incharge_name']); ?></div>
                <div style="font-size:0.85rem; color:#E2E8F0; margin-top:0.1rem;"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($lab['incharge_email']); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Grid Layout: Software & Facilities -->
<div class="stats-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-laptop-code" style="color:var(--secondary-teal);"></i> Installed Software Stack</h3>
        </div>
        <div class="card-body">
            <div class="software-pills" style="gap:0.5rem;">
                <?php foreach ($softwareList as $sw): ?>
                    <?php if(!empty($sw)): ?>
                        <span class="software-pill" style="font-size:0.85rem; padding:0.35rem 0.75rem;"><?php echo htmlspecialchars($sw); ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fa-solid fa-layer-group" style="color:var(--secondary-teal);"></i> Infrastructure & Facilities</h3>
        </div>
        <div class="card-body">
            <div class="facilities-pills" style="gap:0.5rem;">
                <?php foreach ($facilitiesList as $fac): ?>
                    <?php if(!empty($fac)): ?>
                        <span class="facility-badge" style="font-size:0.82rem; padding:0.35rem 0.75rem;"><i class="fa-solid fa-check" style="color:var(--status-success);"></i> <?php echo htmlspecialchars($fac); ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Equipment List -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-microchip" style="color:var(--secondary-teal);"></i> Installed Equipment Inventory (<?php echo count($equipmentList); ?>)</h3>
    </div>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Equipment Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Purchase Date</th>
                    <th>Warranty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($equipmentList) > 0): ?>
                    <?php foreach ($equipmentList as $eq): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($eq['equipment_code']); ?></strong></td>
                        <td><?php echo htmlspecialchars($eq['equipment_name']); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($eq['category']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($eq['quantity']); ?> Units</strong></td>
                        <td><?php echo htmlspecialchars($eq['purchase_date']); ?></td>
                        <td><?php echo htmlspecialchars($eq['warranty']); ?></td>
                        <td>
                            <?php if ($eq['status'] === 'Available'): ?>
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Available</span>
                            <?php elseif ($eq['status'] === 'Under Maintenance'): ?>
                                <span class="badge badge-warning"><i class="fa-solid fa-wrench"></i> Under Maintenance</span>
                            <?php else: ?>
                                <span class="badge badge-info"><?php echo htmlspecialchars($eq['status']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:2rem; color:var(--text-muted);">No equipment items currently registered to this lab.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Lab Timetable Sessions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-calendar-days" style="color:var(--secondary-teal);"></i> Scheduled Practical Sessions</h3>
    </div>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Time Slot</th>
                    <th>Subject</th>
                    <th>Division & Batch</th>
                    <th>Faculty Incharge</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($schedulesList) > 0): ?>
                    <?php foreach ($schedulesList as $sch): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($sch['day_of_week']); ?></strong></td>
                        <td><i class="fa-regular fa-clock" style="color:var(--secondary-teal);"></i> <?php echo htmlspecialchars($sch['time_slot']); ?></td>
                        <td><span class="badge badge-info"><?php echo htmlspecialchars($sch['subject']); ?></span></td>
                        <td>Division <strong><?php echo htmlspecialchars($sch['division']); ?></strong> (<?php echo htmlspecialchars($sch['batch']); ?>)</td>
                        <td><?php echo htmlspecialchars($sch['faculty_name']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:2rem; color:var(--text-muted);">No scheduled practical sessions found for this lab.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>
