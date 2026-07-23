document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete').forEach((button) => {
        button.addEventListener('click', (event) => {
            const confirmed = window.confirm('Are you sure you want to perform this action?');
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    const savedTheme = localStorage.getItem('aiml-theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }

    const toggle = document.getElementById('themeToggle');
    if (toggle) {
        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const nextTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('aiml-theme', nextTheme);
        });
    }
});
