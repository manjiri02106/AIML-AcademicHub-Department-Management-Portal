/* ==========================================================================
   AIML AcademicHub Department Management Portal - Notices Module
   Announcement Board, Priority Badges, Notice Publisher & Pin Handler
   ========================================================================== */

let noticesState = [];

function initNoticesModule() {
  noticesState = [...initialPortalData.notices];
  renderNoticesFeed();
}

function renderNoticesFeed() {
  const container = document.getElementById('notices-feed-container');
  if (!container) return;

  const categoryFilter = document.getElementById('notice-category-filter')?.value || 'all';

  const filtered = noticesState.filter(n => {
    return categoryFilter === 'all' || n.category.toLowerCase() === categoryFilter.toLowerCase();
  });

  container.innerHTML = '';

  if (filtered.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 3rem; background: var(--surface-card); border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
        <i class="fas fa-bullhorn" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <h3>No Notices Posted</h3>
        <p>No active department notices match your selection.</p>
      </div>
    `;
    return;
  }

  filtered.forEach(notice => {
    const card = document.createElement('div');
    card.className = `notice-card fade-in ${notice.priority === 'urgent' ? 'urgent' : ''} ${notice.pinned ? 'pinned' : ''}`;
    
    let priorityBadge = `<span class="badge badge-primary">${notice.category}</span>`;
    if (notice.priority === 'urgent') {
      priorityBadge = `<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> URGENT</span>`;
    } else if (notice.priority === 'warning') {
      priorityBadge = `<span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> ATTENTION</span>`;
    }

    card.innerHTML = `
      <div class="notice-header">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
          ${notice.pinned ? '<i class="fas fa-thumbtack" style="color: var(--primary-blue);" title="Pinned Notice"></i>' : ''}
          ${priorityBadge}
          <span style="font-size: 0.8rem; color: var(--text-muted);">${notice.date}</span>
        </div>
        <span style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted);">By ${notice.author}</span>
      </div>

      <h3 class="notice-title" style="margin-bottom: 0.5rem;">${notice.title}</h3>
      <p style="font-size: 0.88rem; color: var(--text-main); line-height: 1.5; margin-bottom: 1rem;">${notice.content}</p>

      <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
        <button type="button" class="btn btn-outline btn-sm" onclick="togglePinNotice('${notice.id}')">
          <i class="fas fa-thumbtack"></i> ${notice.pinned ? 'Unpin' : 'Pin'}
        </button>
        <button type="button" class="btn btn-outline btn-sm" onclick="shareNotice('${notice.id}')">
          <i class="fas fa-share-alt"></i> Share
        </button>
        <button type="button" class="btn btn-outline btn-sm" style="color: var(--error-color);" onclick="deleteNotice('${notice.id}')">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    `;
    container.appendChild(card);
  });
}

function openAddNoticeModal() {
  openModal('addNoticeModal');
}

function handleAddNoticeSubmit(event) {
  event.preventDefault();
  const form = event.target;

  const newNotice = {
    id: "NOT-" + Math.floor(100 + Math.random() * 900),
    title: form.noticeTitle.value,
    category: form.noticeCategory.value,
    priority: form.noticePriority.value,
    date: new Date().toISOString().slice(0,10),
    author: initialPortalData.faculty.name,
    pinned: form.noticePinned.checked,
    content: form.noticeContent.value
  };

  noticesState.unshift(newNotice);
  renderNoticesFeed();
  closeModal('addNoticeModal');
  form.reset();
  showToast('Notice Published', 'New announcement posted to department feed', 'success');

  // Activity log
  initialPortalData.recentActivities.unshift({
    type: "notice",
    text: `Posted notice '${newNotice.title}'`,
    time: "Just now",
    icon: "fa-bullhorn",
    bg: "var(--info-bg)",
    color: "var(--info-color)"
  });
  renderRecentActivities();
}

function togglePinNotice(noticeId) {
  const notice = noticesState.find(n => n.id === noticeId);
  if (notice) {
    notice.pinned = !notice.pinned;
    renderNoticesFeed();
    showToast('Notice Updated', notice.pinned ? 'Notice pinned to top' : 'Notice unpinned', 'info');
  }
}

function shareNotice(noticeId) {
  showToast('Share Link', `Notice link generated for ${noticeId}`, 'info');
}

function deleteNotice(noticeId) {
  if (confirm("Are you sure you want to delete this notice?")) {
    noticesState = noticesState.filter(n => n.id !== noticeId);
    renderNoticesFeed();
    showToast('Notice Deleted', 'Announcement removed', 'warning');
  }
}
