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

