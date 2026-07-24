<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireRole(['Administrator']);

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> | AIML AcademicHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
</head>
<body>
    <div class="container-fluid">
        <div class="row g-0">
            <aside class="col-lg-2 sidebar p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="brand-logo-sidebar">
                        <img src="<?= url('/assets/images/image.png') ?>" alt="AIML AcademicHub Logo" class="img-fluid">
                    </div>
                    <div>
                        <div class="fw-bold">AIML Hub</div>
                        <div class="small text-white-50">Academic Portal</div>
                    </div>
                </div>
                <nav class="nav flex-column gap-2">
                    <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                    <a class="nav-link" href="#"><i class="bi bi-people me-2"></i>Students</a>
                    <a class="nav-link" href="#"><i class="bi bi-person-badge me-2"></i>Faculty</a>
                    <a class="nav-link" href="#"><i class="bi bi-calendar-check me-2"></i>Attendance</a>
                    <a class="nav-link" href="#"><i class="bi bi-journal-richtext me-2"></i>Projects</a>
                    <a class="nav-link" href="#"><i class="bi bi-briefcase me-2"></i>Placements</a>
                    <a class="nav-link" href="#"><i class="bi bi-bar-chart me-2"></i>Reports</a>
                    <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                    <a class="nav-link" href="<?= url('/auth/logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </nav>
            </aside>
            <main class="col-lg-10 p-4 p-lg-5 bg-light">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                    <div>
                        <p class="text-muted mb-1">Welcome back</p>
                        <h2 class="page-title mb-0">Hello, <?= escape($_SESSION['user']['name']) ?></h2>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="input-group shadow-sm" style="max-width: 280px;">
                            <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0" placeholder="Search modules">
                        </div>
                        <div class="stat-icon"><i class="bi bi-bell"></i></div>
                        <div class="d-flex align-items-center gap-2 p-2 rounded-4 bg-white border">
                            <div class="brand-badge" style="width: 40px; height: 40px;">A</div>
                            <div>
                                <div class="fw-semibold">Administrator</div>
                                <small class="text-muted">Department Control</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-hero dashboard-card p-4 p-lg-5 mb-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <p class="text-muted mb-2">Operations Overview</p>
                            <h3 class="fw-bold mb-3">AIML AcademicHub at a glance</h3>
                            <p class="text-muted mb-0">Monitor academic performance, attendance, placements, and institutional reports from one polished workspace.</p>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <a href="#" class="btn btn-primary">View Reports</a>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Students</p>
                                    <h3 class="fw-bold mb-0">1,240</h3>
                                </div>
                                <div class="stat-icon"><i class="bi bi-people"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Faculty</p>
                                    <h3 class="fw-bold mb-0">86</h3>
                                </div>
                                <div class="stat-icon"><i class="bi bi-person-workspace"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Attendance</p>
                                    <h3 class="fw-bold mb-0">92%</h3>
                                </div>
                                <div class="stat-icon"><i class="bi bi-calendar2-check"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">Projects</p>
                                    <h3 class="fw-bold mb-0">34</h3>
                                </div>
                                <div class="stat-icon"><i class="bi bi-journal-richtext"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-lg-8">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Latest Activity</h5>
                                <span class="badge bg-primary-subtle text-primary">Live</span>
                            </div>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="fw-semibold">New student registrations</div>
                                            <div class="small text-muted">24 new students enrolled this week</div>
                                        </div>
                                        <span class="small text-muted">2h ago</span>
                                    </div>
                                </div>
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="fw-semibold">Placement review updated</div>
                                            <div class="small text-muted">Company onboarding progress synced</div>
                                        </div>
                                        <span class="small text-muted">5h ago</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="dashboard-card p-4">
                            <h5 class="fw-bold mb-3">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-outline-primary">Add Student</a>
                                <a href="#" class="btn btn-outline-primary">Create Report</a>
                                <a href="#" class="btn btn-outline-primary">Manage Faculty</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-footer mt-5 pt-4 border-top">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                        <div class="text-muted small">© 2026 Zeal College of Engineering. All rights reserved.</div>
                        <div class="small text-muted">Narhe, Pune | zcoer@zealeducation.com</div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</body>
</html>
