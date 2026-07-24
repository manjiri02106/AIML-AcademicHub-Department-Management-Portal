/* ==========================================================================
   AIML AcademicHub Department Management Portal - Attendance Module
   Marking, Statistics, History, Monthly Reports & Excel/PDF Exports
   ========================================================================== */

let currentAttendanceState = [];

function initAttendanceModule() {
  currentAttendanceState = JSON.parse(JSON.stringify(initialPortalData.students));
  renderAttendanceTable();
  updateAttendanceSummaryStats();
}

function renderAttendanceTable() {
  const tbody = document.getElementById('attendance-table-body');
  if (!tbody) return;

  tbody.innerHTML = '';

  currentAttendanceState.forEach((student, index) => {
    const tr = document.createElement('tr');
    if (student.status === 'absent') tr.classList.add('low-attendance-row');

    tr.innerHTML = `
      <td><strong>${student.rollNo}</strong></td>
      <td>
        <div style="display: flex; align-items: center; gap: 0.6rem;">
          <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--accent-blue); color: var(--primary-blue); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem;">
            ${student.name.charAt(0)}
          </div>
          <span>${student.name}</span>
        </div>
      </td>
      <td text-align="center">
        <div class="attendance-status-toggle">
          <button type="button" class="attendance-opt-btn ${student.status === 'present' ? 'active-present' : ''}" onclick="setStudentAttendance(${index}, 'present')">
            <i class="fas fa-check"></i> Present
          </button>
          <button type="button" class="attendance-opt-btn ${student.status === 'absent' ? 'active-absent' : ''}" onclick="setStudentAttendance(${index}, 'absent')">
            <i class="fas fa-times"></i> Absent
          </button>
          <button type="button" class="attendance-opt-btn ${student.status === 'late' ? 'active-late' : ''}" onclick="setStudentAttendance(${index}, 'late')">
            <i class="fas fa-clock"></i> Late
          </button>
        </div>
      </td>
      <td>
        <input type="text" class="form-control" style="padding: 0.35rem 0.6rem; font-size: 0.82rem;" value="${student.remarks || ''}" placeholder="Add remark..." onchange="updateStudentRemark(${index}, this.value)" />
      </td>
    `;
    tbody.appendChild(tr);
  });

  updateAttendanceSummaryStats();
}

function setStudentAttendance(index, status) {
  currentAttendanceState[index].status = status;
  renderAttendanceTable();
}

function updateStudentRemark(index, value) {
  currentAttendanceState[index].remarks = value;
}

function markAllPresent() {
  currentAttendanceState.forEach(s => s.status = 'present');
  renderAttendanceTable();
  showToast('Marked All Present', 'All student statuses set to Present', 'success');
}

function saveAttendanceData() {
  const presentCount = currentAttendanceState.filter(s => s.status === 'present' || s.status === 'late').length;
  const total = currentAttendanceState.length;
  const percent = ((presentCount / total) * 100).toFixed(1);

  showToast('Attendance Saved Successfully', `Recorded ${presentCount}/${total} Present (${percent}%)`, 'success');
  
  // Add to activity feed
  initialPortalData.recentActivities.unshift({
    type: "attendance",
    text: `Marked attendance for Sem VI - AIML (${percent}% Present)`,
    time: "Just now",
    icon: "fa-user-check",
    bg: "var(--accent-blue)",
    color: "var(--primary-blue)"
  });
  
  renderRecentActivities();
}

function updateAttendanceSummaryStats() {
  const total = currentAttendanceState.length;
  const present = currentAttendanceState.filter(s => s.status === 'present').length;
  const absent = currentAttendanceState.filter(s => s.status === 'absent').length;
  const late = currentAttendanceState.filter(s => s.status === 'late').length;
  const percent = total > 0 ? (((present + late) / total) * 100).toFixed(1) : 0;

  const totalEl = document.getElementById('att-stat-total');
  const presentEl = document.getElementById('att-stat-present');
  const absentEl = document.getElementById('att-stat-absent');
  const percentEl = document.getElementById('att-stat-percent');

  if (totalEl) totalEl.textContent = total;
  if (presentEl) presentEl.textContent = present + late;
  if (absentEl) absentEl.textContent = absent;
  if (percentEl) percentEl.textContent = `${percent}%`;
}

/* Exports */
function downloadAttendanceExcel() {
  if (typeof XLSX !== 'undefined') {
    const exportData = currentAttendanceState.map(s => ({
      "Roll Number": s.rollNo,
      "Student Name": s.name,
      "Status": s.status.toUpperCase(),
      "Remarks": s.remarks || "N/A"
    }));

    const worksheet = XLSX.utils.json_to_sheet(exportData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Attendance");
    XLSX.writeFile(workbook, `AIML_Attendance_${new Date().toISOString().slice(0,10)}.xlsx`);
    showToast('Export Complete', 'Attendance Excel spreadsheet generated', 'success');
  } else {
    // Fallback CSV
    let csvContent = "data:text/csv;charset=utf-8,Roll No,Student Name,Status,Remarks\n";
    currentAttendanceState.forEach(s => {
      csvContent += `${s.rollNo},"${s.name}",${s.status.toUpperCase()},"${s.remarks || ''}"\n`;
    });
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `AIML_Attendance_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Export Complete', 'Attendance CSV file generated', 'success');
  }
}

function downloadAttendancePDF() {
  window.print();
}

function openAttendanceHistoryModal() {
  openModal('attendanceHistoryModal');
}

function openMonthlyReportModal() {
  openModal('monthlyReportModal');
}
