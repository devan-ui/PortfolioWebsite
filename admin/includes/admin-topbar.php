<?php
/**
 * Shared Admin Topbar Partial with User Profile Dropdown & Change Password Modal
 * Devanandan CS Portfolio Website
 */

$adminUsername = $_SESSION['admin_username'] ?? 'Admin';
$adminEmail = $_SESSION['admin_email'] ?? '';
$adminInitial = strtoupper(substr($adminUsername, 0, 1));
$topbarProfile = get_profile();
$topbarPhotoUrl = !empty($topbarProfile['profile_photo']) ? upload_url($topbarProfile['profile_photo'], 'profile') : '';
?>
<header class="admin-topbar">
    <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
        <button type="button" id="admin-sidebar-toggle" class="admin-sidebar-toggle" aria-label="Toggle Navigation Sidebar">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
        <h1 class="admin-topbar-title"><?= e($adminPageTitle ?? 'Admin Dashboard') ?></h1>
    </div>

    <div class="admin-topbar-actions">
        <a href="<?= url('') ?>" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm" title="Open portfolio in a new tab">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            <span class="admin-btn-text">View Public Site</span>
        </a>

        <!-- Admin Profile Dropdown -->
        <div class="admin-user-dropdown" id="admin-user-dropdown">
            <button type="button" class="admin-user-trigger" id="admin-user-trigger" aria-haspopup="true" aria-expanded="false" title="Account settings & actions">
                <div class="admin-user-avatar">
                    <?php if ($topbarPhotoUrl): ?>
                        <img src="<?= e($topbarPhotoUrl) ?>" alt="<?= e($adminUsername) ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <?= $adminInitial ?>
                    <?php endif; ?>
                </div>
                <div class="admin-user-meta">
                    <span class="admin-user-name"><?= e($adminUsername) ?></span>
                    <span class="admin-user-role">Administrator</span>
                </div>
                <svg class="admin-user-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div class="admin-dropdown-menu" id="admin-dropdown-menu" role="menu">
                <div class="admin-dropdown-header">
                    <span class="admin-dropdown-signed">Signed in as</span>
                    <strong class="admin-dropdown-username"><?= e($adminUsername) ?></strong>
                    <?php if (!empty($adminEmail)): ?>
                        <span class="admin-dropdown-email"><?= e($adminEmail) ?></span>
                    <?php endif; ?>
                </div>

                <div class="admin-dropdown-divider"></div>

                <ul class="admin-dropdown-list">
                    <li>
                        <button type="button" class="admin-dropdown-item" id="open-pwd-modal-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>Change Password</span>
                        </button>
                    </li>
                    <li>
                        <a href="<?= url('admin/profile.php') ?>" class="admin-dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Profile &amp; Resume</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= url('admin/settings.php') ?>" class="admin-dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>Site Settings</span>
                        </a>
                    </li>
                </ul>

                <div class="admin-dropdown-divider"></div>

                <div class="admin-dropdown-footer">
                    <a href="<?= url('admin/logout.php') ?>" class="admin-dropdown-item admin-dropdown-logout">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        <span>Log Out</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Change Password Modal -->
<div class="admin-modal-backdrop" id="pwd-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="pwd-modal-title">
    <div class="admin-modal-dialog">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title" id="pwd-modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--admin-primary);"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Change Password</span>
            </h3>
            <button type="button" class="admin-modal-close" id="close-pwd-modal-btn" aria-label="Close Modal">&times;</button>
        </div>

        <form id="topbar-change-pwd-form" action="<?= url('admin/change-password.php') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="ajax" value="1">

            <div class="admin-modal-body">
                <!-- Alert Box -->
                <div id="pwd-modal-alert" style="display: none; padding: 0.85rem 1rem; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1.25rem;"></div>

                <div class="admin-form-group">
                    <label for="modal_current_password" class="admin-label">Current Password <span style="color:#ef4444;">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="current_password" id="modal_current_password" class="admin-input" placeholder="Enter current password" required autocomplete="current-password" style="padding-right: 2.75rem;">
                        <button type="button" class="modal-pwd-toggle" data-target="modal_current_password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem;" aria-label="Toggle current password visibility">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label for="modal_new_password" class="admin-label">New Password <span style="color:#ef4444;">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="new_password" id="modal_new_password" class="admin-input" placeholder="At least 8 characters" minlength="8" required autocomplete="new-password" style="padding-right: 2.75rem;">
                        <button type="button" class="modal-pwd-toggle" data-target="modal_new_password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem;" aria-label="Toggle new password visibility">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                    <span class="admin-help-text">Minimum 8 characters with letters and numbers.</span>
                </div>

                <div class="admin-form-group" style="margin-bottom: 0;">
                    <label for="modal_confirm_password" class="admin-label">Confirm New Password <span style="color:#ef4444;">*</span></label>
                    <div style="position: relative;">
                        <input type="password" name="confirm_password" id="modal_confirm_password" class="admin-input" placeholder="Repeat new password" minlength="8" required autocomplete="new-password" style="padding-right: 2.75rem;">
                        <button type="button" class="modal-pwd-toggle" data-target="modal_confirm_password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem;" aria-label="Toggle confirm password visibility">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="admin-modal-footer">
                <button type="button" class="admin-btn admin-btn-secondary" id="cancel-pwd-modal-btn">
                    Cancel
                </button>
                <button type="submit" class="admin-btn admin-btn-primary" id="save-pwd-modal-btn">
                    <span>Save New Password</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Dropdown Toggle Logic
    const dropdownTrigger = document.getElementById('admin-user-trigger');
    const dropdownMenu = document.getElementById('admin-dropdown-menu');

    if (dropdownTrigger && dropdownMenu) {
        dropdownTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdownMenu.classList.contains('show');
            if (isOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });

        function openDropdown() {
            dropdownMenu.classList.add('show');
            dropdownTrigger.classList.add('active');
            dropdownTrigger.setAttribute('aria-expanded', 'true');
        }

        function closeDropdown() {
            dropdownMenu.classList.remove('show');
            dropdownTrigger.classList.remove('active');
            dropdownTrigger.setAttribute('aria-expanded', 'false');
        }

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!dropdownTrigger.contains(e.target) && !dropdownMenu.contains(e.target)) {
                closeDropdown();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDropdown();
                closePasswordModal();
            }
        });
    }

    // 2. Change Password Modal Logic
    const openModalBtn = document.getElementById('open-pwd-modal-btn');
    const modalBackdrop = document.getElementById('pwd-modal-backdrop');
    const closeModalBtn = document.getElementById('close-pwd-modal-btn');
    const cancelModalBtn = document.getElementById('cancel-pwd-modal-btn');
    const pwdForm = document.getElementById('topbar-change-pwd-form');
    const pwdAlert = document.getElementById('pwd-modal-alert');
    const saveBtn = document.getElementById('save-pwd-modal-btn');

    if (openModalBtn && modalBackdrop) {
        openModalBtn.addEventListener('click', () => {
            // Close dropdown first
            if (dropdownMenu) {
                dropdownMenu.classList.remove('show');
                dropdownTrigger.classList.remove('active');
            }
            openPasswordModal();
        });

        function openPasswordModal() {
            modalBackdrop.classList.add('show');
            if (pwdAlert) {
                pwdAlert.style.display = 'none';
                pwdAlert.textContent = '';
            }
            if (pwdForm) {
                pwdForm.reset();
            }
            setTimeout(() => {
                const cur = document.getElementById('modal_current_password');
                if (cur) cur.focus();
            }, 100);
        }

        function closePasswordModal() {
            if (modalBackdrop) {
                modalBackdrop.classList.remove('show');
            }
        }

        if (closeModalBtn) closeModalBtn.addEventListener('click', closePasswordModal);
        if (cancelModalBtn) cancelModalBtn.addEventListener('click', closePasswordModal);

        // Click outside dialog to close
        modalBackdrop.addEventListener('click', (e) => {
            if (e.target === modalBackdrop) {
                closePasswordModal();
            }
        });

        // Toggle password reveal in modal
        document.querySelectorAll('.modal-pwd-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    btn.innerHTML = isPassword
                        ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'
                        : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                }
            });
        });

        // AJAX Password Form Submit
        if (pwdForm) {
            pwdForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const cur = document.getElementById('modal_current_password').value;
                const np = document.getElementById('modal_new_password').value;
                const cp = document.getElementById('modal_confirm_password').value;

                if (!cur || !np || !cp) {
                    showAlert('Please fill in all password fields.', 'error');
                    return;
                }

                if (np.length < 8) {
                    showAlert('New password must be at least 8 characters long.', 'error');
                    return;
                }

                if (np !== cp) {
                    showAlert('New password and confirm password do not match.', 'error');
                    return;
                }

                const formData = new FormData(pwdForm);
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span>Updating...</span>';

                try {
                    const response = await fetch(pwdForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert(data.message || 'Password changed successfully!', 'success');
                        pwdForm.reset();
                        setTimeout(() => {
                            closePasswordModal();
                        }, 1200);
                    } else {
                        showAlert(data.error || 'Failed to change password. Please check your credentials.', 'error');
                    }
                } catch (err) {
                    showAlert('Connection error. Please try again.', 'error');
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<span>Save New Password</span>';
                }
            });
        }

        function showAlert(msg, type) {
            if (!pwdAlert) return;
            pwdAlert.style.display = 'block';
            pwdAlert.textContent = msg;
            if (type === 'success') {
                pwdAlert.style.backgroundColor = 'rgba(16, 185, 129, 0.15)';
                pwdAlert.style.border = '1px solid rgba(16, 185, 129, 0.3)';
                pwdAlert.style.color = '#34d399';
            } else {
                pwdAlert.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
                pwdAlert.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                pwdAlert.style.color = '#f87171';
            }
        }
    }
});
</script>
