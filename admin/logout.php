<?php
/**
 * Administrator Logout Handler
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

admin_logout();
set_flash('success', 'You have been successfully logged out.');
header('Location: ' . url('admin/login.php'));
exit;
