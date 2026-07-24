<?php
// frontend/pages/faculty/dashboard.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard | AIML AcademicHub</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php include '../../components/sidebar.php'; ?>
    
    <main class="main-content">
        <?php include '../../components/header.php'; ?>
        
        <div class="page-container">
            <h1 style="margin-bottom: 24px; font-weight: 600;">Welcome back, Dr. Alan</h1>
            
            <div class="stats-grid" id="dashboardStats">
                <div class="glass-card">
                    <div class="card-icon">+</div>
                    <div class="stat-value" id="courseCount">-</div>
                    <div class="stat-label">Allocated Courses</div>
                </div>
                <div class="glass-card">
                    <div class="card-icon">+</div>
                    <div class="stat-value" id="menteeCount">-</div>
                    <div class="stat-label">Assigned Mentees</div>
                </div>
                <div class="glass-card card-accent">
                    <div class="card-icon">+</div>
                    <div class="stat-value">2</div>
                    <div class="stat-label">Pending Approvals</div>
                </div>
                <div class="glass-card card-dark">
                    <div class="card-icon">+</div>
                    <div class="stat-value">Upcoming</div>
                    <div class="stat-label">Schedule</div>
                </div>
            </div>

            <div class="glass-card" style="margin-top: 32px;">
                <h3 style="margin-bottom: 16px;">Upcoming Schedule</h3>
                <p style="color: var(--text-secondary);">No immediate classes scheduled for today.</p>
            </div>
        </div>
    </main>

    <script src="../../js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const stats = await apiCall('get_dashboard_stats');
            if (stats.status === 'success') {
                document.getElementById('courseCount').textContent = stats.data.courses;
                document.getElementById('menteeCount').textContent = stats.data.mentees;
            }
        });
    </script>
</body>
</html>
