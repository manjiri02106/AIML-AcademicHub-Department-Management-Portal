/* ==========================================================================
   AIML AcademicHub Department Management Portal - Subject Management Module
   Card Layouts, Filter & Search, Add/Edit Modals & CSV/Excel Imports
   ========================================================================== */

let subjectsState = [];

function initSubjectsModule() {
  subjectsState = [...initialPortalData.subjects];
  renderSubjectsGrid();
}

function renderSubjectsGrid(filteredList = null) {
  const container = document.getElementById('subject-cards-grid');
  if (!container) return;

  const list = filteredList || subjectsState;
  container.innerHTML = '';

  if (list.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: var(--surface-card); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
        <i class="fas fa-book-open" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <h3>No Subjects Found</h3>
        <p>Try adjusting your search query or filter options.</p>
      </div>
    `;
    return;
  }

  list.forEach(subject => {
    const card = document.createElement('div');
    card.className = 'subject-card fade-in';
    card.innerHTML = `
      <div class="subject-card-header">
        <span class="subject-code-tag">${subject.code}</span>
        <span class="badge badge-primary">${subject.semester}</span>
      </div>
      <h3 class="subject-title">${subject.name}</h3>
      
      <div class="subject-meta-list">
        <div class="subject-meta-item">
          <i class="fas fa-award"></i>
          <span>${subject.credits} Credits</span>
        </div>
        <div class="subject-meta-item">
          <i class="fas fa-user-tie"></i>
          <span>${subject.faculty}</span>
        </div>
        <div class="subject-meta-item">
          <i class="fas fa-chalkboard-teacher"></i>
          <span>${subject.lectureHours} hrs/wk (Lect)</span>
        </div>
        <div class="subject-meta-item">
          <i class="fas fa-laptop-code"></i>
          <span>${subject.practicalHours} hrs/wk (Prac)</span>
        </div>
      </div>

      <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8rem; margin-top: 0.5rem; color: var(--text-muted);">
        <span>Enrolled: <strong>${subject.enrolledCount || 64}</strong></span>
        <span>Avg Att: <strong style="color: var(--success-color);">${subject.avgAttendance || '90%'}</strong></span>
      </div>

      <div class="subject-card-actions">
        <button type="button" class="btn btn-outline btn-sm" onclick="viewSubjectDetails('${subject.id}')">
          <i class="fas fa-eye"></i> Details
        </button>
        <button type="button" class="btn btn-outline btn-sm" onclick="editSubjectModal('${subject.id}')">
          <i class="fas fa-edit"></i> Edit
        </button>
        <button type="button" class="btn btn-outline btn-sm" style="color: var(--error-color);" onclick="deleteSubject('${subject.id}')">
          <i class="fas fa-trash-alt"></i>
        </button>
      </div>
    `;
    container.appendChild(card);
  });
}

function filterSubjects() {
  const search = document.getElementById('subject-search-input')?.value.toLowerCase() || '';
  const semFilter = document.getElementById('subject-sem-filter')?.value || 'all';
  const facultyFilter = document.getElementById('subject-faculty-filter')?.value || 'all';

  const filtered = subjectsState.filter(s => {
    const matchesSearch = s.name.toLowerCase().includes(search) || s.code.toLowerCase().includes(search);
    const matchesSem = semFilter === 'all' || s.semester.toLowerCase().includes(semFilter.toLowerCase());
    const matchesFaculty = facultyFilter === 'all' || s.faculty.toLowerCase().includes(facultyFilter.toLowerCase());
    return matchesSearch && matchesSem && matchesFaculty;
  });

  renderSubjectsGrid(filtered);
}

function openAddSubjectModal() {
  openModal('addSubjectModal');
}

function handleAddSubjectSubmit(event) {
  event.preventDefault();
  const form = event.target;
  
  const newSubject = {
    id: "SUB-" + Math.floor(100 + Math.random() * 900),
    code: form.subjectCode.value,
    name: form.subjectName.value,
    semester: form.subjectSem.value,
    credits: parseInt(form.subjectCredits.value),
    faculty: form.subjectFaculty.value,
    lectureHours: parseInt(form.subjectLecHours.value),
    practicalHours: parseInt(form.subjectPracHours.value),
    enrolledCount: 64,
    avgAttendance: "100%"
  };

  subjectsState.unshift(newSubject);
  renderSubjectsGrid();
  closeModal('addSubjectModal');
  form.reset();
  showToast('Subject Created', `${newSubject.name} (${newSubject.code}) added successfully`, 'success');
}

function deleteSubject(subjectId) {
  if (confirm("Are you sure you want to delete this subject?")) {
    subjectsState = subjectsState.filter(s => s.id !== subjectId);
    renderSubjectsGrid();
    showToast('Subject Deleted', 'Subject removed from list', 'warning');
  }
}

function viewSubjectDetails(subjectId) {
  const subject = subjectsState.find(s => s.id === subjectId);
  if (!subject) return;

  const content = document.getElementById('subjectDetailContent');
  if (content) {
    content.innerHTML = `
      <h4>${subject.code}: ${subject.name}</h4>
      <p style="margin-bottom: 1rem;">Faculty Assigned: <strong>${subject.faculty}</strong></p>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: var(--body-bg); padding: 1rem; border-radius: var(--radius-sm);">
          <span style="font-size: 0.75rem; color: var(--text-muted);">Semester</span>
          <h5>${subject.semester}</h5>
        </div>
        <div style="background: var(--body-bg); padding: 1rem; border-radius: var(--radius-sm);">
          <span style="font-size: 0.75rem; color: var(--text-muted);">Credits</span>
          <h5>${subject.credits} Credits</h5>
        </div>
      </div>

      <h5>Syllabus Overview</h5>
      <ul style="padding-left: 1.25rem; font-size: 0.88rem; color: var(--text-muted);">
        <li>Unit 1: Foundational Principles & Architecture</li>
        <li>Unit 2: Mathematical Formulations & Optimization</li>
        <li>Unit 3: Deep Neural Networks & Fine-Tuning</li>
        <li>Unit 4: Real-world Applications & Capstone Evaluation</li>
      </ul>
    `;
    openModal('subjectDetailModal');
  }
}

function editSubjectModal(subjectId) {
  showToast('Edit Mode', `Opening editor for ${subjectId}`, 'info');
}

function exportSubjectsExcel() {
  if (typeof XLSX !== 'undefined') {
    const exportData = subjectsState.map(s => ({
      "Subject Code": s.code,
      "Subject Name": s.name,
      "Semester": s.semester,
      "Credits": s.credits,
      "Assigned Faculty": s.faculty,
      "Lecture Hrs/Wk": s.lectureHours,
      "Practical Hrs/Wk": s.practicalHours
    }));

    const worksheet = XLSX.utils.json_to_sheet(exportData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Subjects");
    XLSX.writeFile(workbook, `AIML_Subjects_${new Date().toISOString().slice(0,10)}.xlsx`);
    showToast('Export Complete', 'Subject list exported to Excel', 'success');
  }
}
