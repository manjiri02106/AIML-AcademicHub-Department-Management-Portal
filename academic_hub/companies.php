<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION)) {
    $_SESSION = [];
}

function companies_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function companies_redirect(string $search = '', int $page = 1): never
{
    $query = http_build_query(array_filter([
        'search' => $search,
        'page' => $page > 1 ? $page : null,
    ], static fn ($value): bool => $value !== null && $value !== ''));

    header('Location: companies.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

function companies_set_flash(string $type, string $message): void
{
    $_SESSION['companies_flash'] = ['type' => $type, 'message' => $message];
}

if (empty($_SESSION['companies_csrf_token'])) {
    $_SESSION['companies_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['companies_csrf_token'];
$postedSearch = trim((string) ($_POST['return_search'] ?? ''));
$postedPage = max(1, (int) ($_POST['return_page'] ?? 1));

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($csrfToken, $submittedToken)) {
        companies_set_flash('danger', 'Your session expired. Please try again.');
        companies_redirect($postedSearch, $postedPage);
    }

    $action = (string) ($_POST['action'] ?? '');

    try {
        if ($action === 'add' || $action === 'update') {
            $companyName = trim((string) ($_POST['company_name'] ?? ''));
            $industry = trim((string) ($_POST['industry'] ?? ''));
            $website = trim((string) ($_POST['website'] ?? ''));
            $contactPerson = trim((string) ($_POST['contact_person'] ?? ''));
            $contactEmail = trim((string) ($_POST['contact_email'] ?? ''));

            if ($companyName === '' || $industry === '') {
                throw new InvalidArgumentException('Company name and industry are required.');
            }

            if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException('Please enter a valid website URL.');
            }

            if ($contactEmail !== '' && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Please enter a valid contact email.');
            }

            if ($action === 'add') {
                $statement = $conn->prepare(
                    'INSERT INTO companies (company_name, industry, website, contact_person, contact_email)
                     VALUES (?, ?, NULLIF(?, \'\'), NULLIF(?, \'\'), NULLIF(?, \'\'))'
                );
                $statement->bind_param('sssss', $companyName, $industry, $website, $contactPerson, $contactEmail);
                $statement->execute();
                $statement->close();
                companies_set_flash('success', 'Company added successfully.');
            } else {
                $companyId = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);

                if (!$companyId) {
                    throw new InvalidArgumentException('The selected company is invalid.');
                }

                $statement = $conn->prepare(
                    'UPDATE companies
                     SET company_name = ?, industry = ?, website = NULLIF(?, \'\'),
                         contact_person = NULLIF(?, \'\'), contact_email = NULLIF(?, \'\')
                     WHERE company_id = ?'
                );
                $statement->bind_param('sssssi', $companyName, $industry, $website, $contactPerson, $contactEmail, $companyId);
                $statement->execute();
                $statement->close();
                companies_set_flash('success', 'Company updated successfully.');
            }
        } elseif ($action === 'delete') {
            $companyId = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);

            if (!$companyId) {
                throw new InvalidArgumentException('The selected company is invalid.');
            }

            $statement = $conn->prepare('DELETE FROM companies WHERE company_id = ?');
            $statement->bind_param('i', $companyId);
            $statement->execute();
            $statement->close();
            companies_set_flash('success', 'Company deleted successfully.');
        } else {
            throw new InvalidArgumentException('Unsupported company action.');
        }
    } catch (InvalidArgumentException $exception) {
        companies_set_flash('danger', $exception->getMessage());
    } catch (mysqli_sql_exception $exception) {
        error_log('Company management query failed: ' . $exception->getMessage());
        $message = $exception->getCode() === 1062
            ? 'A company with that name already exists.'
            : 'The company could not be saved. It may be linked to an existing placement drive.';
        companies_set_flash('danger', $message);
    }

    companies_redirect($postedSearch, $postedPage);
}

$search = trim((string) ($_GET['search'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$pageSize = 6;
$flash = $_SESSION['companies_flash'] ?? null;
unset($_SESSION['companies_flash']);

$countStatement = $conn->prepare(
    'SELECT COUNT(*)
     FROM companies
     WHERE company_name LIKE CONCAT("%", ?, "%")
        OR industry LIKE CONCAT("%", ?, "%")
        OR contact_person LIKE CONCAT("%", ?, "%")'
);
$countStatement->bind_param('sss', $search, $search, $search);
$countStatement->execute();
$countStatement->bind_result($totalCompanies);
$countStatement->fetch();
$countStatement->close();

$totalPages = max(1, (int) ceil($totalCompanies / $pageSize));
$page = min($page, $totalPages);
$offset = ($page - 1) * $pageSize;

$listStatement = $conn->prepare(
    'SELECT company_id, company_name, industry, website, contact_person, contact_email, created_at
     FROM companies
     WHERE company_name LIKE CONCAT("%", ?, "%")
        OR industry LIKE CONCAT("%", ?, "%")
        OR contact_person LIKE CONCAT("%", ?, "%")
     ORDER BY company_name ASC
     LIMIT ? OFFSET ?'
);
$listStatement->bind_param('sssii', $search, $search, $search, $pageSize, $offset);
$listStatement->execute();
$companies = $listStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$listStatement->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companies - Academic ERP Placement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .company-card { transition: transform 0.3s ease, box-shadow 0.3s ease; border: none; height: 100%; }
        .company-card:hover { transform: translateY(-8px); box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15); }
        .company-logo { width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 16px; background: linear-gradient(135deg, #0d6efd, #4dabf7); color: #fff; font-size: 1.75rem; font-weight: 600; margin-bottom: 1rem; }
        .company-name { font-size: 1.1rem; font-weight: 600; color: #212529; margin-bottom: 0.5rem; }
        .company-location { display: flex; align-items: center; gap: 0.5rem; color: #6c757d; font-size: 0.9rem; margin-bottom: 1rem; }
        .company-badge { display: inline-block; background: linear-gradient(135deg, #0d6efd, #4dabf7); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 500; font-size: 0.85rem; margin-bottom: 1rem; }
        .company-details { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .detail-item { flex: 1; min-width: 120px; }
        .detail-label { font-size: 0.75rem; color: #6c757d; text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem; }
        .detail-value { font-size: 0.95rem; font-weight: 600; color: #212529; overflow-wrap: anywhere; }
        .company-actions { display: flex; gap: 0.5rem; }
        .company-actions .btn { flex: 1; }
        @media (max-width: 575px) { .company-actions { flex-direction: column; } }
    </style>
</head>
<body class="bg-light">

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row gx-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>
        <div class="col-12 col-lg-9">
            <div class="py-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-primary mb-2 small fw-semibold">Companies</p>
                        <h1 class="h3 mb-0">Hiring Companies</h1>
                        <p class="text-muted mb-0">Manage companies participating in campus placement drives.</p>
                    </div>
                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Company
                    </button>
                </div>

                <?php if ($flash): ?>
                    <div class="alert alert-<?php echo companies_escape($flash['type']); ?> alert-dismissible fade show" role="alert">
                        <?php echo companies_escape($flash['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form method="get" class="row g-2 align-items-center" role="search">
                            <div class="col-12 col-md">
                                <label for="companySearch" class="visually-hidden">Search companies</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" id="companySearch" name="search" value="<?php echo companies_escape($search); ?>" placeholder="Search by company, industry or contact person">
                                </div>
                            </div>
                            <div class="col-12 col-md-auto d-flex gap-2">
                                <button class="btn btn-outline-primary" type="submit">Search</button>
                                <?php if ($search !== ''): ?>
                                    <a class="btn btn-outline-secondary" href="companies.php">Clear</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">Showing <?php echo number_format($totalCompanies); ?> compan<?php echo $totalCompanies === 1 ? 'y' : 'ies'; ?></p>
                    <span class="text-muted small">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                </div>

                <?php if ($companies): ?>
                    <div class="row g-4 mb-4">
                        <?php foreach ($companies as $company): ?>
                            <?php
                            $companyName = (string) $company['company_name'];
                            $initials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $companyName), 0, 2));
                            ?>
                            <div class="col-12 col-sm-6 col-xl-4">
                                <div class="card company-card shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <div class="company-logo" aria-hidden="true"><?php echo companies_escape($initials); ?></div>
                                        <div class="company-name"><?php echo companies_escape($companyName); ?></div>
                                        <div class="company-location"><i class="bi bi-building"></i> <?php echo companies_escape($company['industry']); ?></div>
                                        <div class="company-badge"><?php echo companies_escape($company['contact_person'] ?: 'No contact person'); ?></div>
                                        <div class="company-details">
                                            <div class="detail-item">
                                                <div class="detail-label">Contact Email</div>
                                                <div class="detail-value"><?php echo companies_escape($company['contact_email'] ?: 'Not provided'); ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Website</div>
                                                <div class="detail-value">
                                                    <?php if (!empty($company['website'])): ?>
                                                        <a href="<?php echo companies_escape($company['website']); ?>" target="_blank" rel="noopener noreferrer">Visit site</a>
                                                    <?php else: ?>
                                                        Not provided
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="company-actions mt-auto">
                                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editCompanyModal<?php echo (int) $company['company_id']; ?>">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </button>
                                            <form method="post" class="flex-fill" onsubmit="return confirm('Delete this company? Companies linked to placement drives cannot be deleted.');">
                                                <input type="hidden" name="csrf_token" value="<?php echo companies_escape($csrfToken); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="company_id" value="<?php echo (int) $company['company_id']; ?>">
                                                <input type="hidden" name="return_search" value="<?php echo companies_escape($search); ?>">
                                                <input type="hidden" name="return_page" value="<?php echo $page; ?>">
                                                <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-trash me-1"></i> Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="editCompanyModal<?php echo (int) $company['company_id']; ?>" tabindex="-1" aria-labelledby="editCompanyLabel<?php echo (int) $company['company_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editCompanyLabel<?php echo (int) $company['company_id']; ?>">Edit Company</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="csrf_token" value="<?php echo companies_escape($csrfToken); ?>">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="company_id" value="<?php echo (int) $company['company_id']; ?>">
                                                <input type="hidden" name="return_search" value="<?php echo companies_escape($search); ?>">
                                                <input type="hidden" name="return_page" value="<?php echo $page; ?>">
                                                <div class="mb-3">
                                                    <label for="editCompanyName<?php echo (int) $company['company_id']; ?>" class="form-label">Company Name</label>
                                                    <input type="text" class="form-control" id="editCompanyName<?php echo (int) $company['company_id']; ?>" name="company_name" value="<?php echo companies_escape($company['company_name']); ?>" maxlength="150" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="editIndustry<?php echo (int) $company['company_id']; ?>" class="form-label">Industry</label>
                                                    <input type="text" class="form-control" id="editIndustry<?php echo (int) $company['company_id']; ?>" name="industry" value="<?php echo companies_escape($company['industry']); ?>" maxlength="100" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="editWebsite<?php echo (int) $company['company_id']; ?>" class="form-label">Website</label>
                                                    <input type="url" class="form-control" id="editWebsite<?php echo (int) $company['company_id']; ?>" name="website" value="<?php echo companies_escape($company['website']); ?>" maxlength="255" placeholder="https://example.com">
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="editContactPerson<?php echo (int) $company['company_id']; ?>" class="form-label">Contact Person</label>
                                                        <input type="text" class="form-control" id="editContactPerson<?php echo (int) $company['company_id']; ?>" name="contact_person" value="<?php echo companies_escape($company['contact_person']); ?>" maxlength="120">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="editContactEmail<?php echo (int) $company['company_id']; ?>" class="form-label">Contact Email</label>
                                                        <input type="email" class="form-control" id="editContactEmail<?php echo (int) $company['company_id']; ?>" name="contact_email" value="<?php echo companies_escape($company['contact_email']); ?>" maxlength="150">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-building display-5 text-muted"></i>
                            <h5 class="mt-3">No companies found</h5>
                            <p class="text-muted mb-0">Try a different search or add a new company.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Company pagination">
                        <ul class="pagination justify-content-center">
                            <?php $previousQuery = http_build_query(['search' => $search, 'page' => $page - 1]); ?>
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="companies.php?<?php echo $previousQuery; ?>" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>
                            </li>
                            <?php for ($paginationPage = 1; $paginationPage <= $totalPages; $paginationPage++): ?>
                                <li class="page-item <?php echo $paginationPage === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="companies.php?<?php echo http_build_query(['search' => $search, 'page' => $paginationPage]); ?>"><?php echo $paginationPage; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php $nextQuery = http_build_query(['search' => $search, 'page' => $page + 1]); ?>
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="companies.php?<?php echo $nextQuery; ?>" aria-label="Next"><i class="bi bi-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Add Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo companies_escape($csrfToken); ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="return_search" value="<?php echo companies_escape($search); ?>">
                    <input type="hidden" name="return_page" value="<?php echo $page; ?>">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="companyName" name="company_name" maxlength="150" required>
                    </div>
                    <div class="mb-3">
                        <label for="industry" class="form-label">Industry</label>
                        <input type="text" class="form-control" id="industry" name="industry" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" class="form-control" id="website" name="website" maxlength="255" placeholder="https://example.com">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contactPerson" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="contactPerson" name="contact_person" maxlength="120">
                        </div>
                        <div class="col-md-6">
                            <label for="contactEmail" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contactEmail" name="contact_email" maxlength="150">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>