<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Module 5: Lab Schedule (Weekly Timetable)
// ====================================================================
require_once 'includes/db.php';

$pageTitle = "Lab Schedule | AIML AcademicHub";

$timeSlots = [
    '8:00 AM – 10:00 AM',
    '10:30 AM – 12:30 PM',
    '1:15 PM – 3:15 PM'
];

$daysOfWeek = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday'
];

// Fetch all schedule entries joined with lab details
$schedulesGrid = [];
try {
    $stmt = $pdo->query("SELECT s.*, l.lab_name, l.location FROM schedules s JOIN labs l ON s.lab_id = l.id");
    $rawSchedules = $stmt->fetchAll();

    foreach ($rawSchedules as $sch) {
        $day = $sch['day_of_week'];
        $slot = $sch['time_slot'];
        if (!isset($schedulesGrid[$day][$slot])) {
            $schedulesGrid[$day][$slot] = [];
        }
        $schedulesGrid[$day][$slot][] = $sch;
    }
} catch (PDOException $e) {
    //
}

include('header.php');
include('sidebar.php');
?>

<div class="section-title-wrapper">
    <div>
        <h2 class="section-title"><i class="fa-solid fa-calendar-days"></i> Laboratory Practical Schedule</h2>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-top:0.2rem;">Weekly practical timetable for 1st Year (2nd Semester) engineering batches.</p>
    </div>
    <button class="btn btn-outline" onclick="printTimetable()">
        <i class="fa-solid fa-print"></i> Print Schedule
    </button>
</div>

<!-- Interactive Filter Bar -->
<div class="schedule-filter-bar">
    <div class="filter-group">
        <div class="filter-item">
            <label><i class="fa-solid fa-graduation-cap"></i> Academic Year:</label>
            <select id="filterYear" class="custom-select" disabled style="background-color:rgba(86,124,141,0.1); font-weight:600;">
                <option value="1st Year" selected>1st Year (Semester 2)</option>
            </select>
        </div>

        <div class="filter-item">
            <label><i class="fa-solid fa-users-rectangle"></i> Division:</label>
            <select id="filterDivision" class="custom-select">
                <option value="ALL">All Divisions (A, B, C)</option>
                <option value="A">Division A</option>
                <option value="B">Division B</option>
                <option value="C">Division C</option>
            </select>
        </div>

        <div class="filter-item">
            <label><i class="fa-solid fa-people-group"></i> Batch:</label>
            <select id="filterBatch" class="custom-select">
                <option value="ALL">All Batches (1, 2, 3)</option>
                <option value="Batch 1">Batch 1</option>
                <option value="Batch 2">Batch 2</option>
                <option value="Batch 3">Batch 3</option>
            </select>
        </div>
    </div>

    <div style="font-size:0.8rem; color:var(--secondary-teal); font-weight:600;">
        <i class="fa-solid fa-circle-info"></i> Gaps indicate dedicated theory lecture slots
    </div>
</div>

<!-- Timetable Matrix Container -->
<div class="timetable-container" id="timetableContainer">
    <table class="timetable">
        <thead>
            <tr>
                <th style="width:140px;">Time Slot</th>
                <?php foreach ($daysOfWeek as $day): ?>
                    <th><?php echo $day; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($timeSlots as $slot): ?>
            <tr>
                <td class="time-slot-header">
                    <i class="fa-regular fa-clock"></i>
                    <div><?php echo $slot; ?></div>
                    <span style="font-size:0.7rem; font-weight:normal; color:var(--text-muted);">(2 Hours)</span>
                </td>

                <?php foreach ($daysOfWeek as $day): ?>
                <td>
                    <?php if (!empty($schedulesGrid[$day][$slot])): ?>
                        <div style="display:flex; flex-direction:column; gap:0.6rem; height:100%;">
                            <?php foreach ($schedulesGrid[$day][$slot] as $sch): ?>
                            <div class="schedule-card-item"
                                 data-division="<?php echo htmlspecialchars($sch['division']); ?>"
                                 data-batch="<?php echo htmlspecialchars($sch['batch']); ?>">
                                
                                <div class="schedule-subject">
                                    <span><i class="fa-solid fa-atom"></i> <?php echo htmlspecialchars($sch['subject']); ?></span>
                                    <span class="schedule-meta-badge">Div <?php echo htmlspecialchars($sch['division']); ?> (<?php echo htmlspecialchars($sch['batch']); ?>)</span>
                                </div>

                                <div class="schedule-details">
                                    <div><i class="fa-solid fa-user-tie"></i> <strong>Faculty:</strong> <?php echo htmlspecialchars($sch['faculty_name']); ?></div>
                                    <div><i class="fa-solid fa-flask"></i> <strong>Lab:</strong> <?php echo htmlspecialchars($sch['lab_name']); ?></div>
                                    <div><i class="fa-solid fa-location-dot"></i> <strong>Location:</strong> <?php echo htmlspecialchars($sch['location']); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="theory-gap-cell">
                            <i class="fa-solid fa-book-open" style="margin-right:0.3rem;"></i> Theory Lectures
                        </div>
                    <?php endif; ?>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
