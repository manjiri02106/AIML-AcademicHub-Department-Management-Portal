<?php
/**
 * reports.php – Reports Generation Page
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once 'config/database.php';

$page_title   = 'Reports';
$page_subtitle = 'Generate and export department reports';
$current_page  = 'reports';

// ── Filter Parameters ────────────────────────────────────────
$report_type  = $_GET['report_type'] ?? 'publications';
$year_filter  = (int)($_GET['year'] ?? 0);
$faculty_f    = trim($_GET['faculty'] ?? '');
$pub_type_f   = trim($_GET['pub_type'] ?? '');
$status_f     = trim($_GET['status'] ?? '');
$event_year_f = (int)($_GET['event_year'] ?? 0);

// ── Available Years ──────────────────────────────────────────
$pub_years = [];
$res = $conn->query("SELECT DISTINCT year FROM publications ORDER BY year DESC");
while ($r = $res->fetch_assoc()) $pub_years[] = $r['year'];

$res_years = [];
$res2 = $conn->query("SELECT DISTINCT YEAR(start_date) y FROM research WHERE start_date IS NOT NULL ORDER BY y DESC");
while ($r = $res2->fetch_assoc()) $res_years[] = $r['y'];

// ── Faculties ────────────────────────────────────────────────
$faculties = [];
$res3 = $conn->query("SELECT DISTINCT faculty_name FROM research ORDER BY faculty_name");
while ($r = $res3->fetch_assoc()) $faculties[] = $r['faculty_name'];

// ── Summary Counts ───────────────────────────────────────────
$summary = [
    'total_research'     => $conn->query("SELECT COUNT(*) c FROM research")->fetch_assoc()['c'],
    'total_publications' => $conn->query("SELECT COUNT(*) c FROM publications")->fetch_assoc()['c'],
    'total_patents'      => $conn->query("SELECT COUNT(*) c FROM patents")->fetch_assoc()['c'],
    'total_events'       => $conn->query("SELECT COUNT(*) c FROM events")->fetch_assoc()['c'],
    'total_notices'      => $conn->query("SELECT COUNT(*) c FROM notices")->fetch_assoc()['c'],
];

// ── Report Data ──────────────────────────────────────────────
$report_data   = [];
$report_title  = '';
$report_cols   = [];

if ($report_type === 'publications') {
    $report_title = 'Publications Report';
    $where = "WHERE 1=1";
    $params = []; $types = '';
    if ($year_filter)   { $where .= " AND year=?";              $types .= 'i'; $params[] = $year_filter; }
    if ($pub_type_f)    { $where .= " AND publication_type=?";  $types .= 's'; $params[] = $pub_type_f; }
    if ($faculty_f)     { $where .= " AND author LIKE ?";       $types .= 's'; $params[] = "%$faculty_f%"; }
    $stmt = $conn->prepare("SELECT publication_id,title,author,department,publication_type,journal_conference,publisher,year,doi FROM publications $where ORDER BY year DESC, created_at DESC");
    if ($params) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    $report_cols = ['Pub. ID','Title','Author(s)','Type','Journal/Conference','Publisher','Year','DOI'];

} elseif ($report_type === 'research') {
    $report_title = 'Research Projects Report';
    $where = "WHERE 1=1";
    $params = []; $types = '';
    if ($status_f)  { $where .= " AND status=?";        $types .= 's'; $params[] = $status_f; }
    if ($faculty_f) { $where .= " AND faculty_name=?";  $types .= 's'; $params[] = $faculty_f; }
    if ($year_filter) { $where .= " AND YEAR(start_date)=?"; $types .= 'i'; $params[] = $year_filter; }
    $stmt = $conn->prepare("SELECT research_id,title,faculty_name,research_domain,category,funding_agency,budget,status,start_date,end_date FROM research $where ORDER BY created_at DESC");
    if ($params) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    $report_cols = ['Research ID','Title','Faculty','Domain','Category','Funding Agency','Budget','Status','Start Date','End Date'];

} elseif ($report_type === 'patents') {
    $report_title = 'Patents Report';
    $where = "WHERE 1=1";
    $params = []; $types = '';
    if ($status_f) { $where .= " AND status=?"; $types .= 's'; $params[] = $status_f; }
    $stmt = $conn->prepare("SELECT patent_id,title,inventors,application_number,filing_date,grant_date,status,country,patent_office FROM patents $where ORDER BY filing_date DESC");
    if ($params) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    $report_cols = ['Patent ID','Title','Inventors','App. No.','Filed','Granted','Status','Country','Office'];

} elseif ($report_type === 'events') {
    $report_title = 'Events Report';
    $where = "WHERE 1=1";
    $params = []; $types = '';
    if ($event_year_f) { $where .= " AND YEAR(event_date)=?"; $types .= 'i'; $params[] = $event_year_f; }
    $stmt = $conn->prepare("SELECT event_id,event_name,event_date,venue,organizer FROM events $where ORDER BY event_date DESC");
    if ($params) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    $report_cols = ['Event ID','Event Name','Date','Venue','Organizer'];

} elseif ($report_type === 'summary') {
    $report_title = 'Department Summary Report';
    // Publications by type
    $pub_by_type = [];
    $r = $conn->query("SELECT publication_type, COUNT(*) c FROM publications GROUP BY publication_type ORDER BY c DESC");
    while ($row = $r->fetch_assoc()) $pub_by_type[] = $row;
    // Research by status
    $res_by_status = [];
    $r2 = $conn->query("SELECT status, COUNT(*) c FROM research GROUP BY status ORDER BY c DESC");
    while ($row = $r2->fetch_assoc()) $res_by_status[] = $row;
    // Top faculty by publications
    $top_faculty = [];
    $r3 = $conn->query("SELECT faculty_name, COUNT(*) c FROM research GROUP BY faculty_name ORDER BY c DESC LIMIT 10");
    while ($row = $r3->fetch_assoc()) $top_faculty[] = $row;
}

$conn->close();

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div id="main-content">
<?php include 'includes/navbar.php'; ?>
<main class="page-content">

<!-- Filter Card -->
<div class="report-filter-card no-print">
    <div class="card-title"><i class="fa-solid fa-chart-bar me-2"></i>Generate Report</div>
    <div class="card-subtitle">Select report type and filters below, then click Generate.</div>
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3 col-sm-6">
            <label class="form-label">Report Type <span class="req" style="color:#ff8a80;">*</span></label>
            <select name="report_type" class="form-select" id="reportTypeSelect" onchange="updateFilters(this.value)">
                <option value="publications" <?= $report_type==='publications'?'selected':'' ?>>Publications</option>
                <option value="research"     <?= $report_type==='research'    ?'selected':'' ?>>Research Projects</option>
                <option value="patents"      <?= $report_type==='patents'     ?'selected':'' ?>>Patents</option>
                <option value="events"       <?= $report_type==='events'      ?'selected':'' ?>>Events</option>
                <option value="summary"      <?= $report_type==='summary'     ?'selected':'' ?>>Summary Overview</option>
            </select>
        </div>

        <!-- Publication Filters -->
        <div class="col-md-2 col-sm-6 filter-pub filter-research <?= !in_array($report_type,['publications','research'])?'d-none':'' ?>">
            <label class="form-label">Year</label>
            <select name="year" class="form-select">
                <option value="">All Years</option>
                <?php foreach (array_unique(array_merge($pub_years,$res_years)) as $y): ?>
                <option value="<?= $y ?>" <?= $year_filter==$y?'selected':'' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3 col-sm-6 filter-pub filter-research <?= !in_array($report_type,['publications','research'])?'d-none':'' ?>">
            <label class="form-label">Faculty</label>
            <select name="faculty" class="form-select">
                <option value="">All Faculty</option>
                <?php foreach ($faculties as $f): ?>
                <option value="<?= e($f) ?>" <?= $faculty_f===$f?'selected':'' ?>><?= e($f) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 col-sm-6 filter-pub <?= $report_type!=='publications'?'d-none':'' ?>">
            <label class="form-label">Pub. Type</label>
            <select name="pub_type" class="form-select">
                <option value="">All Types</option>
                <option value="journal"     <?= $pub_type_f==='journal'    ?'selected':'' ?>>Journal</option>
                <option value="conference"  <?= $pub_type_f==='conference' ?'selected':'' ?>>Conference</option>
                <option value="book"        <?= $pub_type_f==='book'       ?'selected':'' ?>>Book</option>
                <option value="book_chapter"<?= $pub_type_f==='book_chapter'?'selected':'' ?>>Book Chapter</option>
                <option value="other"       <?= $pub_type_f==='other'      ?'selected':'' ?>>Other</option>
            </select>
        </div>

        <div class="col-md-2 col-sm-6 filter-research filter-patents <?= !in_array($report_type,['research','patents'])?'d-none':'' ?>">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <?php if ($report_type === 'research'): ?>
                    <option value="ongoing"   <?= $status_f==='ongoing'  ?'selected':'' ?>>Ongoing</option>
                    <option value="completed" <?= $status_f==='completed'?'selected':'' ?>>Completed</option>
                    <option value="pending"   <?= $status_f==='pending'  ?'selected':'' ?>>Pending</option>
                    <option value="cancelled" <?= $status_f==='cancelled'?'selected':'' ?>>Cancelled</option>
                <?php else: ?>
                    <option value="filed"     <?= $status_f==='filed'    ?'selected':'' ?>>Filed</option>
                    <option value="published" <?= $status_f==='published'?'selected':'' ?>>Published</option>
                    <option value="granted"   <?= $status_f==='granted'  ?'selected':'' ?>>Granted</option>
                    <option value="rejected"  <?= $status_f==='rejected' ?'selected':'' ?>>Rejected</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-md-2 col-sm-6 filter-events <?= $report_type!=='events'?'d-none':'' ?>">
            <label class="form-label">Event Year</label>
            <select name="event_year" class="form-select">
                <option value="">All Years</option>
                <?php foreach (range(date('Y'), 2020) as $y): ?>
                <option value="<?= $y ?>" <?= $event_year_f==$y?'selected':'' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 col-sm-6">
            <button type="submit" class="btn btn-light w-100 fw-600">
                <i class="fa-solid fa-play"></i> Generate
            </button>
        </div>
    </form>
</div>

<!-- Summary Tiles -->
<div class="report-summary-strip no-print">
    <div class="summary-tile"><div class="tile-val"><?= $summary['total_research'] ?></div><div class="tile-lbl">Research</div></div>
    <div class="summary-tile"><div class="tile-val" style="color:var(--success);"><?= $summary['total_publications'] ?></div><div class="tile-lbl">Publications</div></div>
    <div class="summary-tile"><div class="tile-val" style="color:var(--warning);"><?= $summary['total_patents'] ?></div><div class="tile-lbl">Patents</div></div>
    <div class="summary-tile"><div class="tile-val" style="color:var(--purple);"><?= $summary['total_events'] ?></div><div class="tile-lbl">Events</div></div>
    <div class="summary-tile"><div class="tile-val" style="color:var(--danger);"><?= $summary['total_notices'] ?></div><div class="tile-lbl">Notices</div></div>
</div>

<!-- Report Output -->
<div class="card-panel">
    <div class="card-panel-header">
        <div class="card-panel-title">
            <div class="title-icon"><i class="fa-solid fa-file-lines"></i></div>
            <?= htmlspecialchars($report_title) ?>
            <?php if ($report_type !== 'summary'): ?>
            <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:11px;padding:3px 8px;border-radius:20px;"><?= count($report_data) ?> records</span>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2 no-print">
            <button onclick="printReport()" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-print"></i> Print / Export PDF
            </button>
        </div>
    </div>

    <!-- PRINT HEADER (visible only in print) -->
    <div style="display:none;" class="print-header">
        <div style="text-align:center;padding:16px 0;border-bottom:2px solid #1565c0;margin-bottom:16px;">
            <div style="font-size:20px;font-weight:800;color:#1565c0;">AIML Academic Hub – Department Management Portal</div>
            <div style="font-size:14px;color:#666;"><?= htmlspecialchars($report_title) ?> &nbsp;|&nbsp; Generated: <?= date('d M Y, h:i A') ?></div>
        </div>
    </div>

    <div class="card-panel-body" style="padding:0;">

    <?php if ($report_type === 'summary'): ?>
        <!-- SUMMARY REPORT -->
        <div style="padding:20px 24px;">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:12px;">Publications by Type</h6>
                    <table class="data-table">
                        <thead><tr><th>Type</th><th>Count</th></tr></thead>
                        <tbody>
                            <?php foreach ($pub_by_type as $r): ?>
                            <tr>
                                <td><span class="badge-status badge-<?= e($r['publication_type']) ?>"><?= ucwords(str_replace('_',' ',e($r['publication_type']))) ?></span></td>
                                <td style="font-weight:700;color:var(--primary);"><?= $r['c'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:12px;">Research by Status</h6>
                    <table class="data-table">
                        <thead><tr><th>Status</th><th>Count</th></tr></thead>
                        <tbody>
                            <?php foreach ($res_by_status as $r): ?>
                            <tr>
                                <td><span class="badge-status badge-<?= e($r['status']) ?>"><?= ucfirst(e($r['status'])) ?></span></td>
                                <td style="font-weight:700;color:var(--primary);"><?= $r['c'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <h6 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:12px;">Research by Faculty</h6>
                    <table class="data-table">
                        <thead><tr><th>Faculty Name</th><th>No. of Research Projects</th></tr></thead>
                        <tbody>
                            <?php foreach ($top_faculty as $r): ?>
                            <tr>
                                <td class="fw-600"><?= e($r['faculty_name']) ?></td>
                                <td style="font-weight:700;color:var(--primary);"><?= $r['c'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($report_type === 'publications'): ?>
        <div class="table-wrapper">
            <?php if (empty($report_data)): ?>
            <div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-book-open"></i></div><h6>No data found for selected filters.</h6></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><?php foreach ($report_cols as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($report_data as $r): ?>
                    <tr>
                        <td class="td-id"><?= e($r['publication_id']) ?></td>
                        <td style="max-width:200px;"><div class="fw-600 text-truncate-2" style="font-size:13px;"><?= e($r['title']) ?></div></td>
                        <td style="font-size:12.5px;"><?= e($r['author']) ?></td>
                        <td><span class="badge-status badge-<?= e($r['publication_type']) ?>"><?= ucwords(str_replace('_',' ',e($r['publication_type']))) ?></span></td>
                        <td style="font-size:12.5px;"><?= e($r['journal_conference'] ?: '—') ?></td>
                        <td style="font-size:12.5px;"><?= e($r['publisher'] ?: '—') ?></td>
                        <td style="font-weight:700;color:var(--primary);"><?= e($r['year']) ?></td>
                        <td style="font-size:12px;" class="mono"><?= $r['doi'] ? '<a href="https://doi.org/'.e($r['doi']).'" target="_blank">'.e($r['doi']).'</a>' : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    <?php elseif ($report_type === 'research'): ?>
        <div class="table-wrapper">
            <?php if (empty($report_data)): ?>
            <div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-flask"></i></div><h6>No data found for selected filters.</h6></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><?php foreach ($report_cols as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($report_data as $r): ?>
                    <tr>
                        <td class="td-id"><?= e($r['research_id']) ?></td>
                        <td style="max-width:180px;"><div class="fw-600 text-truncate-2" style="font-size:13px;"><?= e($r['title']) ?></div></td>
                        <td style="font-size:12.5px;"><?= e($r['faculty_name']) ?></td>
                        <td style="font-size:12.5px;"><?= e($r['research_domain']) ?></td>
                        <td style="font-size:12.5px;"><?= e($r['category']) ?></td>
                        <td style="font-size:12.5px;"><?= e($r['funding_agency'] ?: '—') ?></td>
                        <td style="font-weight:700;color:var(--primary);"><?= $r['budget'] ? '₹'.number_format($r['budget'],0) : '—' ?></td>
                        <td><span class="badge-status badge-<?= e($r['status']) ?>"><?= ucfirst(e($r['status'])) ?></span></td>
                        <td style="font-size:12px;"><?= formatDate($r['start_date']) ?></td>
                        <td style="font-size:12px;"><?= formatDate($r['end_date']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    <?php elseif ($report_type === 'patents'): ?>
        <div class="table-wrapper">
            <?php if (empty($report_data)): ?>
            <div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-certificate"></i></div><h6>No data found for selected filters.</h6></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><?php foreach ($report_cols as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($report_data as $r): ?>
                    <tr>
                        <td class="td-id"><?= e($r['patent_id']) ?></td>
                        <td style="max-width:180px;"><div class="fw-600 text-truncate-2" style="font-size:13px;"><?= e($r['title']) ?></div></td>
                        <td style="font-size:12.5px;"><?= e($r['inventors']) ?></td>
                        <td class="mono" style="font-size:12px;"><?= e($r['application_number'] ?: '—') ?></td>
                        <td style="font-size:12px;"><?= formatDate($r['filing_date']) ?></td>
                        <td style="font-size:12px;"><?= formatDate($r['grant_date']) ?></td>
                        <td><span class="badge-status badge-<?= e($r['status']) ?>"><?= ucfirst(e($r['status'])) ?></span></td>
                        <td style="font-size:12.5px;"><?= e($r['country'] ?: '—') ?></td>
                        <td style="font-size:12px;"><?= e($r['patent_office'] ?: '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

    <?php elseif ($report_type === 'events'): ?>
        <div class="table-wrapper">
            <?php if (empty($report_data)): ?>
            <div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-calendar-xmark"></i></div><h6>No data found for selected filters.</h6></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><?php foreach ($report_cols as $c): ?><th><?= $c ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($report_data as $r): ?>
                    <tr>
                        <td class="td-id"><?= e($r['event_id']) ?></td>
                        <td class="fw-600" style="font-size:13px;"><?= e($r['event_name']) ?></td>
                        <td style="font-weight:700;color:var(--purple);"><?= formatDate($r['event_date']) ?></td>
                        <td style="font-size:12.5px;"><?= e($r['venue'] ?: '—') ?></td>
                        <td style="font-size:12.5px;"><?= e($r['organizer'] ?: '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    </div><!-- /.card-panel-body -->
</div><!-- /.card-panel -->

</main>
<?php include 'includes/footer.php'; ?>
</div>

<script>
// Print styles fix
document.querySelectorAll('.print-header').forEach(el => el.style.display = 'none');
window.addEventListener('beforeprint', () => {
    document.querySelectorAll('.print-header').forEach(el => el.style.display = 'block');
});
window.addEventListener('afterprint', () => {
    document.querySelectorAll('.print-header').forEach(el => el.style.display = 'none');
});

function updateFilters(type) {
    // Show/hide filter groups based on report type
    document.querySelectorAll('[class*="filter-"]').forEach(el => {
        const classes = Array.from(el.classList);
        const matches = classes.some(c => c === 'filter-' + type);
        el.classList.toggle('d-none', !matches);
    });
}
// On load, trigger once
updateFilters('<?= e($report_type) ?>');
</script>
