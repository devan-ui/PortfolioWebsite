/**
 * Admin Panel Interactive Utilities
 * Devanandan CS Portfolio Website
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Auto-slug generation from Title input
    const titleInput = document.getElementById('title-input');
    const slugInput = document.getElementById('slug-input');

    if (titleInput && slugInput && !slugInput.dataset.manualEdit) {
        titleInput.addEventListener('input', () => {
            if (!slugInput.dataset.manualEdit) {
                slugInput.value = generateSlug(titleInput.value);
            }
        });

        slugInput.addEventListener('input', () => {
            if (slugInput.value.trim() !== '') {
                slugInput.dataset.manualEdit = 'true';
            } else {
                delete slugInput.dataset.manualEdit;
            }
        });
    }

    function generateSlug(str) {
        return str
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // 2. Real-time Image Upload Previews
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach(input => {
        const previewTargetId = input.getAttribute('data-preview');
        const previewTarget = document.getElementById(previewTargetId);

        if (previewTarget) {
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewTarget.innerHTML = `<img src="${e.target.result}" alt="Selected Preview" style="width:100%;height:100%;object-fit:cover;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // 3. Confirm Delete Prompts
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const message = btn.getAttribute('data-confirm') || 'Are you sure you want to delete this item? This action cannot be undone.';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // 4. Mobile Sidebar Toggler & Backdrop
    const sidebarToggleBtn = document.getElementById('admin-sidebar-toggle');
    const sidebarCloseBtn = document.getElementById('admin-sidebar-close');
    const sidebar = document.getElementById('admin-sidebar') || document.querySelector('.admin-sidebar');
    const backdrop = document.getElementById('admin-sidebar-backdrop');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('active');
        document.body.classList.add('admin-sidebar-locked');
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
        document.body.classList.remove('admin-sidebar-locked');
    }

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeSidebar();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Close sidebar on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    // Close on mobile when navigating links in sidebar
    const navLinks = document.querySelectorAll('.admin-sidebar-nav a, .admin-sidebar-footer a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 900) {
                closeSidebar();
            }
        });
    });
});
