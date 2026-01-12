/**
 * Utility Functions - Sistem Rekomendasi Kost
 */

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(Number(amount));
}

function formatDistance(km) {
    km = parseFloat(km);
    if (isNaN(km)) return '0 m';
    return km < 1 ? `${Math.round(km * 1000)} m` : `${km.toFixed(1)} km`;
}

function formatNumber(value, decimals = 2) {
    return parseFloat(value).toFixed(decimals);
}

function formatPercent(value) {
    return `${(value * 100).toFixed(1)}%`;
}

function showLoading(message = 'Loading...') {
    if (document.querySelector('.loader-overlay')) return;
    const loader = document.createElement('div');
    loader.className = 'loader-overlay';
    loader.innerHTML = `<div class="spinner"></div><p class="loader-text">${message}</p>`;
    document.body.appendChild(loader);
}

function hideLoading() {
    const loader = document.querySelector('.loader-overlay');
    if (loader) loader.remove();
}

function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

function getQueryParams() {
    return Object.fromEntries(new URLSearchParams(window.location.search).entries());
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getPlaceholderImage(text = 'Kost', size = 400) {
    return `https://placehold.co/${size}x${size}/667eea/ffffff?text=${encodeURIComponent(text)}`;
}

function getRatingStars(rating, max = 5) {
    return Array(max).fill(0).map((_, i) =>
        i < rating ? '<span class="star filled">★</span>' : '<span class="star">☆</span>'
    ).join('');
}

function getRankBadgeClass(rank) {
    return rank === 1 ? 'gold' : rank === 2 ? 'silver' : rank === 3 ? 'bronze' : '';
}

const Modal = {
    open(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.add('active'); document.body.style.overflow = 'hidden'; }
    },
    close(id) {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('active'); document.body.style.overflow = ''; }
    },
    init() {
        document.querySelectorAll('.modal-overlay').forEach(o => {
            o.addEventListener('click', e => { if (e.target === o) this.close(o.id); });
        });
        document.querySelectorAll('.modal-close').forEach(b => {
            b.addEventListener('click', () => { const m = b.closest('.modal-overlay'); if (m) this.close(m.id); });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { const m = document.querySelector('.modal-overlay.active'); if (m) this.close(m.id); }
        });
    }
};

async function checkAuth() {
    try { return await AuthAPI.me(); } catch { return null; }
}

// Export
window.formatCurrency = formatCurrency;
window.formatDistance = formatDistance;
window.formatNumber = formatNumber;
window.formatPercent = formatPercent;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.showToast = showToast;
window.debounce = debounce;
window.getQueryParams = getQueryParams;
window.escapeHtml = escapeHtml;
window.getPlaceholderImage = getPlaceholderImage;
window.getRatingStars = getRatingStars;
window.getRankBadgeClass = getRankBadgeClass;
window.Modal = Modal;
window.checkAuth = checkAuth;
