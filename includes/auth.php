<?php
/**
 * Authentication and Authorization Guard
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/functions.php';

/**
 * Check if an administrator is currently authenticated
 */
function is_admin_logged_in(): bool {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    
    // Check session timeout (2 hours of inactivity)
    $timeout = 7200;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        admin_logout();
        return false;
    }
    
    // Update activity timestamp
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Require administrator authentication for protected routes
 */
function require_admin(): void {
    if (!is_admin_logged_in()) {
        set_flash('error', 'Please log in to access the administration panel.');
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

/**
 * Attempt admin login with username or email and plaintext password
 */
function admin_login(string $identifier, string $password): array {
    $identifier = trim($identifier);
    if (empty($identifier) || empty($password)) {
        return ['success' => false, 'error' => 'Please provide both username/email and password.'];
    }

    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = :u_ident OR email = :e_ident LIMIT 1");
        $stmt->execute(['u_ident' => $identifier, 'e_ident' => $identifier]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid login credentials.'];
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['last_activity'] = time();

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Database authentication error.'];
    }
}

/**
 * Log out administrator and terminate session
 */
function admin_logout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Update admin password securely
 */
function admin_change_password(int $adminId, string $currentPassword, string $newPassword): array {
    if (strlen($newPassword) < 8) {
        return ['success' => false, 'error' => 'New password must be at least 8 characters long.'];
    }

    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT password_hash FROM admins WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $adminId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($currentPassword, $row['password_hash'])) {
            return ['success' => false, 'error' => 'Current password is incorrect.'];
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare("UPDATE admins SET password_hash = :hash WHERE id = :id");
        $updateStmt->execute(['hash' => $newHash, 'id' => $adminId]);

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Failed to update password in database.'];
    }
}
