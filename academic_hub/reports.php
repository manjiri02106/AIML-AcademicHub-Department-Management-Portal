<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Analytics & Reports - Academic ERP Placement</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	<link href="assets/css/style.css" rel="stylesheet">
	<style>
		.report-page {
			padding-bottom: 2rem;
		}
		.report-header {
			margin-bottom: 1.75rem;
		}
		.report-eyebrow {
			color: #1976d2;
			font-size: 0.75rem;
			font-weight: 700;
			letter-spacing: 0.08em;
			text-transform: uppercase;
		}
		.report-title {
			color: #112a46;
			font-size: clamp(1.6rem, 2.6vw, 2.35rem);
			font-weight: 700;
		}
		.report-filter {
			min-width: 150px;
			border-radius: 13px;
			border-color: rgba(17, 42, 77, 0.12);
			color: #112a46;
			font-weight: 600;
		}
		.metric-card {
			min-height: 172px;
			overflow: hidden;
			position: relative;
			transition: transform 0.25s ease, box-shadow 0.25s ease;
		}
		.metric-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 28px 60px rgba(17, 42, 77, 0.15);
		}
		.metric-card::after {
			background: rgba(255, 255, 255, 0.12);
			border-radius: 50%;
			content: '';
			height: 130px;
			position: absolute;
			right: -42px;
			top: -44px;
			width: 130px;
		}
		.metric-card .card-body {
			position: relative;
			z-index: 1;
		}
		.metric-icon {
			align-items: center;
			background: rgba(255, 255, 255, 0.18);
			border-radius: 14px;
			display: inline-flex;
			height: 44px;
			justify-content: center;
			width: 44px;
		}
		.metric-label {
			font-size: 0.75rem;
			letter-spacing: 0.05em;
			opacity: 0.78;
			text-transform: uppercase;
		}
		.metric-value {
			font-size: clamp(1.7rem, 3vw, 2.35rem);
			font-weight: 700;
			letter-spacing: 0;
		}
		.metric-note {
			font-size: 0.82rem;
			margin: 0;
			opacity: 0.78;
		}
		.chart-card {
			height: 100%;
		}
		.chart-card .card-header {
			padding-bottom: 0.75rem;
		}
		.chart-card .card-body {
			min-height: 290px;
			position: relative;
		}
		.chart-card canvas {
			max-height: 280px;
		}
		.insight-row {
			align-items: center;
			border-bottom: 1px solid rgba(17, 42, 77, 0.08);
			display: flex;
			gap: 0.9rem;
			justify-content: space-between;
			padding: 0.9rem 0;
		}
		.insight-row:last-child {
			border-bottom: 0;
			padding-bottom: 0;
		}
		.insight-row:first-child {
			padding-top: 0;
		}
		.insight-dot {
			border-radius: 50%;
			flex: 0 0 10px;
			height: 10px;
			width: 10px;
		}
		.insight-copy {
			color: #6b7b95;
			font-size: 0.88rem;
			line-height: 1.45;
		}
		.insight-copy strong {
			color: #112a46;
			display: block;
			font-size: 0.92rem;
		}
		@media (max-width: 575px) {
			.report-page {
				padding-top: 0.25rem;
			}
			.report-header .btn,
			.report-filter {
				width: 100%;
			}
			.chart-card .card-body {
				min-height: 250px;
				padding: 1rem;
			}
		}
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
			<main class="report-page py-4">
				<div class="report-header d-flex flex-column flex-md-row align-items-start align-items-md-end justify-content-between gap-3">
					<div>
						<p class="report-eyebrow mb-2">Performance intelligence</p>
						<h1 class="report-title mb-2">Analytics Dashboard</h1>
						<p class="text-muted mb-0">See placement outcomes, package trends and recruiter reach at a glance.</p>
					</div>
					<div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
						<select class="form-select report-filter" id="reportPeriod" aria-label="Report period">
							<option value="2026">Academic Year 2026</option>
							<option value="2025">Academic Year 2025</option>
							<option value="2024">Academic Year 2024</option>
						</select>
						<button type="button" class="btn btn-primary text-nowrap" id="printReport">
							<i class="bi bi-download me-2"></i>Export report
						</button>
					</div>
				</div>

				<div class="row g-3 mb-4">
					<div class="col-12 col-sm-6 col-xl-3">
						<article class="card metric-card border-0 text-white h-100" style="background: linear-gradient(135deg, #124e78, #1e88a8);">
							<div class="card-body p-4 d-flex flex-column justify-content-between">
								<div class="d-flex justify-content-between align-items-start mb-4">
									<span class="metric-label">Highest Package</span>
									<span class="metric-icon"><i class="bi bi-trophy fs-5"></i></span>
								</div>
								<div>
									<div class="metric-value">24.5 LPA</div>
									<p class="metric-note mt-1"><i class="bi bi-arrow-up-right me-1"></i>18% above last year</p>
								</div>
							</div>
						</article>
					</div>
					<div class="col-12 col-sm-6 col-xl-3">
						<article class="card metric-card border-0 text-white h-100" style="background: linear-gradient(135deg, #146c5b, #24a17d);">
							<div class="card-body p-4 d-flex flex-column justify-content-between">
								<div class="d-flex justify-content-between align-items-start mb-4">
									<span class="metric-label">Average Package</span>
									<span class="metric-icon"><i class="bi bi-graph-up-arrow fs-5"></i></span>
								</div>
								<div>
									<div class="metric-value">7.8 LPA</div>
									<p class="metric-note mt-1"><i class="bi bi-arrow-up-right me-1"></i>9.6% year over year</p>
								</div>
							</div>
						</article>
					</div>
					<div class="col-12 col-sm-6 col-xl-3">
						<article class="card metric-card border-0 text-white h-100" style="background: linear-gradient(135deg, #8a4b23, #d17a34);">
							<div class="card-body p-4 d-flex flex-column justify-content-between">
								<div class="d-flex justify-content-between align-items-start mb-4">
									<span class="metric-label">Placement %</span>
									<span class="metric-icon"><i class="bi bi-bullseye fs-5"></i></span>
								</div>
								<div>
									<div class="metric-value">82.4%</div>
									<p class="metric-note mt-1"><i class="bi bi-arrow-up-right me-1"></i>6.2 points improved</p>
								</div>
							</div>
						</article>
					</div>
					<div class="col-12 col-sm-6 col-xl-3">
						<article class="card metric-card border-0 text-white h-100" style="background: linear-gradient(135deg, #4b397c, #7653a8);">
							<div class="card-body p-4 d-flex flex-column justify-content-between">
								<div class="d-flex justify-content-between align-items-start mb-4">
									<span class="metric-label">Companies Visited</span>
									<span class="metric-icon"><i class="bi bi-buildings fs-5"></i></span>
								</div>
								<div>
									<div class="metric-value">68</div>
									<p class="metric-note mt-1"><i class="bi bi-plus-lg me-1"></i>12 new partners this year</p>
								</div>
							</div>
						</article>
					</div>
				</div>

				<div class="row g-3 mb-4">
					<div class="col-12 col-xl-8">
						<section class="card chart-card border-0">
							<div class="card-header d-flex align-items-start justify-content-between gap-3">
								<div>
									<h2 class="h6 mb-1">Placement trend</h2>
									<p class="text-muted small mb-0">Offers secured across the academic year</p>
								</div>
								<span class="badge rounded-pill text-bg-light">2026</span>
							</div>
							<div class="card-body">
								<canvas id="placementTrend" aria-label="Monthly placement trend chart"></canvas>
							</div>
						</section>
					</div>
					<div class="col-12 col-xl-4">
						<section class="card chart-card border-0">
							<div class="card-header">
								<h2 class="h6 mb-1">Placement status</h2>
								<p class="text-muted small mb-0">Student outcome distribution</p>
							</div>
							<div class="card-body">
								<canvas id="placementStatus" aria-label="Placement status doughnut chart"></canvas>
							</div>
						</section>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-12 col-lg-7">
						<section class="card chart-card border-0">
							<div class="card-header d-flex align-items-start justify-content-between gap-3">
								<div>
									<h2 class="h6 mb-1">Department performance</h2>
									<p class="text-muted small mb-0">Placement rate by department</p>
								</div>
								<i class="bi bi-bar-chart-line text-primary fs-5"></i>
							</div>
							<div class="card-body">
								<canvas id="departmentPerformance" aria-label="Department placement performance chart"></canvas>
							</div>
						</section>
					</div>
					<div class="col-12 col-lg-5">
						<section class="card border-0 h-100">
							<div class="card-header">
								<h2 class="h6 mb-1">Key insights</h2>
								<p class="text-muted small mb-0">Signals worth sharing with department heads</p>
							</div>
							<div class="card-body pt-2">
								<div class="insight-row">
									<span class="insight-dot bg-success"></span>
									<div class="insight-copy flex-grow-1"><strong>CSE leads placement outcomes</strong>92% placement rate, 10 points above the average.</div>
								</div>
								<div class="insight-row">
									<span class="insight-dot bg-warning"></span>
									<div class="insight-copy flex-grow-1"><strong>Hiring activity peaks in May</strong>32% of recorded offers arrived in the final drive cycle.</div>
								</div>
								<div class="insight-row">
									<span class="insight-dot bg-primary"></span>
									<div class="insight-copy flex-grow-1"><strong>Partner network is expanding</strong>12 new companies visited campus this academic year.</div>
								</div>
							</div>
						</section>
					</div>
				</div>
			</main>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
	const chartFont = {
		family: 'Inter, system-ui, sans-serif'
	};
	const chartGrid = 'rgba(17, 42, 77, 0.08)';

	new Chart(document.getElementById('placementTrend'), {
		type: 'line',
		data: {
			labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
			datasets: [{
				label: 'Offers',
				data: [18, 27, 33, 41, 46, 58, 63, 71, 84, 108, 96],
				borderColor: '#1976d2',
				backgroundColor: 'rgba(25, 118, 210, 0.12)',
				fill: true,
				tension: 0.38,
				pointRadius: 4,
				pointHoverRadius: 6,
				pointBackgroundColor: '#ffffff',
				pointBorderColor: '#1976d2',
				pointBorderWidth: 2
			}]
		},
		options: {
			maintainAspectRatio: false,
			interaction: { intersect: false, mode: 'index' },
			plugins: {
				legend: { display: false },
				tooltip: { padding: 12, displayColors: false }
			},
			scales: {
				y: { beginAtZero: true, grid: { color: chartGrid }, ticks: { font: chartFont, stepSize: 25 } },
				x: { grid: { display: false }, ticks: { font: chartFont } }
			}
		}
	});

	new Chart(document.getElementById('placementStatus'), {
		type: 'doughnut',
		data: {
			labels: ['Placed', 'In process', 'Seeking opportunity'],
			datasets: [{
				data: [824, 116, 60],
				backgroundColor: ['#198754', '#f9a825', '#dce7f3'],
				borderColor: '#ffffff',
				borderWidth: 4,
				hoverOffset: 5
			}]
		},
		options: {
			maintainAspectRatio: false,
			cutout: '70%',
			plugins: {
				legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14, font: chartFont } },
				tooltip: { padding: 12 }
			}
		}
	});

	new Chart(document.getElementById('departmentPerformance'), {
		type: 'bar',
		data: {
			labels: ['CSE', 'IT', 'ECE', 'ME', 'CE'],
			datasets: [{
				label: 'Placement rate',
				data: [92, 86, 79, 72, 68],
				backgroundColor: ['#1976d2', '#36a2eb', '#20a486', '#f9a825', '#d17a34'],
				borderRadius: 8,
				borderSkipped: false,
				barThickness: 28
			}]
		},
		options: {
			maintainAspectRatio: false,
			plugins: {
				legend: { display: false },
				tooltip: { padding: 12, callbacks: { label: context => `${context.raw}% placed` } }
			},
			scales: {
				y: { beginAtZero: true, max: 100, grid: { color: chartGrid }, ticks: { font: chartFont, callback: value => `${value}%` } },
				x: { grid: { display: false }, ticks: { font: chartFont } }
			}
		}
	});

	document.getElementById('printReport').addEventListener('click', () => window.print());
</script>
</body>
</html>
