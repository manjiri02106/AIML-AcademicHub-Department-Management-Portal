<?php
// frontend/pages/faculty/mentoring.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentoring Records | AIML AcademicHub</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <?php include '../../components/sidebar.php'; ?>
    
    <main class="main-content">
        <?php include '../../components/header.php'; ?>
        
        <div class="page-container">
            <div class="glass-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h2 style="font-weight: 600;">Mentoring Sessions</h2>
                    <button class="btn btn-primary" onclick="openModal('addSessionModal')">+ Add Session</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student Roll</th>
                                <th>Student Name</th>
                                <th>Discussion Points</th>
                                <th>Action Items</th>
                            </tr>
                        </thead>
                        <tbody id="mentoringTableBody">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Session Modal -->
    <div class="modal-overlay" id="addSessionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 style="font-weight: 600;">Log Mentoring Session</h3>
                <button class="modal-close" onclick="closeModal('addSessionModal')">&times;</button>
            </div>
            <form id="sessionForm">
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <select class="form-control" id="studentSelect" required>
                        <option value="">Select a student...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Session Date</label>
                    <input type="date" class="form-control" id="sessionDate" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Discussion Points</label>
                    <textarea class="form-control" id="discussionPoints" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Action Items</label>
                    <textarea class="form-control" id="actionItems" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Record</button>
            </form>
        </div>
    </div>

    <script src="../../js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            loadRecords();
            loadStudents();

            // Set today's date as default
            document.getElementById('sessionDate').valueAsDate = new Date();

            document.getElementById('sessionForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const data = {
                    student_id: document.getElementById('studentSelect').value,
                    session_date: document.getElementById('sessionDate').value,
                    discussion_points: document.getElementById('discussionPoints').value,
                    action_items: document.getElementById('actionItems').value
                };

                const res = await apiCall('add_mentoring_record', 'POST', data);
                if (res.status === 'success') {
                    closeModal('addSessionModal');
                    document.getElementById('sessionForm').reset();
                    loadRecords();
                }
            });
        });

        async function loadRecords() {
            const res = await apiCall('get_mentoring_records');
            const tbody = document.getElementById('mentoringTableBody');
            tbody.innerHTML = '';
            
            if (res.status === 'success' && res.data.length > 0) {
                res.data.forEach(record => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="white-space: nowrap;">${record.session_date}</td>
                        <td style="font-weight: 600;">${record.roll_number}</td>
                        <td>${record.student_name}</td>
                        <td>${record.discussion_points}</td>
                        <td>${record.action_items || '-'}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-secondary);">No records found.</td></tr>';
            }
        }

        async function loadStudents() {
            const res = await apiCall('get_students');
            if (res.status === 'success') {
                const select = document.getElementById('studentSelect');
                res.data.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = `${student.roll_number} - ${student.name}`;
                    select.appendChild(option);
                });
            }
        }
    </script>
</body>
</html>
