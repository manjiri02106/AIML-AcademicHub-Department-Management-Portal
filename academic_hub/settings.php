<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Settings - Academic ERP</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	<link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include __DIR__ . '/includes/navbar.php'; ?>
<div class="container-fluid">
	<div class="row gx-4">
		<div class="col-12 col-lg-3"><?php include __DIR__ . '/includes/sidebar.php'; ?></div>
		<div class="col-12 col-lg-9">
			<main class="page-shell py-4">
				<header class="mb-4"><p class="section-kicker mb-2">Workspace preferences</p><h1 class="page-title mb-2">Settings</h1><p class="page-subtitle mb-0">Keep your placement workspace aligned with your department’s workflow.</p></header>
				<div class="row g-3">
					<div class="col-12 col-xl-8">
						<section class="card border-0 mb-3"><div class="card-header"><h2 class="h6 mb-1">Department profile</h2><p class="text-muted small mb-0">Information shown to students and recruiting partners.</p></div><div class="card-body pt-3"><div class="row g-3"><div class="col-12 col-md-6"><label class="form-label fw-semibold" for="departmentName">Department name</label><input class="form-control" id="departmentName" value="Artificial Intelligence & Machine Learning"></div><div class="col-12 col-md-6"><label class="form-label fw-semibold" for="academicYear">Academic year</label><select class="form-select" id="academicYear"><option>2026 - 2027</option><option>2025 - 2026</option></select></div><div class="col-12"><label class="form-label fw-semibold" for="contactEmail">Placement office email</label><input type="email" class="form-control" id="contactEmail" value="placements@academichub.edu"></div></div></div></section>
						<section class="card border-0"><div class="card-header"><h2 class="h6 mb-1">Notifications</h2><p class="text-muted small mb-0">Choose the updates your team receives.</p></div><div class="card-body pt-3"><div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="driveAlerts" checked><label class="form-check-label" for="driveAlerts">Drive registration alerts</label></div><div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="weeklyDigest" checked><label class="form-check-label" for="weeklyDigest">Weekly placement digest</label></div><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="partnerUpdates"><label class="form-check-label" for="partnerUpdates">New partner updates</label></div></div></section>
					</div>
					<div class="col-12 col-xl-4"><section class="card border-0 h-100"><div class="card-header"><h2 class="h6 mb-1">Account</h2><p class="text-muted small mb-0">Signed-in workspace access.</p></div><div class="card-body pt-3"><div class="d-flex align-items-center gap-3 mb-4"><div class="avatar"><i class="bi bi-person-fill"></i></div><div><strong>John Doe</strong><small class="d-block text-muted">Placement administrator</small></div></div><div class="border-top pt-3 mb-3"><small class="text-muted d-block mb-1">Access level</small><strong>Administrator</strong></div><div class="border-top pt-3"><small class="text-muted d-block mb-1">Last sign-in</small><strong>Today, 09:42 AM</strong></div></div><div class="card-footer bg-transparent border-0 p-4 pt-0"><button class="btn btn-primary w-100"><i class="bi bi-check2 me-2"></i>Save changes</button></div></section></div>
				</div>
			</main>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
