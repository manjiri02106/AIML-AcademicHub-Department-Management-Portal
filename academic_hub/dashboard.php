<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic ERP Placement Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
require_once __DIR__ . '/includes/db.php';

$statsResult = $conn->query(
        "SELECT
                (SELECT COUNT(*) FROM companies) AS total_companies,
                (SELECT COUNT(*) FROM placement_drives
                 WHERE drive_date >= CURDATE()
                     AND status IN ('Scheduled', 'Open')) AS upcoming_drives,
                (SELECT COUNT(*) FROM students) AS registered_students,
                (SELECT COUNT(*) FROM students WHERE cgpa >= 7.00) AS eligible_students,
                (SELECT COUNT(DISTINCT applications.student_id)
                 FROM applications
                 INNER JOIN offers ON offers.application_id = applications.application_id
                 WHERE offers.offer_status = 'Accepted') AS placed_students,
                (SELECT COUNT(*) FROM offers) AS total_offers"
)->fetch_assoc();

include __DIR__ . '/includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row gx-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>
        <div class="col-12 col-lg-9">
            <div class="py-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">
                    <div>
                        <p class="text-uppercase text-primary mb-2 small fw-semibold">TPO Dashboard</p>
                        <h1 class="h3 mb-0">Placement Overview</h1>
                        <p class="text-muted mb-0">Track drives, students, offers and placement activity in one view.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-share me-1"></i> Share</button>
                        <button class="btn btn-primary btn-sm"><i class="bi bi-download me-1"></i> Export</button>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 text-white" style="background: linear-gradient(135deg,#0d6efd,#4dabf7); box-shadow: 0 24px 40px rgba(13,110,253,0.18); transition: transform .25s ease, box-shadow .25s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <p class="text-uppercase small mb-2 opacity-75">Total Companies</p>
                                        <h3 class="mb-0"><?php echo number_format((int) $statsResult['total_companies']); ?></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-15 rounded-3 p-3">
                                        <i class="bi bi-building fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 opacity-75">Companies participating in campus placements.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 text-white" style="background: linear-gradient(135deg,#198754,#42ba75); box-shadow: 0 24px 40px rgba(25,135,84,0.18); transition: transform .25s ease, box-shadow .25s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <p class="text-uppercase small mb-2 opacity-75">Upcoming Drives</p>
                                        <h3 class="mb-0"><?php echo number_format((int) $statsResult['upcoming_drives']); ?></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-15 rounded-3 p-3">
                                        <i class="bi bi-calendar-event fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 opacity-75">Scheduled and open drives with upcoming dates.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 text-white" style="background: linear-gradient(135deg,#0dcaf0,#37b9e7); box-shadow: 0 24px 40px rgba(13,202,240,0.18); transition: transform .25s ease, box-shadow .25s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <p class="text-uppercase small mb-2 opacity-75">Registered Students</p>
                                        <h3 class="mb-0"><?php echo number_format((int) $statsResult['registered_students']); ?></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-15 rounded-3 p-3">
                                        <i class="bi bi-people-fill fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 opacity-75">Students registered in the academic portal.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 text-white" style="background: linear-gradient(135deg,#3b5bdb,#3f82fc); box-shadow: 0 24px 40px rgba(59,91,219,0.18); transition: transform .25s ease, box-shadow .25s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <p class="text-uppercase small mb-2 opacity-75">Eligible Students</p>
                                        <h3 class="mb-0"><?php echo number_format((int) $statsResult['eligible_students']); ?></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-15 rounded-3 p-3">
                                        <i class="bi bi-person-check-fill fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 opacity-75">Students meeting the minimum CGPA requirement.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 text-white" style="background: linear-gradient(135deg,#fd7e14,#ffad5c); box-shadow: 0 24px 40px rgba(253,126,20,0.18); transition: transform .25s ease, box-shadow .25s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <p class="text-uppercase small mb-2 opacity-75">Placed Students</p>
                                        <h3 class="mb-0"><?php echo number_format((int) $statsResult['placed_students']); ?></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-15 rounded-3 p-3">
                                        <i class="bi bi-award fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 opacity-75">Students with an accepted placement offer.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card h-100 border-0 text-white" style="background: linear-gradient(135deg,#6f42c1,#9b75e8); box-shadow: 0 24px 40px rgba(111,66,193,0.18); transition: transform .25s ease, box-shadow .25s ease;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <p class="text-uppercase small mb-2 opacity-75">Offers</p>
                                        <h3 class="mb-0"><?php echo number_format((int) $statsResult['total_offers']); ?></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-15 rounded-3 p-3">
                                        <i class="bi bi-briefcase-fill fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 opacity-75">Offers generated from campus drives.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.querySelectorAll('.card.h-100').forEach(card => {
                        card.addEventListener('mouseenter', () => {
                            card.style.transform = 'translateY(-6px)';
                            card.style.boxShadow = '0 28px 50px rgba(0,0,0,0.18)';
                        });
                        card.addEventListener('mouseleave', () => {
                            card.style.transform = 'translateY(0)';
                            card.style.boxShadow = card.style.boxShadow.replace('0 28px 50px rgba(0,0,0,0.18)', '0 24px 40px rgba(0,0,0,0.18)');
                        });
                    });
                </script>

                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0 d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                                <div>
                                    <h6 class="mb-1">Placement Drives</h6>
                                    <p class="text-muted mb-0">Latest drive schedule, eligibility and status.</p>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary">View all</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Company</th>
                                            <th>Role</th>
                                            <th>Package</th>
                                            <th>Date</th>
                                            <th>Eligible Branch</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Innotech</strong></td>
                                            <td>Software Engineer</td>
                                            <td>10 LPA</td>
                                            <td>Aug 1, 2026</td>
                                            <td>CSE, IT</td>
                                            <td><span class="badge bg-success">Open</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td><strong>NovaSys</strong></td>
                                            <td>Data Analyst</td>
                                            <td>8 LPA</td>
                                            <td>Jul 18, 2026</td>
                                            <td>CSE, ECE</td>
                                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Apex Solutions</strong></td>
                                            <td>System Engineer</td>
                                            <td>6.5 LPA</td>
                                            <td>Jun 30, 2026</td>
                                            <td>ME, CE</td>
                                            <td><span class="badge bg-info text-dark">Review</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                        </tr>
                                        <tr>
                                            <td><strong>ByteLabs</strong></td>
                                            <td>Intern</td>
                                            <td>2.5 LPA</td>
                                            <td>May 20, 2026</td>
                                            <td>CSE, ECE</td>
                                            <td><span class="badge bg-danger">Closed</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12 col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0">
                                <h6 class="mb-0">Placement Statistics</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="placementPie"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0">
                                <h6 class="mb-0">Monthly Placement Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyBar"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white border-0">
                                <h6 class="mb-0">Department Placement</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0">Recent Activity</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">24 new student registrations</div>
                                    <small class="text-muted">Recent signups for campus drive eligibility.</small>
                                </div>
                                <span class="badge bg-primary rounded-pill align-self-center">Now</span>
                            </div>
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">Innotech drive confirmed</div>
                                    <small class="text-muted">Official schedule published for August 1.</small>
                                </div>
                                <span class="badge bg-success rounded-pill align-self-center">Today</span>
                            </div>
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">Offer report generated</div>
                                    <small class="text-muted">Latest placement offer analytics are ready.</small>
                                </div>
                                <span class="badge bg-info rounded-pill align-self-center">Latest</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
