/* ==========================================================================
   AIML AcademicHub Department Management Portal - Core Application JS
   Router, Theme Manager, Modal Manager, Notification Drawer & UI Helpers
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  initPortalApp();
});

function initPortalApp() {
  // Initialize Modules
  initTheme();
  initNavigation();
  initRecentActivities();
  initUpcomingLectures();

  // Initialize Sub-Modules
  if (typeof initAttendanceModule === 'function') initAttendanceModule();
  if (typeof initSubjectsModule === 'function') initSubjectsModule();
  if (typeof initMarksModule === 'function') initMarksModule();
  if (typeof initCourseFilesModule === 'function') initCourseFilesModule();
  if (typeof initNoticesModule === 'function') initNoticesModule();
  
  // Charts Initialization
  setTimeout(() => {
    if (typeof initDashboardCharts === 'function') initDashboardCharts();
  }, 100);
}

/* --------------------------------------------------------------------------
   TAB NAVIGATION ROUTER
   -------------------------------------------------------------------------- */
function initNavigation() {
  const sidebarLinks = document.querySelectorAll('.sidebar-item[data-tab]');
  sidebarLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const tabId = link.getAttribute('data-tab');
      switchTab(tabId);
    });
  });
}

function switchTab(tabId) {
  // Update sidebar active link
  document.querySelectorAll('.sidebar-item[data-tab]').forEach(item => {
    item.classList.remove('active');
    if (item.getAttribute('data-tab') === tabId) {
      item.classList.add('active');
    }
  });

  // Toggle view containers
  document.querySelectorAll('.module-container').forEach(container => {
    container.style.display = 'none';
  });

  const activeModule = document.getElementById(`module-${tabId}`);
  if (activeModule) {
    activeModule.style.display = 'block';
    activeModule.classList.add('fade-in');
  }

  // Mobile sidebar auto-close
  if (window.innerWidth <= 1024) {
    closeMobileSidebar();
  }

  // Re-trigger chart layouts if returning to dashboard or marks
  if (tabId === 'dashboard' && typeof initDashboardCharts === 'function') {
    initDashboardCharts();
  }
}

/* --------------------------------------------------------------------------
   DARK MODE THEME MANAGER
   -------------------------------------------------------------------------- */
function initTheme() {
  const savedTheme = localStorage.getItem('portal_theme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark-mode');
    updateThemeIcon(true);
  }
}

function toggleDarkMode() {
  const isDark = document.body.classList.toggle('dark-mode');
  localStorage.setItem('portal_theme', isDark ? 'dark' : 'light');
  updateThemeIcon(isDark);

  if (typeof updateChartsTheme === 'function') {
    updateChartsTheme();
  }

  showToast('Theme Changed', `Switched to ${isDark ? 'Dark' : 'Light'} Mode`, 'info');
}

function updateThemeIcon(isDark) {
  const icon = document.getElementById('themeToggleIcon');
  if (icon) {
    icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
  }
}

/* --------------------------------------------------------------------------
   MOBILE SIDEBAR DRAWER TOGGLE
   -------------------------------------------------------------------------- */
function toggleMobileSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    sidebar.classList.toggle('show-mobile');
  }
}

function closeMobileSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    sidebar.classList.remove('show-mobile');
  }
}

/* --------------------------------------------------------------------------
   DROPDOWNS & NOTIFICATIONS
   -------------------------------------------------------------------------- */
function toggleDropdown(menuId) {
  const menu = document.getElementById(menuId);
  if (!menu) return;

  const isVisible = menu.classList.contains('show');
  
  // Close all open dropdowns first
  document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));

  if (!isVisible) {
    menu.classList.add('show');
  }
}

// Close dropdowns on outside click
document.addEventListener('click', (e) => {
  if (!e.target.closest('.faculty-profile-pill') && !e.target.closest('.nav-icon-btn')) {
    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
  }
});

/* --------------------------------------------------------------------------
   MODAL DIALOG CONTROLLER
   -------------------------------------------------------------------------- */
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('active');
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('active');
  }
}

/* --------------------------------------------------------------------------
   TOAST NOTIFICATION SYSTEM
   -------------------------------------------------------------------------- */
function showToast(title, message, type = 'info') {
  const container = document.getElementById('toast-container');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;

  let icon = 'fa-info-circle';
  if (type === 'success') icon = 'fa-check-circle';
  if (type === 'error') icon = 'fa-times-circle';
  if (type === 'warning') icon = 'fa-exclamation-triangle';

  toast.innerHTML = `
    <i class="fas ${icon} toast-icon"></i>
    <div class="toast-content">
      <span class="toast-title">${title}</span>
      <span class="toast-message">${message}</span>
    </div>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    toast.style.transition = 'all 0.3s ease-out';
    setTimeout(() => toast.remove(), 300);
  }, 4000);
}

/* --------------------------------------------------------------------------
   RECENT ACTIVITIES & LECTURES RENDERING
   -------------------------------------------------------------------------- */
function initRecentActivities() {
  renderRecentActivities();
}

function renderRecentActivities() {
  const container = document.getElementById('recent-activities-list');
  if (!container) return;

  container.innerHTML = '';

  initialPortalData.recentActivities.forEach(act => {
    const item = document.createElement('div');
    item.className = 'activity-item';
    item.innerHTML = `
      <div class="activity-icon" style="background: ${act.bg}; color: ${act.color};">
        <i class="fas ${act.icon}"></i>
      </div>
      <div class="activity-details">
        <span class="activity-text">${act.text}</span>
        <span class="activity-time">${act.time}</span>
      </div>
    `;
    container.appendChild(item);
  });
}

function initUpcomingLectures() {
  const container = document.getElementById('upcoming-lectures-list');
  if (!container) return;

  container.innerHTML = '';

  initialPortalData.upcomingLectures.forEach(lec => {
    const card = document.createElement('div');
    card.className = 'upcoming-lecture-card';
    card.innerHTML = `
      <div>
        <h4 style="font-size: 0.92rem; font-weight: 600;">${lec.subject}</h4>
        <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.15rem;">${lec.room} • ${lec.batch}</p>
      </div>
      <span class="lecture-time-badge">${lec.time}</span>
    `;
    container.appendChild(card);
  });
}

function saveFacultySettings(event) {
  event.preventDefault();
  showToast('Settings Saved', 'Faculty portal preferences updated', 'success');
}
