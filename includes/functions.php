<?php
/**
 * Global Helper Functions & Core Utilities
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// In-memory cache for settings and profile
$GLOBALS['_cached_settings'] = null;
$GLOBALS['_cached_profile'] = null;

/**
 * Escape HTML output for XSS prevention
 */
function e(?string $str): string {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate absolute URL for given path
 */
function url(string $path = ''): string {
    $path = '/' . ltrim($path, '/');
    return BASE_URL . $path;
}

/**
 * Generate asset URL with optional cache busting
 */
function asset(string $path): string {
    $cleanPath = '/' . ltrim($path, '/');
    $fullPath = ROOT_PATH . '/assets' . $cleanPath;
    $version = file_exists($fullPath) ? '?v=' . filemtime($fullPath) : '';
    return BASE_URL . '/assets' . $cleanPath . $version;
}

/**
 * Generate URL for uploaded files
 */
function upload_url(?string $filename, string $subfolder = ''): string {
    if (empty($filename)) {
        return '';
    }
    // If filename is already a full URL
    if (strpos($filename, 'http://') === 0 || strpos($filename, 'https://') === 0) {
        return $filename;
    }
    
    $cleanFile = ltrim(str_replace('\\', '/', $filename), '/');
    
    // Check if filename already includes uploads/
    if (strpos($cleanFile, 'uploads/') === 0) {
        return BASE_URL . '/' . $cleanFile;
    }
    
    $subfolder = trim(str_replace('\\', '/', $subfolder), '/');
    
    // If $cleanFile already starts with $subfolder . '/', avoid duplicating the subfolder
    if ($subfolder !== '' && strpos($cleanFile, $subfolder . '/') === 0) {
        return BASE_URL . '/uploads/' . $cleanFile;
    }
    
    // Check if $cleanFile already starts with any standard upload folder
    foreach (['projects', 'profile', 'resumes'] as $folder) {
        if (strpos($cleanFile, $folder . '/') === 0) {
            return BASE_URL . '/uploads/' . $cleanFile;
        }
    }
    
    $subfolderPart = $subfolder ? '/' . $subfolder : '';
    return BASE_URL . '/uploads' . $subfolderPart . '/' . $cleanFile;
}

/**
 * Generate and return CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render hidden CSRF form input field
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token from POST request
 */
function verify_csrf_token(?string $token = null): bool {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    }
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return !empty($token) && !empty($sessionToken) && hash_equals($sessionToken, $token);
}

/**
 * Retrieve site setting by key
 */
function get_setting(string $key, string $default = ''): string {
    if ($GLOBALS['_cached_settings'] === null) {
        $GLOBALS['_cached_settings'] = [];
        try {
            $db = get_db();
            $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
            while ($row = $stmt->fetch()) {
                $GLOBALS['_cached_settings'][$row['setting_key']] = $row['setting_value'];
            }
        } catch (Exception $e) {
            // Silently fall back to default if database is empty
        }
    }
    return $GLOBALS['_cached_settings'][$key] ?? $default;
}

/**
 * Retrieve personal profile data
 */
function get_profile(): array {
    if ($GLOBALS['_cached_profile'] === null) {
        try {
            $db = get_db();
            $stmt = $db->query("SELECT * FROM profile ORDER BY id ASC LIMIT 1");
            $data = $stmt->fetch();
            $GLOBALS['_cached_profile'] = $data ?: [];
        } catch (Exception $e) {
            $GLOBALS['_cached_profile'] = [];
        }
    }
    return $GLOBALS['_cached_profile'];
}

/**
 * Set flash alert message
 */
function set_flash(string $type, string $message): void {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear flash alert messages
 */
function get_flashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/**
 * Render flash messages HTML
 */
function render_flashes(): string {
    $flashes = get_flashes();
    if (empty($flashes)) {
        return '';
    }
    $html = '<div class="flash-messages-container" id="flash-container">';
    foreach ($flashes as $flash) {
        $type = e($flash['type']);
        $msg = e($flash['message']);
        $html .= "<div class='flash-message flash-{$type}' role='alert'>" .
                 "<div class='flash-content'>{$msg}</div>" .
                 "<button type='button' class='flash-close' onclick='this.parentElement.remove()'>&times;</button>" .
                 "</div>";
    }
    $html .= '</div>';
    return $html;
}

/**
 * Generate clean URL slug from string
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Safe File Upload Handler
 * Whitelists mime types, checks file sizes, assigns cryptographically random filenames
 */
function upload_file(array $file, string $targetSubdir = 'projects', array $allowedMimes = ALLOWED_IMAGE_TYPES, array $allowedExts = ALLOWED_IMAGE_EXTS, int $maxSize = MAX_FILE_SIZE): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameters.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize directive on server.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE specified in the form.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk (check server write permissions).',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.'
        ];
        return ['success' => false, 'error' => $errors[$file['error']] ?? 'Upload error code: ' . $file['error']];
    }

    if ($file['size'] > $maxSize) {
        $mb = round($maxSize / (1024 * 1024), 1);
        return ['success' => false, 'error' => "File size cannot exceed {$mb}MB."];
    }

    // Check extension
    $originalName = $file['name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        return ['success' => false, 'error' => 'Invalid file extension: .' . htmlspecialchars($ext)];
    }

    // Safe MIME type checking (prevents fatal error if finfo is disabled on free hosting)
    $mime = null;
    if (class_exists('finfo')) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = @$finfo->file($file['tmp_name']);
        } catch (\Throwable $e) {
            $mime = null;
        }
    }
    if (!$mime && function_exists('mime_content_type')) {
        $mime = @mime_content_type($file['tmp_name']);
    }
    if (!$mime && !empty($file['type'])) {
        $mime = $file['type'];
    }

    // Validate MIME type against whitelist if detected
    if ($mime && !in_array($mime, $allowedMimes, true)) {
        // Fallback check for images if mime reported generically (e.g. application/octet-stream)
        $isImageExt = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif'], true);
        $imgCheck = $isImageExt ? @getimagesize($file['tmp_name']) : false;
        if (!$imgCheck && $ext !== 'svg' && $ext !== 'pdf') {
            return ['success' => false, 'error' => 'Invalid file MIME type: ' . htmlspecialchars($mime)];
        }
    }

    // Ensure uploads root directory exists
    if (!is_dir(UPLOADS_PATH)) {
        @mkdir(UPLOADS_PATH, 0777, true);
    }
    if (!is_writable(UPLOADS_PATH)) {
        @chmod(UPLOADS_PATH, 0777);
    }

    // Target subdirectory
    $cleanSubdir = trim(str_replace('\\', '/', $targetSubdir), '/');
    $uploadDir = UPLOADS_PATH . ($cleanSubdir ? '/' . $cleanSubdir : '');
    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'error' => "Cannot create directory 'uploads/{$cleanSubdir}'. Please check write permissions on your hosting (chmod 755 or 777 on uploads folder)."];
        }
    }
    if (!is_writable($uploadDir)) {
        @chmod($uploadDir, 0777);
    }

    // Secure, randomized unique filename
    $safeFilename = bin2hex(random_bytes(10)) . '_' . time() . '.' . $ext;
    $targetPath = $uploadDir . '/' . $safeFilename;

    if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'error' => "Failed to save uploaded file to 'uploads/{$cleanSubdir}'. Please ensure uploads folder has write permissions (chmod 755 or 777)."];
    }

    // Store relative path from uploads directory
    $storedPath = ($cleanSubdir ? $cleanSubdir . '/' : '') . $safeFilename;
    return [
        'success' => true,
        'filename' => $safeFilename,
        'relative_path' => $storedPath,
        'full_path' => $targetPath
    ];
}

/**
 * Determine if current page matches given path/identifier
 */
function is_active_page(string $page): bool {
    $script = basename($_SERVER['PHP_SELF'] ?? '');
    return strcasecmp($script, $page) === 0 || strcasecmp($script, $page . '.php') === 0;
}

/**
 * Render proficiency badge with semantic color classes
 */
function render_proficiency_badge(string $level): string {
    $level = ucfirst(strtolower($level));
    $class = match($level) {
        'Advanced' => 'badge-advanced',
        'Intermediate' => 'badge-intermediate',
        default => 'badge-beginner'
    };
    return '<span class="proficiency-badge ' . $class . '">' . e($level) . '</span>';
}

/**
 * Format date string safely
 */
function format_display_date(?string $date): string {
    if (empty($date)) return '';
    if (in_array(strtolower($date), ['present', 'current', 'no expiration', 'graduated', 'pursuing'])) {
        return e(ucwords($date));
    }
    $time = strtotime($date);
    return $time ? date('M Y', $time) : e($date);
}
