<?php
/**
 * Main Entry Point - Projects & Internships Module
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/functions.php';

if (file_exists('header.php')) { include('header.php'); } else if (file_exists('../header.php')) { include('../header.php'); }
if (file_exists('sidebar.php')) { include('sidebar.php'); } else if (file_exists('../sidebar.php')) { include('../sidebar.php'); }
?>

<link rel="stylesheet" href="assets/css/style.css">
<script src="assets/js/script.js" defer></script>

<div class="module-container">
    <div style="text-align: center; margin-bottom: 36px; padding-top: 20px;">
        <h1 style="font-size: 32px; font-weight: 700; color: var(--primary-navy); margin-bottom: 8px;">
            <i class="fas fa-graduation-cap" style="color: var(--secondary-teal);"></i> Projects & Internships Portal
        </h1>
        <p style="font-size: 16px; color: var(--text-muted); max-width: 600px; margin: 0 auto;">
            Academic Hub Department Management System - Select your workspace below to access features.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px; max-width: 900px; margin: 0 auto 40px;">
        <!-- Student Workspace -->
        <div class="glass-card" style="text-align: center; padding: 32px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--status-approved-bg); color: var(--status-approved-text); display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 18px;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h2 style="color: var(--primary-navy); font-size: 22px; margin-bottom: 10px;">Student Portal</h2>
                <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 24px; line-height: 1.6;">
                    Register projects, upload deliverables, log corporate internships, track weekly progress milestones, and view faculty feedback.
                </p>
            </div>
            <div>
                <a href="student/dashboard.php" class="btn btn-navy" style="width: 100%; padding: 12px;"><i class="fas fa-arrow-right"></i> Open Student Portal</a>
            </div>
        </div>

        <!-- Faculty Workspace -->
        <div class="glass-card" style="text-align: center; padding: 32px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="width: 70px; height: 70px; border-radius: 50%; background: var(--status-ongoing-bg); color: var(--status-ongoing-text); display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 18px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h2 style="color: var(--primary-navy); font-size: 22px; margin-bottom: 10px;">Faculty Portal</h2>
                <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 24px; line-height: 1.6;">
                    Allocate guide mentors, evaluate project proposals, verify internship offer letters/certificates, and generate analytics reports.
                </p>
            </div>
            <div>
                <a href="faculty/dashboard.php" class="btn btn-teal" style="width: 100%; padding: 12px;"><i class="fas fa-arrow-right"></i> Open Faculty Portal</a>
            </div>
        </div>
    </div>
</div>

<?php 
if (file_exists('footer.php')) { include('footer.php'); } else if (file_exists('../footer.php')) { include('../footer.php'); }
?>
