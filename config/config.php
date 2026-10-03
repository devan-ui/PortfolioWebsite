<?php
/**
 * Application Configuration
 * Devanandan CS Portfolio Website
 */

// Prevent multiple inclusions
if (defined('APP_INIT')) {
    return;
}
define('APP_INIT', true);

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    // Session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Environment & Debugging (Set to false in production)
define('APP_DEBUG', true);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database Credentials
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'devanandan_portfolio');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

// Filesystem Paths
$detectedRoot = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
$detectedRoot = str_replace('\\', '/', $detectedRoot);
define('ROOT_PATH', $detectedRoot);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Dynamic Base URL Detection
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Determine script directory relative to document root
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
// Normalize admin subdirectory if the current script is in /admin
if (substr($scriptDir, -6) === '/admin') {
    $basePath = substr($scriptDir, 0, -6);
} else {
    $basePath = $scriptDir;
}
$basePath = rtrim($basePath, '/');
if ($basePath === '.' || $basePath === '/') {
    $basePath = '';
}

define('BASE_PATH', $basePath);
define('BASE_URL', $protocol . $host . $basePath);

// Uploads configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp', 'image/svg+xml', 'image/avif']);
define('ALLOWED_IMAGE_EXTS', ['jpg', 'jpeg', 'png', 'webp', 'svg', 'avif']);
define('ALLOWED_DOC_TYPES', ['application/pdf']);
define('ALLOWED_DOC_EXTS', ['pdf']);
