<?php
// ====================================================================
// AIML AcademicHub - Department Management Portal
// Header Component with Light/Dark Theme Switcher
// ====================================================================
if (!isset($pageTitle)) {
    $pageTitle = "Laboratory Management | AIML AcademicHub";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script>
        // Inline Theme Script to prevent flash of wrong theme
        (function() {
            const savedTheme = localStorage.getItem('academichub_theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>
    <div class="app-layout">
        <!-- Top Navbar -->
        <header class="app-header">
            <div class="header-left">
                <button class="mobile-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle Navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="brand-badge">
                    <i class="fa-solid fa-microchip brand-icon"></i>
                    <div class="brand-text">
                        <span class="hub-title">AIML AcademicHub</span>
                        <span class="sub-title">Department Management Portal</span>
                    </div>
                </div>
            </div>
            
            <div class="header-center">
                <h1 class="module-header-title">Laboratory Management Module</h1>
            </div>

            <div class="header-right">
                <!-- Theme Toggle Button -->
                <button class="theme-toggle-btn" id="themeToggleBtn" onclick="toggleTheme()" title="Switch Light/Dark Mode">
                    <i class="fa-solid fa-sun icon-sun"></i>
                    <i class="fa-solid fa-moon icon-moon"></i>
                    <span class="theme-label">Theme</span>
                </button>
                
                <!-- Admin User Profile Pill -->
                <div class="user-profile-pill">
                    <div class="user-avatar">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name">Dept. Incharge</span>
                        <span class="user-role">AIML Department</span>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="app-body">
