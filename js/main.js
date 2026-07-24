/**
 * AIML AcademicHub - Department Management Portal
 * Core JavaScript Utility File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Theme
    initTheme();

    // Mobile Sidebar Handlers
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggleBtn && sidebar && overlay) {
        sidebarToggleBtn.addEventListener('click', function() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        });
    }

    if (sidebarCloseBtn && sidebar && overlay) {
        sidebarCloseBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    }

    function closeSidebar() {
        if (sidebar && overlay) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }
    }
});

// Light/Dark Theme Switcher with LocalStorage
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = (currentTheme === 'light') ? 'dark' : 'light';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('academichub_theme', newTheme);
    
    showToast(`Switched to ${newTheme.toUpperCase()} mode`, 'info');
}

function initTheme() {
    const savedTheme = localStorage.getItem('academichub_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
}

// Toast Notification System
function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let iconClass = 'fa-circle-info';
    if (type === 'success') iconClass = 'fa-circle-check';
    if (type === 'error') iconClass = 'fa-circle-exclamation';

    toast.innerHTML = `
        <i class="fa-solid ${iconClass}"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Modal Helpers
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}
