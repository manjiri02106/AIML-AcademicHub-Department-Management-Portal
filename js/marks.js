/* ==========================================================================
   AIML AcademicHub Department Management Portal - Marks Entry Module
   Interactive Input, Automated Percentage/Grade/Pass-Fail & Analytics
   ========================================================================== */

let marksState = [];
let currentExamMaxMarks = 20;

function initMarksModule() {
  marksState = JSON.parse(JSON.stringify(initialPortalData.students));
  renderMarksTable();
}

function updateExamTypeMaxMarks() {
  const examType = document.getElementById('marks-exam-type')?.value || 'ut1';
  if (examType === 'ut1' || examType === 'ut2') currentExamMaxMarks = 20;
  else if (examType === 'assignment') currentExamMaxMarks = 25;
  else if (examType === 'practical') currentExamMaxMarks = 50;
  else if (examType === 'endSem') currentExamMaxMarks = 100;

  renderMarksTable();
}

function getStudentMarkValue(student) {
  const examType = document.getElementById('marks-exam-type')?.value || 'ut1';
  return student[examType] !== undefined ? student[examType] : 0;
}

function setStudentMarkValue(student, val) {
  const examType = document.getElementById('marks-exam-type')?.value || 'ut1';
  student[examType] = parseFloat(val) || 0;
}

function calculateGradeAndStatus(obtained, max) {
  const percent = max > 0 ? (obtained / max) * 100 : 0;
  let grade = 'F';
  let isPass = true;

  if (percent >= 90) grade = 'S';
  else if (percent >= 80) grade = 'A';
  else if (percent >= 70) grade = 'B';
  else if (percent >= 60) grade = 'C';
  else if (percent >= 50) grade = 'D';
  else {
    grade = 'F';
    isPass = false;
  }

  return { percent: percent.toFixed(1), grade, isPass };
}

function renderMarksTable() {
  const tbody = document.getElementById('marks-table-body');
  if (!tbody) return;

  tbody.innerHTML = '';

  let totalPercentSum = 0;
  let passCount = 0;

  marksState.forEach((student, index) => {
    const obtained = getStudentMarkValue(student);
    const { percent, grade, isPass } = calculateGradeAndStatus(obtained, currentExamMaxMarks);

    totalPercentSum += parseFloat(percent);
    if (isPass) passCount++;

    const tr = document.createElement('tr');
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
      <td>
        <input type="number" min="0" max="${currentExamMaxMarks}" step="0.5" class="marks-input-cell" value="${obtained}" onchange="handleMarkInputChange(${index}, this.value)" />
      </td>
      <td><strong>${currentExamMaxMarks}</strong></td>
      <td>
        <span style="font-weight: 600; color: ${isPass ? 'var(--text-main)' : 'var(--error-color)'};">
          ${percent}%
        </span>
      </td>
      <td>
        <span class="grade-badge grade-${grade}">${grade}</span>
      </td>
      <td>
        <span class="badge ${isPass ? 'badge-success' : 'badge-danger'}">
          ${isPass ? '<i class="fas fa-check-circle"></i> Pass' : '<i class="fas fa-times-circle"></i> Fail'}
        </span>
      </td>
    `;
    tbody.appendChild(tr);
  });

  updateMarksSummaryStats(totalPercentSum, passCount, marksState.length);
}

function handleMarkInputChange(index, val) {
  const num = Math.min(Math.max(0, parseFloat(val) || 0), currentExamMaxMarks);
  setStudentMarkValue(marksState[index], num);
  renderMarksTable();
}

function updateMarksSummaryStats(totalPercentSum, passCount, total) {
  const avgPercent = total > 0 ? (totalPercentSum / total).toFixed(1) : 0;
  const passRate = total > 0 ? ((passCount / total) * 100).toFixed(1) : 0;

  const avgEl = document.getElementById('marks-stat-avg');
  const passRateEl = document.getElementById('marks-stat-pass-rate');

  if (avgEl) avgEl.textContent = `${avgPercent}%`;
  if (passRateEl) passRateEl.textContent = `${passRate}%`;
}

function saveMarksDraft() {
  showToast('Draft Saved', 'Marks draft saved to local session', 'info');
}

function publishMarks() {
  openModal('publishMarksModal');
}

function confirmPublishMarks() {
  closeModal('publishMarksModal');
  showToast('Marks Published!', 'Results published on Student Academic Portal', 'success');

  // Add activity log
  initialPortalData.recentActivities.unshift({
    type: "marks",
    text: `Published Unit Test 1 Marks for Sem VI AIML`,
    time: "Just now",
    icon: "fa-award",
    bg: "var(--warning-bg)",
    color: "var(--warning-color)"
  });
  renderRecentActivities();
}

function downloadMarksExcel() {
  if (typeof XLSX !== 'undefined') {
    const examType = document.getElementById('marks-exam-type')?.value.toUpperCase() || 'EXAM';
    const exportData = marksState.map(s => {
      const obtained = getStudentMarkValue(s);
      const { percent, grade, isPass } = calculateGradeAndStatus(obtained, currentExamMaxMarks);
      return {
        "Roll Number": s.rollNo,
        "Student Name": s.name,
        "Marks Obtained": obtained,
        "Max Marks": currentExamMaxMarks,
        "Percentage": `${percent}%`,
        "Grade": grade,
        "Status": isPass ? "PASS" : "FAIL"
      };
    });

    const worksheet = XLSX.utils.json_to_sheet(exportData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Marks_Report");
    XLSX.writeFile(workbook, `AIML_${examType}_Marks_${new Date().toISOString().slice(0,10)}.xlsx`);
    showToast('Export Complete', 'Marks report exported to Excel', 'success');
  }
}

function downloadMarksPDF() {
  window.print();
}
