<?php
require_once __DIR__ . '/includes/db.php';

function placed_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$search = trim((string) ($_GET['search'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 8;
$searchPattern = '%' . $search . '%';

$countStatement = $conn->prepare(
    'SELECT COUNT(*)
     FROM offers
     INNER JOIN applications ON applications.application_id = offers.application_id
     INNER JOIN students ON students.student_id = applications.student_id
     INNER JOIN placement_drives ON placement_drives.drive_id = applications.drive_id
     INNER JOIN companies ON companies.company_id = placement_drives.company_id
     WHERE offers.offer_status = \'Accepted\'
       AND (CONCAT(students.first_name, \' \', students.last_name) LIKE ?
            OR students.roll_number LIKE ?
            OR companies.company_name LIKE ?
            OR placement_drives.job_role LIKE ?)'
);
$countStatement->bind_param('ssss', $searchPattern, $searchPattern, $searchPattern, $searchPattern);
$countStatement->execute();
$countStatement->bind_result($totalPlaced);
$countStatement->fetch();
$countStatement->close();

$totalPages = max(1, (int) ceil($totalPlaced / $pageSize));
$page = min($page, $totalPages);
$offset = ($page - 1) * $pageSize;

$listStatement = $conn->prepare(
    'SELECT students.first_name, students.last_name, students.roll_number,
            companies.company_name, placement_drives.job_role,
            offers.offered_package_lpa, offers.joining_date, offers.offer_status
     FROM offers
     INNER JOIN applications ON applications.application_id = offers.application_id
     INNER JOIN students ON students.student_id = applications.student_id
     INNER JOIN placement_drives ON placement_drives.drive_id = applications.drive_id
     INNER JOIN companies ON companies.company_id = placement_drives.company_id
     WHERE offers.offer_status = \'Accepted\'
       AND (CONCAT(students.first_name, \' \', students.last_name) LIKE ?
            OR students.roll_number LIKE ?
            OR companies.company_name LIKE ?
            OR placement_drives.job_role LIKE ?)
     ORDER BY offers.joining_date IS NULL ASC, offers.joining_date ASC, students.last_name ASC
     LIMIT ? OFFSET ?'
);
$listStatement->bind_param('ssssii', $searchPattern, $searchPattern, $searchPattern, $searchPattern, $pageSize, $offset);
$listStatement->execute();
$placedStudents = $listStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$listStatement->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placed Students - Academic ERP Placement</title>
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
                        <p class="text-uppercase text-primary mb-2 small fw-semibold">Placement outcomes</p>
                        <h1 class="h3 mb-1">Placed Students</h1>
                        <p class="text-muted mb-0">Students with accepted placement offers and confirmed career outcomes.</p>
                    </div>
                    <span class="badge rounded-pill text-bg-success fs-6 px-3 py-2"><?php echo number_format($totalPlaced); ?> placed</span>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="get" class="row g-2 align-items-center" role="search">
                            <div class="col-12 col-md">
                                <label for="placedSearch" class="visually-hidden">Search placed students</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" id="placedSearch" name="search" value="<?php echo placed_escape($search); ?>" placeholder="Search student, PRN, company or role">
                                </div>
                            </div>
                            <div class="col-12 col-md-auto d-flex gap-2">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Search</button>
                                <?php if ($search !== ''): ?><a class="btn btn-outline-secondary" href="placed-students.php">Clear</a><?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                        <div>
                            <h2 class="h6 mb-1">Placement records</h2>
                            <p class="text-muted small mb-0">Accepted offers only.</p>
                        </div>
                        <span class="text-muted small">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Company</th>
                                    <th>Package</th>
                                    <th>Role</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($placedStudents): ?>
                                    <?php foreach ($placedStudents as $student): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo placed_escape($student['first_name'] . ' ' . $student['last_name']); ?></strong>
                                                <small class="d-block text-muted"><?php echo placed_escape($student['roll_number']); ?></small>
                                            </td>
                                            <td><?php echo placed_escape($student['company_name']); ?></td>
                                            <td><?php echo number_format((float) $student['offered_package_lpa'], 2); ?> LPA</td>
                                            <td><?php echo placed_escape($student['job_role']); ?></td>
                                            <td><?php echo $student['joining_date'] ? date('d M Y', strtotime($student['joining_date'])) : 'Not scheduled'; ?></td>
                                            <td><span class="badge rounded-pill text-bg-success"><i class="bi bi-check-circle me-1"></i><?php echo placed_escape($student['offer_status']); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-award display-6 d-block mb-2"></i>
                                            No placed students found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4" aria-label="Placed student pagination">
                        <ul class="pagination justify-content-center">
                            <?php for ($paginationPage = 1; $paginationPage <= $totalPages; $paginationPage++): ?>
                                <li class="page-item <?php echo $paginationPage === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="placed-students.php?<?php echo http_build_query(['search' => $search, 'page' => $paginationPage]); ?>"><?php echo $paginationPage; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
