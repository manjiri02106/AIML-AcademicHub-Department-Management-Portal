<?php
// frontend/pages/faculty/course-allocation.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Allocation | AIML AcademicHub</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php include '../../components/sidebar.php'; ?>
    
    <main class="main-content">
        <?php include '../../components/header.php'; ?>
        
        <div class="page-container">
            <div class="glass-card">
                <h2 style="margin-bottom: 24px; font-weight: 600;">My Allocated Courses</h2>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="courseTableBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="../../js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const res = await apiCall('get_courses');
            const tbody = document.getElementById('courseTableBody');
            
            if (res.status === 'success' && res.data.length > 0) {
                res.data.forEach(course => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight: 600;">${course.course_code}</td>
                        <td>${course.course_name}</td>
                        <td>Semester ${course.semester}</td>
                        <td>${course.academic_year}</td>
                        <td><span class="badge">Active</span></td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-secondary);">No courses allocated yet.</td></tr>';
            }
        });
    </script>
</body>
</html>
