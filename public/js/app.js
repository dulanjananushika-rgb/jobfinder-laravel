/**
 * JobFinder.lk - Next-Gen Interactive Controls
 * Handles mobile drawer, notification popovers, and interactive micro-animations
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Drawer
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const mobileBackdrop = document.getElementById('mobileBackdrop');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');

    function openMobileMenu() {
        if (mobileDrawer && mobileBackdrop) {
            mobileDrawer.classList.add('open');
            mobileBackdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeMobileMenu() {
        if (mobileDrawer && mobileBackdrop) {
            mobileDrawer.classList.remove('open');
            mobileBackdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (mobileBtn) mobileBtn.addEventListener('click', openMobileMenu);
    if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeMobileMenu);
    if (mobileBackdrop) mobileBackdrop.addEventListener('click', closeMobileMenu);

    // 2. In-App Notifications Dropdown
    const notifBtn = document.getElementById('notifBellBtn');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                notifDropdown.classList.remove('show');
            }
        });
    }

    // 3. Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    });
});
