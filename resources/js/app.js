/**
 * Light / dark theme toggle.
 *
 * The initial theme is set by an inline script in the layout's <head>. This
 * handles clicks on any [data-theme-toggle] button, remembers the choice, and
 * follows the system setting until the user has picked a theme themselves.
 */
const root = document.documentElement;
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)');

function storedTheme() {
    try {
        return localStorage.getItem('theme');
    } catch {
        return null;
    }
}

function applyTheme(theme) {
    root.dataset.theme = theme;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    });
}

document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-theme-toggle]')) {
        return;
    }

    const theme = root.dataset.theme === 'dark' ? 'light' : 'dark';

    try {
        localStorage.setItem('theme', theme);
    } catch {
        // Storage can be unavailable (e.g. private browsing); the theme still applies for this page.
    }

    applyTheme(theme);
});

systemPrefersDark.addEventListener('change', (event) => {
    if (!storedTheme()) {
        applyTheme(event.matches ? 'dark' : 'light');
    }
});

applyTheme(root.dataset.theme ?? (systemPrefersDark.matches ? 'dark' : 'light'));

/**
 * Off-canvas sidebar on small screens.
 *
 * [data-sidebar-open] opens it; [data-sidebar-close] (the close button and the
 * backdrop) and the Escape key close it.
 */
function setSidebarOpen(isOpen) {
    document.body.classList.toggle('sidebar-open', isOpen);

    document.querySelectorAll('[data-sidebar-open]').forEach((button) => {
        button.setAttribute('aria-expanded', String(isOpen));
    });
}

document.addEventListener('click', (event) => {
    if (event.target.closest('[data-sidebar-open]')) {
        setSidebarOpen(true);
    } else if (event.target.closest('[data-sidebar-close]')) {
        setSidebarOpen(false);
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
        setSidebarOpen(false);
        document.querySelector('[data-sidebar-open]')?.focus();
    }
});

window.matchMedia('(min-width: 901px)').addEventListener('change', (event) => {
    if (event.matches) {
        setSidebarOpen(false);
    }
});
