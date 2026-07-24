<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION)) {
    $_SESSION = [];
}

function drives_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function drives_redirect(string $search = '', string $status = '', int $page = 1): void
{
    $query = http_build_query(array_filter([
        'search' => $search,
        'status' => $status,
        'page' => $page > 1 ? $page : null,
    ], static fn ($value): bool => $value !== null && $value !== ''));

    header('Location: placement-drives.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

function drives_set_flash(string $type, string $message): void
{
    $_SESSION['drives_flash'] = ['type' => $type, 'message' => $message];
}

function drives_form_fields(array $companies, ?array $drive = null): void
{
    $prefix = $drive ? 'edit' . (int) $drive['drive_id'] : 'add';
    $selectedCompany = $drive['company_id'] ?? '';
    $selectedStatus = $drive['status'] ?? 'Scheduled';
    $statuses = ['Scheduled', 'Open', 'Closed', 'Completed'];
    ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="<?php echo $prefix; ?>Company" class="form-label">Company</label>
            <select class="form-select" id="<?php echo $prefix; ?>Company" name="company_id" required>
                <option value="">Select company</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?php echo (int) $company['company_id']; ?>" <?php echo (string) $selectedCompany === (string) $company['company_id'] ? 'selected' : ''; ?>><?php echo drives_escape($company['company_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label for="<?php echo $prefix; ?>Title" class="form-label">Drive Title</label>
            <input type="text" class="form-control" id="<?php echo $prefix; ?>Title" name="drive_title" value="<?php echo drives_escape($drive['drive_title'] ?? ''); ?>" maxlength="180" required>
        </div>
        <div class="col-md-6">
            <label for="<?php echo $prefix; ?>Role" class="form-label">Role</label>
            <input type="text" class="form-control" id="<?php echo $prefix; ?>Role" name="job_role" value="<?php echo drives_escape($drive['job_role'] ?? ''); ?>" maxlength="150" required>
        </div>
        <div class="col-md-6">
            <label for="<?php echo $prefix; ?>Package" class="form-label">Package (LPA)</label>
            <input type="number" class="form-control" id="<?php echo $prefix; ?>Package" name="package_lpa" value="<?php echo $drive ? drives_escape((string) $drive['package_lpa']) : ''; ?>" min="0" step="0.01" required>
        </div>
        <div class="col-md-6">
            <label for="<?php echo $prefix; ?>Date" class="form-label">Drive Date</label>
            <input type="date" class="form-control" id="<?php echo $prefix; ?>Date" name="drive_date" value="<?php echo drives_escape($drive['drive_date'] ?? ''); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="<?php echo $prefix; ?>Deadline" class="form-label">Application Deadline</label>
            <input type="date" class="form-control" id="<?php echo $prefix; ?>Deadline" name="application_deadline" value="<?php echo drives_escape($drive['application_deadline'] ?? ''); ?>" required>
        </div>
        <div class="col-md-8">
            <label for="<?php echo $prefix; ?>Eligibility" class="form-label">Eligibility</label>
            <textarea class="form-control" id="<?php echo $prefix; ?>Eligibility" name="eligibility_criteria" rows="3" required><?php echo drives_escape($drive['eligibility_criteria'] ?? ''); ?></textarea>
        </div>
        <div class="col-md-4">
            <label for="<?php echo $prefix; ?>Status" class="form-label">Status</label>
            <select class="form-select" id="<?php echo $prefix; ?>Status" name="status" required>
                <?php foreach ($statuses as $status): ?>
                    <option value="<?php echo drives_escape($status); ?>" <?php echo $selectedStatus === $status ? 'selected' : ''; ?>><?php echo drives_escape($status); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php
}

$allowedStatuses = ['Scheduled', 'Open', 'Closed', 'Completed'];

if (empty($_SESSION['drives_csrf_token'])) {
    $_SESSION['drives_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['drives_csrf_token'];
$postedSearch = trim((string) ($_POST['return_search'] ?? ''));
$postedStatus = (string) ($_POST['return_status'] ?? '');
$postedPage = max(1, (int) ($_POST['return_page'] ?? 1));

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($csrfToken, $submittedToken)) {
        drives_set_flash('danger', 'Your session expired. Please try again.');
        drives_redirect($postedSearch, $postedStatus, $postedPage);
    }

    $action = (string) ($_POST['action'] ?? '');

    try {
        if ($action === 'add' || $action === 'update') {
            $companyId = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);
            $driveTitle = trim((string) ($_POST['drive_title'] ?? ''));
            $jobRole = trim((string) ($_POST['job_role'] ?? ''));
            $packageLpa = filter_input(INPUT_POST, 'package_lpa', FILTER_VALIDATE_FLOAT);
            $driveDate = trim((string) ($_POST['drive_date'] ?? ''));
            $applicationDeadline = trim((string) ($_POST['application_deadline'] ?? ''));
            $eligibilityCriteria = trim((string) ($_POST['eligibility_criteria'] ?? ''));
            $driveStatus = (string) ($_POST['status'] ?? 'Scheduled');

            if (!$companyId || $driveTitle === '' || $jobRole === '' || $packageLpa === false || $packageLpa < 0 || $driveDate === '' || $applicationDeadline === '' || $eligibilityCriteria === '') {
                throw new InvalidArgumentException('Please complete all required drive fields with valid values.');
            }

            if (!in_array($driveStatus, $allowedStatuses, true)) {
                throw new InvalidArgumentException('Please select a valid drive status.');
            }

            $datePattern = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($datePattern, $driveDate) || !preg_match($datePattern, $applicationDeadline)) {
                throw new InvalidArgumentException('Please provide valid drive and application deadline dates.');
            }

            if ($applicationDeadline > $driveDate) {
                throw new InvalidArgumentException('The application deadline cannot be after the drive date.');
            }

            if ($action === 'add') {
                $statement = $conn->prepare(
                    'INSERT INTO placement_drives
                        (company_id, drive_title, job_role, package_lpa, drive_date, application_deadline, eligibility_criteria, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $statement->bind_param('issdssss', $companyId, $driveTitle, $jobRole, $packageLpa, $driveDate, $applicationDeadline, $eligibilityCriteria, $driveStatus);
                $statement->execute();
                $statement->close();
                drives_set_flash('success', 'Placement drive added successfully.');
            } else {
                $driveId = filter_input(INPUT_POST, 'drive_id', FILTER_VALIDATE_INT);

                if (!$driveId) {
                    throw new InvalidArgumentException('The selected drive is invalid.');
                }

                $statement = $conn->prepare(
                    'UPDATE placement_drives
                     SET company_id = ?, drive_title = ?, job_role = ?, package_lpa = ?, drive_date = ?,
                         application_deadline = ?, eligibility_criteria = ?, status = ?
                     WHERE drive_id = ?'
                );
                $statement->bind_param('issdssssi', $companyId, $driveTitle, $jobRole, $packageLpa, $driveDate, $applicationDeadline, $eligibilityCriteria, $driveStatus, $driveId);
                $statement->execute();
                $statement->close();
                drives_set_flash('success', 'Placement drive updated successfully.');
            }
        } elseif ($action === 'delete') {
            $driveId = filter_input(INPUT_POST, 'drive_id', FILTER_VALIDATE_INT);

            if (!$driveId) {
                throw new InvalidArgumentException('The selected drive is invalid.');
            }

            $statement = $conn->prepare('DELETE FROM placement_drives WHERE drive_id = ?');
            $statement->bind_param('i', $driveId);
            $statement->execute();
            $statement->close();
            drives_set_flash('success', 'Placement drive deleted successfully.');
        } else {
            throw new InvalidArgumentException('Unsupported placement drive action.');
        }
    } catch (InvalidArgumentException $exception) {
        drives_set_flash('danger', $exception->getMessage());
    } catch (mysqli_sql_exception $exception) {
        error_log('Placement drive query failed: ' . $exception->getMessage());
        $message = $exception->getCode() === 1451
            ? 'This drive cannot be deleted because students have applied to it.'
            : 'The placement drive could not be saved.';
        drives_set_flash('danger', $message);
    }

    drives_redirect($postedSearch, $postedStatus, $postedPage);
}

$search = trim((string) ($_GET['search'] ?? ''));
$statusFilter = (string) ($_GET['status'] ?? '');
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = '';
}
$page = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 6;
$flash = $_SESSION['drives_flash'] ?? null;
unset($_SESSION['drives_flash']);

$statsStatement = $conn->prepare(
    'SELECT
        (SELECT COUNT(*) FROM placement_drives WHERE drive_date >= CURDATE() AND status IN (\'Scheduled\', \'Open\')) AS upcoming_drives,
        (SELECT COUNT(*) FROM placement_drives WHERE status = \'Open\') AS open_drives,
        (SELECT COUNT(DISTINCT company_id) FROM placement_drives) AS visited_companies'
);
$statsStatement->execute();
$stats = $statsStatement->get_result()->fetch_assoc();
$statsStatement->close();

$companiesStatement = $conn->prepare('SELECT company_id, company_name FROM companies ORDER BY company_name ASC');
$companiesStatement->execute();
$companies = $companiesStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$companiesStatement->close();

$searchPattern = '%' . $search . '%';
$countStatement = $conn->prepare(
    'SELECT COUNT(*)
     FROM placement_drives
     INNER JOIN companies ON companies.company_id = placement_drives.company_id
     WHERE (companies.company_name LIKE ? OR placement_drives.job_role LIKE ? OR placement_drives.eligibility_criteria LIKE ?)
       AND (? = \'\' OR placement_drives.status = ?)'
);
$countStatement->bind_param('sssss', $searchPattern, $searchPattern, $searchPattern, $statusFilter, $statusFilter);
$countStatement->execute();
$countStatement->bind_result($totalDrives);
$countStatement->fetch();
$countStatement->close();

$totalPages = max(1, (int) ceil($totalDrives / $pageSize));
$page = min($page, $totalPages);
$offset = ($page - 1) * $pageSize;

$listStatement = $conn->prepare(
    'SELECT placement_drives.drive_id, placement_drives.company_id, companies.company_name,
            placement_drives.drive_title, placement_drives.job_role, placement_drives.package_lpa,
            placement_drives.drive_date, placement_drives.application_deadline,
            placement_drives.eligibility_criteria, placement_drives.status
     FROM placement_drives
     INNER JOIN companies ON companies.company_id = placement_drives.company_id
     WHERE (companies.company_name LIKE ? OR placement_drives.job_role LIKE ? OR placement_drives.eligibility_criteria LIKE ?)
       AND (? = \'\' OR placement_drives.status = ?)
     ORDER BY placement_drives.drive_date ASC, placement_drives.drive_id DESC
     LIMIT ? OFFSET ?'
);
$listStatement->bind_param('sssssii', $searchPattern, $searchPattern, $searchPattern, $statusFilter, $statusFilter, $pageSize, $offset);
$listStatement->execute();
$drives = $listStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$listStatement->close();

$statusClasses = [
    'Scheduled' => 'text-bg-primary',
    'Open' => 'text-bg-success',
    'Closed' => 'text-bg-secondary',
    'Completed' => 'text-bg-dark',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Drives - Academic ERP</title>
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
            <main class="page-shell py-4">
                <header class="d-flex flex-column flex-md-row align-items-start align-items-md-end justify-content-between gap-3 mb-4">
                    <div>
                        <p class="section-kicker mb-2">Recruitment calendar</p>
                        <h1 class="page-title mb-2">Placement Drives</h1>
                        <p class="page-subtitle mb-0">Plan upcoming visits and keep every campus drive on schedule.</p>
                    </div>
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addDriveModal"><i class="bi bi-plus-lg me-2"></i>Add Drive</button>
                </header>

                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo drives_escape($flash['type']); ?> alert-dismissible fade show" role="alert">
                        <?php echo drives_escape($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-4"><div class="card card-hover h-100"><div class="card-body"><div class="card-title">Upcoming</div><div class="card-metric"><?php echo number_format((int) $stats['upcoming_drives']); ?></div><div class="card-note"><i class="bi bi-calendar3 me-1"></i>Future scheduled and open drives</div></div></div></div>
                    <div class="col-12 col-sm-4"><div class="card card-hover h-100"><div class="card-body"><div class="card-title">Open registrations</div><div class="card-metric text-success"><?php echo number_format((int) $stats['open_drives']); ?></div><div class="card-note">Students can apply now</div></div></div></div>
                    <div class="col-12 col-sm-4"><div class="card card-hover h-100"><div class="card-body"><div class="card-title">Companies visited</div><div class="card-metric"><?php echo number_format((int) $stats['visited_companies']); ?></div><div class="card-note"><i class="bi bi-building me-1"></i>Companies with scheduled drives</div></div></div></div>
                </div>

                <section class="card border-0">
                    <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div><h2 class="h6 mb-1">Drive schedule</h2><p class="text-muted small mb-0">Current placement activity and registration status.</p></div>
                        <form method="get" class="row g-2 w-100 w-md-auto" role="search">
                            <div class="col"><label for="driveSearch" class="visually-hidden">Search drives</label><input type="search" class="form-control form-control-sm" id="driveSearch" name="search" value="<?php echo drives_escape($search); ?>" placeholder="Search drives"></div>
                            <div class="col-auto"><label for="driveStatus" class="visually-hidden">Filter by drive status</label><select class="form-select form-select-sm" id="driveStatus" name="status"><option value="">All statuses</option><?php foreach ($allowedStatuses as $option): ?><option value="<?php echo drives_escape($option); ?>" <?php echo $statusFilter === $option ? 'selected' : ''; ?>><?php echo drives_escape($option); ?></option><?php endforeach; ?></select></div>
                            <div class="col-auto"><button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-search me-1"></i>Search</button></div>
                        </form>
                    </div>
                    <div class="table-responsive border-0 shadow-none">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Company</th><th>Role</th><th>Package</th><th>Drive Date</th><th>Eligibility</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                            <?php if ($drives): ?>
                                <?php foreach ($drives as $drive): ?>
                                    <tr>
                                        <td><strong><?php echo drives_escape($drive['company_name']); ?></strong><small class="d-block text-muted"><?php echo drives_escape($drive['drive_title']); ?></small></td>
                                        <td><?php echo drives_escape($drive['job_role']); ?></td>
                                        <td><?php echo number_format((float) $drive['package_lpa'], 2); ?> LPA</td>
                                        <td><?php echo date('d M Y', strtotime($drive['drive_date'])); ?><small class="d-block text-muted">Apply by <?php echo date('d M Y', strtotime($drive['application_deadline'])); ?></small></td>
                                        <td class="text-wrap" style="min-width: 180px;"><?php echo drives_escape($drive['eligibility_criteria']); ?></td>
                                        <td><span class="badge rounded-pill <?php echo $statusClasses[$drive['status']] ?? 'text-bg-secondary'; ?>"><?php echo drives_escape($drive['status']); ?></span></td>
                                        <td class="text-end text-nowrap">
                                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editDriveModal<?php echo (int) $drive['drive_id']; ?>"><i class="bi bi-pencil"></i><span class="visually-hidden"> Edit</span></button>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Delete this placement drive?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo drives_escape($csrfToken); ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="drive_id" value="<?php echo (int) $drive['drive_id']; ?>"><input type="hidden" name="return_search" value="<?php echo drives_escape($search); ?>"><input type="hidden" name="return_status" value="<?php echo drives_escape($statusFilter); ?>"><input type="hidden" name="return_page" value="<?php echo $page; ?>">
                                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i><span class="visually-hidden"> Delete</span></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="editDriveModal<?php echo (int) $drive['drive_id']; ?>" tabindex="-1" aria-labelledby="editDriveLabel<?php echo (int) $drive['drive_id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><form method="post">
                                            <div class="modal-header"><h5 class="modal-title" id="editDriveLabel<?php echo (int) $drive['drive_id']; ?>">Edit Drive</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                                            <div class="modal-body">
                                                <input type="hidden" name="csrf_token" value="<?php echo drives_escape($csrfToken); ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="drive_id" value="<?php echo (int) $drive['drive_id']; ?>"><input type="hidden" name="return_search" value="<?php echo drives_escape($search); ?>"><input type="hidden" name="return_status" value="<?php echo drives_escape($statusFilter); ?>"><input type="hidden" name="return_page" value="<?php echo $page; ?>">
                                                <?php drives_form_fields($companies, $drive); ?>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button></div>
                                        </form></div></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center text-muted py-5">No placement drives found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mt-4">
                    <span class="text-muted small">Showing <?php echo number_format((int) $totalDrives); ?> drive<?php echo $totalDrives === 1 ? '' : 's'; ?> · Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <?php if ($totalPages > 1): ?><nav aria-label="Placement drive pagination"><ul class="pagination mb-0"><li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="placement-drives.php?<?php echo http_build_query(['search' => $search, 'status' => $statusFilter, 'page' => $page - 1]); ?>" aria-label="Previous"><i class="bi bi-chevron-left"></i></a></li><?php for ($paginationPage = 1; $paginationPage <= $totalPages; $paginationPage++): ?><li class="page-item <?php echo $paginationPage === $page ? 'active' : ''; ?>"><a class="page-link" href="placement-drives.php?<?php echo http_build_query(['search' => $search, 'status' => $statusFilter, 'page' => $paginationPage]); ?>"><?php echo $paginationPage; ?></a></li><?php endfor; ?><li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"><a class="page-link" href="placement-drives.php?<?php echo http_build_query(['search' => $search, 'status' => $statusFilter, 'page' => $page + 1]); ?>" aria-label="Next"><i class="bi bi-chevron-right"></i></a></li></ul></nav><?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</div>

<div class="modal fade" id="addDriveModal" tabindex="-1" aria-labelledby="addDriveLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><form method="post">
        <div class="modal-header"><h5 class="modal-title" id="addDriveLabel">Add Drive</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?php echo drives_escape($csrfToken); ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="return_search" value="<?php echo drives_escape($search); ?>"><input type="hidden" name="return_status" value="<?php echo drives_escape($statusFilter); ?>"><input type="hidden" name="return_page" value="<?php echo $page; ?>">
            <?php drives_form_fields($companies); ?>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Drive</button></div>
    </form></div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>