/**
 * Main App - Sistem Rekomendasi Kost
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    Modal.init();
});

function initNavbar() {
    const navbar = document.querySelector('.navbar');
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    // Scroll effect
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar?.classList.add('scrolled');
            navbar?.classList.remove('transparent');
        } else if (navbar?.dataset.transparent === 'true') {
            navbar?.classList.remove('scrolled');
            navbar?.classList.add('transparent');
        } else {
            navbar?.classList.remove('scrolled');
        }
    });

    // Mobile toggle
    navToggle?.addEventListener('click', () => {
        navToggle.classList.toggle('active');
        navMenu?.classList.toggle('active');
    });

    // Close menu on link click
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navToggle?.classList.remove('active');
            navMenu?.classList.remove('active');
        });
    });
}

// Update nav based on auth
async function updateNavAuth() {
    const user = await checkAuth();
    const authNav = document.getElementById('auth-nav');

    if (authNav) {
        if (user) {
            authNav.innerHTML = `
                <span class="nav-user">Hi, ${escapeHtml(user.nama)}</span>
                ${user.role === 'admin' ? '<a href="/RekomendasiKost/pages/admin/dashboard.html" class="nav-link">Admin</a>' : ''}
                <a href="#" class="nav-link" onclick="logout(); return false;">Logout</a>
            `;
        } else {
            authNav.innerHTML = '<a href="/RekomendasiKost/pages/login.html" class="btn btn-primary btn-sm">Login</a>';
        }
    }
}

async function logout() {
    try {
        await AuthAPI.logout();
        showToast('Logged out', 'success');
        window.location.href = '/RekomendasiKost/';
    } catch (e) {
        showToast(e.message, 'error');
    }
}

window.updateNavAuth = updateNavAuth;
window.logout = logout;
