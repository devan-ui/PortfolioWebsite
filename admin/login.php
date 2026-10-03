<?php
/**
 * Administrator Login Page
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to dashboard
if (is_admin_logged_in()) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        $result = admin_login($identifier, $password);
        if ($result['success']) {
            set_flash('success', 'Welcome back, ' . e($_SESSION['admin_username']) . '!');
            header('Location: ' . url('admin/index.php'));
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Devanandan CS Portfolio</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body admin-login-page">
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="admin-login-header">
                <div class="admin-login-logo">
                    &lt;D/&gt;
                </div>
                <h1 class="admin-login-title">Admin Portal</h1>
                <p class="admin-login-subtitle">Sign in to manage portfolio content</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="admin-login-alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <?= render_flashes() ?>

            <form action="<?= url('admin/login.php') ?>" method="POST" class="admin-login-form" novalidate>
                <?= csrf_field() ?>

                <div class="admin-form-group">
                    <label for="identifier" class="admin-label">Username or Email</label>
                    <input type="text" name="identifier" id="identifier" class="admin-input" placeholder="admin or admin@devanandancs.dev" value="<?= e($_POST['identifier'] ?? '') ?>" required autofocus autocomplete="username">
                </div>

                <div class="admin-form-group">
                    <label for="password" class="admin-label">Password</label>
                    <div style="position: relative; width: 100%;">
                        <input type="password" name="password" id="password" class="admin-input" placeholder="••••••••••••" required autocomplete="current-password" style="padding-right: 2.75rem;">
                        <button type="button" id="toggle-password" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--admin-text-muted); cursor: pointer; padding: 0.25rem; display: flex; align-items: center; justify-content: center;" aria-label="Toggle password visibility">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary admin-login-btn">
                    <span>Authenticate</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </form>

            <div class="admin-login-footer">
                <a href="<?= url('') ?>" class="admin-login-back-link">
                    &larr; Return to Public Portfolio
                </a>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-password');
        const pwdInput = document.getElementById('password');
        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                toggleBtn.innerHTML = isPassword 
                    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'
                    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            });
        }
    </script>
</body>
</html>
