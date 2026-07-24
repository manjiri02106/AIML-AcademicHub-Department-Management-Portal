<?php
require_once __DIR__ . '/includes/db.php';

function eligible_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function eligible_matches_drive(array $student, ?float $minimumCgpa, array $eligibleDepartments): bool
{
    if ($minimumCgpa !== null && (float) $student['cgpa'] < $minimumCgpa) {
        return false;
    }

    return !$eligibleDepartments || in_array(strtoupper((string) $student['department']), $eligibleDepartments, true);
}

function eligible_criteria_summary(string $criteria, ?float $minimumCgpa, array $eligibleDepartments): string
{
    $summary = [];
    if ($minimumCgpa !== null) {
        $summary[] = 'CGPA ' . number_format($minimumCgpa, 2) . '+';
    }
    if ($eligibleDepartments) {
        $summary[] = 'Departments: ' . implode(', ', $eligibleDepartments);
    }

    return $summary ? implode(' · ', $summary) : $criteria;
}

$driveStatement = $conn->prepare(
    'SELECT placement_drives.drive_id, placement_drives.drive_title, placement_drives.job_role,
            placement_drives.eligibility_criteria, placement_drives.drive_date, placement_drives.status,
            companies.company_name
     FROM placement_drives
     INNER JOIN companies ON companies.company_id = placement_drives.company_id
     ORDER BY placement_drives.drive_date ASC, placement_drives.drive_id DESC'
);
$driveStatement->execute();
$drives = $driveStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$driveStatement->close();

$selectedDriveId = filter_input(INPUT_GET, 'drive_id', FILTER_VALIDATE_INT);
$selectedDrive = null;
foreach ($drives as $drive) {
    if ((int) $drive['drive_id'] === (int) $selectedDriveId) {
        $selectedDrive = $drive;
        break;
    }
}
if ($selectedDrive === null && $drives) {
    $selectedDrive = $drives[0];
    $selectedDriveId = (int) $selectedDrive['drive_id'];
}

$search = trim((string) ($_GET['search'] ?? ''));
$departmentFilter = strtoupper(trim((string) ($_GET['department'] ?? '')));
$minimumCgpaFilter = (string) ($_GET['min_cgpa'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 8;
$departments = ['CSE', 'IT', 'ECE', 'ME', 'CE', 'AIML'];
if (!in_array($departmentFilter, $departments, true)) {
    $departmentFilter = '';
}
if ($minimumCgpaFilter !== '' && (!is_numeric($minimumCgpaFilter) || (float) $minimumCgpaFilter < 0 || (float) $minimumCgpaFilter > 10)) {
    $minimumCgpaFilter = '';
}

$minimumCgpa = null;
$eligibleDepartments = [];
if ($selectedDrive) {
    $criteria = (string) $selectedDrive['eligibility_criteria'];
    if (preg_match('/\bcgpa\s*(?:of|is|>=|at least)?\s*(\d+(?:\.\d+)?)/i', $criteria, $cgpaMatch)) {
        $minimumCgpa = (float) $cgpaMatch[1];
    }

    $departmentRules = [
        'CSE' => '/\b(?:cse|computer\s+science)\b/i',
        'IT' => '/\b(?:it|information\s+technology)\b/i',
        'ECE' => '/\b(?:ece|electronics?)\b/i',
        'ME' => '/\b(?:me|mechanical)\b/i',
        'CE' => '/\b(?:ce|civil)\b/i',
        'AIML' => '/\b(?:aiml|artificial\s+intelligence|machine\s+learning)\b/i',
    ];
    foreach ($departmentRules as $department => $pattern) {
        if (preg_match($pattern, $criteria)) {
            $eligibleDepartments[] = $department;
        }
    }
}

$searchPattern = '%' . $search . '%';
$studentStatement = $conn->prepare(
    'SELECT student_id, roll_number, first_name, last_name, email, phone, department, branch, skills, cgpa
     FROM students
     WHERE (? = \'\'
        OR CONCAT(first_name, \' \', last_name) LIKE ?
        OR roll_number LIKE ?
        OR department LIKE ?
        OR branch LIKE ?
        OR skills LIKE ?)
     ORDER BY last_name ASC, first_name ASC, student_id ASC'
);
$studentStatement->bind_param('ssssss', $search, $searchPattern, $searchPattern, $searchPattern, $searchPattern, $searchPattern);
$studentStatement->execute();
$allStudents = $studentStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$studentStatement->close();

$eligibleStudents = array_values(array_filter($allStudents, static function (array $student) use ($minimumCgpa, $eligibleDepartments, $departmentFilter, $minimumCgpaFilter): bool {
    if (!eligible_matches_drive($student, $minimumCgpa, $eligibleDepartments)) {
        return false;
    }
    if ($departmentFilter !== '' && strtoupper((string) $student['department']) !== $departmentFilter) {
        return false;
    }
    return $minimumCgpaFilter === '' || (float) $student['cgpa'] >= (float) $minimumCgpaFilter;
}));

$totalEligible = count($eligibleStudents);
$totalPages = max(1, (int) ceil($totalEligible / $pageSize));
$page = min($page, $totalPages);
$pagedStudents = array_slice($eligibleStudents, ($page - 1) * $pageSize, $pageSize);
$criteriaSummary = $selectedDrive ? eligible_criteria_summary((string) $selectedDrive['eligibility_criteria'], $minimumCgpa, $eligibleDepartments) : 'Select a drive to view matching students.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eligible Students - Academic ERP Placement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row gx-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>
        <div class="col-12 col-lg-9">
            <main class="py-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-primary mb-2 small fw-semibold">Student matching</p>
                        <h1 class="h3 mb-1">Eligible Students</h1>
                        <p class="text-muted mb-0">Find students who meet the selected placement drive requirements.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-primary fs-6 px-3 py-2"><?php echo number_format($totalEligible); ?> eligible</span>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="get" class="row g-3 align-items-end">
                            <div class="col-12 col-xl-4">
                                <label for="driveId" class="form-label">Placement Drive</label>
                                <select class="form-select" id="driveId" name="drive_id" required>
                                    <?php if (!$drives): ?><option value="">No placement drives available</option><?php endif; ?>
                                    <?php foreach ($drives as $drive): ?>
                                        <option value="<?php echo (int) $drive['drive_id']; ?>" <?php echo (int) $selectedDriveId === (int) $drive['drive_id'] ? 'selected' : ''; ?>><?php echo eligible_escape($drive['company_name'] . ' - ' . $drive['job_role']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-xl-3">
                                <label for="studentSearch" class="form-label">Search</label>
                                <input type="search" class="form-control" id="studentSearch" name="search" value="<?php echo eligible_escape($search); ?>" placeholder="Name, PRN or skill">
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department">
                                    <option value="">All</option>
                                    <?php foreach ($departments as $department): ?><option value="<?php echo eligible_escape($department); ?>" <?php echo $departmentFilter === $department ? 'selected' : ''; ?>><?php echo eligible_escape($department); ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-xl-2">
                                <label for="minCgpa" class="form-label">Minimum CGPA</label>
                                <input type="number" class="form-control" id="minCgpa" name="min_cgpa" value="<?php echo eligible_escape($minimumCgpaFilter); ?>" min="0" max="10" step="0.01" placeholder="Any">
                            </div>
                            <div class="col-12 col-xl-1 d-grid">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($selectedDrive): ?>
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex gap-2"><i class="bi bi-info-circle fs-5"></i><div><strong><?php echo eligible_escape($selectedDrive['company_name'] . ' - ' . $selectedDrive['job_role']); ?></strong><div class="small mt-1">Eligibility applied: <?php echo eligible_escape($criteriaSummary); ?></div></div></div>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <div><h2 class="h6 mb-1">Matching students</h2><p class="text-muted small mb-0">Only students who satisfy the selected drive criteria are shown.</p></div>
                        <span class="text-muted small">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>Student</th><th>PRN</th><th>Department</th><th>Branch</th><th>CGPA</th><th>Skills</th><th>Contact</th></tr></thead>
                            <tbody>
                            <?php if ($pagedStudents): ?>
                                <?php foreach ($pagedStudents as $student): ?>
                                    <tr>
                                        <td><strong><?php echo eligible_escape($student['first_name'] . ' ' . $student['last_name']); ?></strong></td>
                                        <td><?php echo eligible_escape($student['roll_number']); ?></td>
                                        <td><?php echo eligible_escape($student['department']); ?></td>
                                        <td><?php echo eligible_escape($student['branch']); ?></td>
                                        <td><span class="badge text-bg-success"><?php echo number_format((float) $student['cgpa'], 2); ?></span></td>
                                        <td style="min-width: 220px;"><?php echo eligible_escape($student['skills']); ?></td>
                                        <td><a href="mailto:<?php echo eligible_escape($student['email']); ?>"><?php echo eligible_escape($student['email']); ?></a><small class="d-block text-muted"><?php echo eligible_escape($student['phone']); ?></small></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-person-x display-6 d-block mb-2"></i>No eligible students match these filters.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4" aria-label="Eligible student pagination"><ul class="pagination justify-content-center">
                        <?php for ($paginationPage = 1; $paginationPage <= $totalPages; $paginationPage++): ?>
                            <li class="page-item <?php echo $paginationPage === $page ? 'active' : ''; ?>"><a class="page-link" href="eligible-students.php?<?php echo http_build_query(['drive_id' => $selectedDriveId, 'search' => $search, 'department' => $departmentFilter, 'min_cgpa' => $minimumCgpaFilter, 'page' => $paginationPage]); ?>"><?php echo $paginationPage; ?></a></li>
                        <?php endfor; ?>
                    </ul></nav>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>