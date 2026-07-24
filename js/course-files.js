/* ==========================================================================
   AIML AcademicHub Department Management Portal - Course Files Module
   File Repository, Drag & Drop Uploads, Categories, Preview & Download Handlers
   ========================================================================== */

let courseFilesState = [];
let activeFileCategory = 'All';

function initCourseFilesModule() {
  courseFilesState = [...initialPortalData.courseFiles];
  renderCourseFilesGrid();
  setupDropzone();
}

function setupDropzone() {
  const dropzone = document.getElementById('fileDropzone');
  if (!dropzone) return;

  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
  });

  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  ['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
  });

  dropzone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files.length > 0) {
      openUploadFileModal(files[0].name);
    }
  });
}

function filterFileCategory(category, element) {
  activeFileCategory = category;
  
  // Highlight active tab
  document.querySelectorAll('.file-tab').forEach(tab => tab.classList.remove('active'));
  if (element) element.classList.add('active');

  renderCourseFilesGrid();
}

function renderCourseFilesGrid() {
  const container = document.getElementById('course-files-grid');
  if (!container) return;

  const searchQuery = document.getElementById('file-search-input')?.value.toLowerCase() || '';

  const filtered = courseFilesState.filter(file => {
    const matchesCategory = activeFileCategory === 'All' || file.category.toLowerCase() === activeFileCategory.toLowerCase();
    const matchesSearch = file.title.toLowerCase().includes(searchQuery) || file.subject.toLowerCase().includes(searchQuery);
    return matchesCategory && matchesSearch;
  });

  container.innerHTML = '';

  if (filtered.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: var(--surface-card); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
        <i class="fas fa-folder-open" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <h3>No Files Found</h3>
        <p>No documents match the selected category or search criteria.</p>
      </div>
    `;
    return;
  }

  filtered.forEach(file => {
    let iconClass = 'fa-file-alt';
    let typeClass = 'file-doc';

    if (file.fileType === 'pdf') { iconClass = 'fa-file-pdf'; typeClass = 'file-pdf'; }
    else if (file.fileType === 'ppt') { iconClass = 'fa-file-powerpoint'; typeClass = 'file-ppt'; }
    else if (file.fileType === 'zip') { iconClass = 'fa-file-archive'; typeClass = 'file-zip'; }

    const card = document.createElement('div');
    card.className = 'file-card fade-in';
    card.innerHTML = `
      <div style="display: flex; align-items: flex-start; justify-content: space-between;">
        <div class="file-icon-type ${typeClass}">
          <i class="fas ${iconClass}"></i>
        </div>
        <span class="badge badge-primary">${file.category}</span>
      </div>

      <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.35rem; line-height: 1.3;">${file.title}</h4>
      <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.75rem;">${file.subject} • ${file.semester}</p>

      <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: var(--text-light); margin-top: auto; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
        <span><i class="far fa-clock"></i> ${file.uploadDate}</span>
        <span><i class="fas fa-download"></i> ${file.downloads} dls</span>
        <span><i class="fas fa-hdd"></i> ${file.fileSize}</span>
      </div>

      <div style="display: flex; gap: 0.5rem; margin-top: 0.85rem;">
        <button type="button" class="btn btn-outline btn-sm" style="flex: 1;" onclick="previewCourseFile('${file.id}')">
          <i class="fas fa-eye"></i> Preview
        </button>
        <button type="button" class="btn btn-primary btn-sm" style="flex: 1;" onclick="downloadCourseFile('${file.id}')">
          <i class="fas fa-download"></i> Download
        </button>
        <button type="button" class="btn btn-outline btn-sm btn-icon" onclick="shareCourseFile('${file.id}')">
          <i class="fas fa-share-alt"></i>
        </button>
        <button type="button" class="btn btn-outline btn-sm btn-icon" style="color: var(--error-color);" onclick="deleteCourseFile('${file.id}')">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    `;
    container.appendChild(card);
  });
}

function openUploadFileModal(prefillFileName = '') {
  const nameInput = document.getElementById('uploadFileTitle');
  if (nameInput && prefillFileName) nameInput.value = prefillFileName;
  openModal('uploadFileModal');
}

function handleUploadFileSubmit(event) {
  event.preventDefault();
  const form = event.target;

  const newFile = {
    id: "FILE-" + Math.floor(100 + Math.random() * 900),
    title: form.fileTitle.value,
    category: form.fileCategory.value,
    fileType: form.fileTitle.value.endsWith('.ppt') ? 'ppt' : form.fileTitle.value.endsWith('.zip') ? 'zip' : 'pdf',
    subject: form.fileSubject.value,
    semester: "Sem VI",
    uploadDate: new Date().toISOString().slice(0,10),
    fileSize: "3.5 MB",
    downloads: 0
  };

  courseFilesState.unshift(newFile);
  renderCourseFilesGrid();
  closeModal('uploadFileModal');
  form.reset();
  showToast('File Uploaded', `${newFile.title} added to ${newFile.category}`, 'success');

  // Add activity
  initialPortalData.recentActivities.unshift({
    type: "file",
    text: `Uploaded '${newFile.title}' to Course Files`,
    time: "Just now",
    icon: "fa-file-upload",
    bg: "var(--success-bg)",
    color: "var(--success-color)"
  });
  renderRecentActivities();
}

function downloadCourseFile(fileId) {
  const file = courseFilesState.find(f => f.id === fileId);
  if (file) {
    file.downloads++;
    renderCourseFilesGrid();
    showToast('Download Started', `Downloading ${file.title}`, 'success');
  }
}

function previewCourseFile(fileId) {
  const file = courseFilesState.find(f => f.id === fileId);
  if (!file) return;

  const container = document.getElementById('filePreviewContent');
  if (container) {
    container.innerHTML = `
      <div style="background: var(--body-bg); padding: 1.5rem; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--border-color);">
        <i class="fas fa-file-pdf" style="font-size: 3.5rem; color: var(--error-color); margin-bottom: 1rem;"></i>
        <h4>${file.title}</h4>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">${file.subject} • Uploaded on ${file.uploadDate}</p>
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-sm); box-shadow: var(--shadow-sm); font-family: monospace; font-size: 0.8rem; text-align: left;">
          [PDF Document Preview Simulator]<br/>
          ----------------------------------------<br/>
          Department of AIML - Academic Document Repository<br/>
          Course: ${file.subject}<br/>
          Status: Verified Academic Asset<br/>
          File Hash: md5_8f9a0c21b3d4e<br/>
        </div>
      </div>
    `;
    openModal('filePreviewModal');
  }
}

function shareCourseFile(fileId) {
  const shareUrl = `${window.location.origin}/portal/course-files?id=${fileId}`;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(shareUrl);
    showToast('Link Copied', 'Download link copied to clipboard', 'info');
  } else {
    showToast('Share Link', shareUrl, 'info');
  }
}

function deleteCourseFile(fileId) {
  if (confirm("Are you sure you want to delete this course file?")) {
    courseFilesState = courseFilesState.filter(f => f.id !== fileId);
    renderCourseFilesGrid();
    showToast('File Removed', 'Course file deleted', 'warning');
  }
}
