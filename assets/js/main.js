document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss flash alerts
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) closeBtn.click();
        }, 5000);
    });

    // Highlight active nav link
    const path = window.location.pathname;
    document.querySelectorAll('.app-navbar .nav-link[href]').forEach(function (link) {
        const href = link.getAttribute('href');
        if (!href || href === '#' || href.includes('lang=')) return;
        try {
            const linkPath = new URL(link.href).pathname;
            if (path === linkPath || (linkPath.length > 1 && path.startsWith(linkPath.replace(/\/$/, '')))) {
                link.classList.add('active');
            }
        } catch (e) { /* ignore */ }
    });

    // Subtle scroll reveal for cards
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });

        document.querySelectorAll('.card, .feature-card, .stat-card').forEach(function (el) {
            el.style.opacity = '0';
            observer.observe(el);
        });
    } else {
        document.querySelectorAll('.card, .feature-card, .stat-card').forEach(function (el) {
            el.classList.add('animate-in');
        });
    }
});
