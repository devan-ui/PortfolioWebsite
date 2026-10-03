/**
 * Frontend Interactive Logic & Enhancements
 * Devanandan CS Portfolio Website
 */

document.addEventListener('DOMContentLoaded', () => {
    // --- 1. Theme Management (Light / Dark Mode) ---
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const rootElement = document.documentElement;
    const storedTheme = localStorage.getItem('theme_preference');
    
    // Check saved preference or system preference
    if (storedTheme) {
        rootElement.setAttribute('data-theme', storedTheme);
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
        rootElement.setAttribute('data-theme', 'light');
    }

    updateThemeButtonIcon();

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = rootElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            rootElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme_preference', nextTheme);
            updateThemeButtonIcon();
        });
    }

    function updateThemeButtonIcon() {
        if (!themeToggleBtn) return;
        const current = rootElement.getAttribute('data-theme');
        if (current === 'light') {
            themeToggleBtn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            `;
            themeToggleBtn.setAttribute('aria-label', 'Switch to Dark Mode');
        } else {
            themeToggleBtn.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            `;
            themeToggleBtn.setAttribute('aria-label', 'Switch to Light Mode');
        }
    }

    // --- 2. Responsive Mobile Navigation & Dropdown Management ---
    const mobileToggle = document.getElementById('mobile-toggle');
    const navMenuContainer = document.getElementById('nav-menu-container');
    const navBackdrop = document.getElementById('nav-backdrop');
    const dropdownItem = document.getElementById('nav-dropdown-item');
    const dropdownBtn = document.getElementById('nav-dropdown-btn');
    const dropdownMenu = document.getElementById('nav-dropdown-menu');

    function closeMobileMenu() {
        if (!mobileToggle || !navMenuContainer) return;
        mobileToggle.setAttribute('aria-expanded', 'false');
        mobileToggle.classList.remove('is-active');
        navMenuContainer.classList.remove('show');
        if (navBackdrop) navBackdrop.classList.remove('show');
        document.body.classList.remove('menu-open');
    }

    function openMobileMenu() {
        if (!mobileToggle || !navMenuContainer) return;
        mobileToggle.setAttribute('aria-expanded', 'true');
        mobileToggle.classList.add('is-active');
        navMenuContainer.classList.add('show');
        if (navBackdrop) navBackdrop.classList.add('show');
        document.body.classList.add('menu-open');
    }

    function toggleMobileMenu() {
        if (!mobileToggle || !navMenuContainer) return;
        const isOpen = navMenuContainer.classList.contains('show');
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    if (mobileToggle && navMenuContainer) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileMenu();
        });

        // Close on backdrop tap
        if (navBackdrop) {
            navBackdrop.addEventListener('click', () => {
                closeMobileMenu();
                closeDropdown();
            });
        }

        // Close when clicking outside of navbar
        document.addEventListener('click', (e) => {
            const isClickInsideMenu = navMenuContainer.contains(e.target);
            const isClickOnToggle = mobileToggle.contains(e.target);
            if (!isClickInsideMenu && !isClickOnToggle) {
                closeMobileMenu();
            }
        });

        // Close mobile drawer when any link is clicked
        const navLinksList = navMenuContainer.querySelectorAll('a');
        navLinksList.forEach(link => {
            link.addEventListener('click', () => {
                closeMobileMenu();
            });
        });
    }

    // --- Interactive "More" Dropdown Toggle (Desktop & Mobile) ---
    function toggleDropdown(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (!dropdownItem || !dropdownBtn) return;
        const isCurrentlyOpen = dropdownItem.classList.contains('open');
        if (isCurrentlyOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    }

    function openDropdown() {
        if (!dropdownItem || !dropdownBtn) return;
        dropdownItem.classList.add('open');
        dropdownBtn.setAttribute('aria-expanded', 'true');
        if (dropdownMenu) dropdownMenu.classList.add('show');
    }

    function closeDropdown() {
        if (!dropdownItem || !dropdownBtn) return;
        dropdownItem.classList.remove('open');
        dropdownBtn.setAttribute('aria-expanded', 'false');
        if (dropdownMenu) dropdownMenu.classList.remove('show');
    }

    if (dropdownBtn && dropdownItem) {
        dropdownBtn.addEventListener('click', toggleDropdown);

        // Close dropdown when clicking outside on desktop
        document.addEventListener('click', (e) => {
            if (!dropdownItem.contains(e.target)) {
                closeDropdown();
            }
        });
    }

    // --- Keyboard Accessibility & Window Resize Handler ---
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeDropdown();
            closeMobileMenu();
        }
    });

    // Reset mobile menu states if resized back to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 991) {
            closeMobileMenu();
        }
    });

    // --- 3. Interactive Skill / Project Filter Buttons ---
    const filterButtons = document.querySelectorAll('[data-filter]');
    const filterItems = document.querySelectorAll('[data-category]');

    if (filterButtons.length > 0 && filterItems.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetCategory = btn.getAttribute('data-filter');

                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                filterItems.forEach(item => {
                    const itemCat = item.getAttribute('data-category');
                    if (targetCategory === 'all' || itemCat === targetCategory) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // --- 4. Auto-dismiss Flash Alerts ---
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.4s ease';
            setTimeout(() => msg.remove(), 400);
        }, 5500);
    });

    // --- 5. Copy Email to Clipboard Helper ---
    const copyBtns = document.querySelectorAll('[data-copy]');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const textToCopy = btn.getAttribute('data-copy');
            navigator.clipboard.writeText(textToCopy).then(() => {
                const orig = btn.innerText;
                btn.innerText = 'Copied!';
                setTimeout(() => {
                    btn.innerText = orig;
                }, 2000);
            });
        });
    });
});
