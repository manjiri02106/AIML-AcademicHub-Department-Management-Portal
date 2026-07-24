<?php
// frontend/pages/faculty/profile.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profile | AIML AcademicHub</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php include '../../components/sidebar.php'; ?>
    
    <main class="main-content">
        <?php include '../../components/header.php'; ?>
        
        <div class="page-container">
            <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
                <h2 style="margin-bottom: 24px; font-weight: 600;">Faculty Profile</h2>
                
                <form id="profileForm">
                    <div class="stats-grid">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designation" disabled>
                        <small style="color: var(--text-secondary);">Contact administrator to change designation</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Qualifications</label>
                        <textarea class="form-control" id="qualifications" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Research Interests</label>
                        <textarea class="form-control" id="researchInterests" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" id="saveBtn">Save Profile</button>
                    <span id="saveStatus" style="margin-left: 12px; color: #34d399; display: none;">Saved successfully!</span>
                </form>
            </div>
        </div>
    </main>

    <script src="../../js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const profileReq = await apiCall('get_profile');
            if (profileReq.status === 'success') {
                const p = profileReq.data || {};
                document.getElementById('fullName').value = p.full_name || '';
                document.getElementById('phone').value = p.phone || '';
                document.getElementById('designation').value = p.designation || '';
                document.getElementById('qualifications').value = p.qualifications || '';
                document.getElementById('researchInterests').value = p.research_interests || '';
            }

            document.getElementById('profileForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('saveBtn');
                btn.textContent = 'Saving...';
                
                const data = {
                    full_name: document.getElementById('fullName').value,
                    phone: document.getElementById('phone').value,
                    qualifications: document.getElementById('qualifications').value,
                    research_interests: document.getElementById('researchInterests').value
                };

                const res = await apiCall('update_profile', 'POST', data);
                if (res.status === 'success') {
                    const status = document.getElementById('saveStatus');
                    status.style.display = 'inline';
                    setTimeout(() => status.style.display = 'none', 3000);
                }
                btn.textContent = 'Save Profile';
            });
        });
    </script>
</body>
</html>
