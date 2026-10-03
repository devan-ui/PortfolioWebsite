<?php
/**
 * Admin Change Password Page & Endpoint
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (!empty($_POST['ajax']))
          || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$adminId = (int)($_SESSION['admin_id'] ?? 0);
$errors = [];
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $err = 'Invalid security token. Please refresh and try again.';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $err]);
            exit;
        }
        $errors[] = $err;
    } else {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($currentPass)) {
            $errors[] = 'Current password is required.';
        }
        if (empty($newPass)) {
            $errors[] = 'New password is required.';
        } elseif (strlen($newPass) < 8) {
            $errors[] = 'New password must be at least 8 characters long.';
        }
        if ($newPass !== $confirmPass) {
            $errors[] = 'New password and confirmation do not match.';
        }

        if (empty($errors)) {
            $result = admin_change_password($adminId, $currentPass, $newPass);
            if ($result['success']) {
                $msg = 'Password changed successfully!';
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $msg]);
                    exit;
                }
                set_flash('success', $msg);
                header('Location: ' . url('admin/change-password.php'));
                exit;
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $result['error']]);
                    exit;
                }
                $errors[] = $result['error'];
            }
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => implode(' ', $errors)]);
                exit;
            }
        }
    }
}

$adminPageTitle = 'Change Password';
require_once __DIR__ . '/includes/admin-header.php';
?>

<?php include __DIR__ . '/includes/admin-sidebar.php'; ?>

<div class="admin-main-wrapper">
    <?php include __DIR__ . '/includes/admin-topbar.php'; ?>

    <main class="admin-content">
        <?= render_flashes() ?>

        <div style="max-width: 680px; margin: 0 auto;">
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 1.6rem; font-weight: 700; color: #fff; margin: 0 0 0.5rem 0;">Account Security</h1>
                <p style="color: var(--admin-text-muted); font-size: 0.95rem; margin: 0;">
                    Update your administrator password to keep your portfolio CMS secure.
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        <?php foreach ($errors as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h2 class="admin-card-title">Change Password</h2>
                </div>
                <div class="admin-card-body">
                    <form action="<?= url('admin/change-password.php') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="admin-form-group">
                            <label for="current_password" class="admin-label">Current Password <span style="color:#ef4444;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" name="current_password" id="current_password" class="admin-input" placeholder="Enter current password" required autocomplete="current-password" style="padding-right: 2.75rem;">
                                <button type="button" class="pwd-toggle" data-target="current_password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem;" aria-label="Toggle password visibility">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <div class="admin-form-group">
                            <label for="new_password" class="admin-label">New Password <span style="color:#ef4444;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" name="new_password" id="new_password" class="admin-input" placeholder="At least 8 characters" minlength="8" required autocomplete="new-password" style="padding-right: 2.75rem;">
                                <button type="button" class="pwd-toggle" data-target="new_password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem;" aria-label="Toggle password visibility">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                            <span class="admin-help-text">Must be at least 8 characters long. Mix letters, numbers, and symbols for better security.</span>
                        </div>

                        <div class="admin-form-group">
                            <label for="confirm_password" class="admin-label">Confirm New Password <span style="color:#ef4444;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" name="confirm_password" id="confirm_password" class="admin-input" placeholder="Repeat new password" minlength="8" required autocomplete="new-password" style="padding-right: 2.75rem;">
                                <button type="button" class="pwd-toggle" data-target="confirm_password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem;" aria-label="Toggle password visibility">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                            <button type="submit" class="admin-btn admin-btn-primary" style="padding: 0.85rem 2rem;">
                                Update Password
                            </button>
                            <a href="<?= url('admin/index.php') ?>" class="admin-btn admin-btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.querySelectorAll('.pwd-toggle').forEach(btn => {
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
    </script>

<?php include __DIR__ . '/includes/admin-footer.php'; ?>
