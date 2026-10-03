<?php
/**
 * One-Click Database & System Installer
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/config/config.php';

$message = '';
$error = '';
$installed = false;

// Check extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

// Perform Installation if requested
if ((isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') || (php_sapi_name() === 'cli')) {
    if (!empty($missingExtensions)) {
        $error = 'Missing required PHP extensions: ' . implode(', ', $missingExtensions);
    } else {
        try {
            // 1. Connect to MySQL server (without selecting DB first)
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ]);

            // 2. Create Database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . DB_NAME . "`");

            // 3. Execute Schema
            $schemaFile = __DIR__ . '/database/schema.sql';
            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                $pdo->exec($schemaSql);
            }

            // 4. Check if tables are seeded
            $count = (int)$pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
            if ($count === 0) {
                $seedFile = __DIR__ . '/database/seed.sql';
                if (file_exists($seedFile)) {
                    $seedSql = file_get_contents($seedFile);
                    $pdo->exec($seedSql);
                }
            }

            // 5. Ensure uploads directories exist
            $uploadDirs = [
                UPLOADS_PATH,
                UPLOADS_PATH . '/projects',
                UPLOADS_PATH . '/profile',
                UPLOADS_PATH . '/resumes'
            ];
            foreach ($uploadDirs as $dir) {
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
            }

            $installed = true;
            $message = 'Database and portfolio application have been successfully initialized!';

            if (php_sapi_name() === 'cli') {
                echo "SUCCESS: " . $message . PHP_EOL;
                exit(0);
            }
        } catch (Exception $e) {
            $error = 'Installation failed: ' . $e->getMessage();
            if (php_sapi_name() === 'cli') {
                echo "ERROR: " . $error . PHP_EOL;
                exit(1);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup &amp; Installer — Devanandan CS</title>
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-login-wrapper">
        <div class="admin-login-card" style="max-width: 560px;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 12px; background: #3b82f6; color: #fff; font-size: 1.2rem; font-weight: 800; margin-bottom: 1rem;">
                    &lt;D/&gt;
                </div>
                <h1 style="font-size: 1.6rem; font-weight: 700; color: #fff; margin: 0 0 0.5rem 0;">Portfolio Database Setup</h1>
                <p style="color: var(--admin-text-muted); font-size: 0.9rem; margin: 0;">Devanandan CS — Antigravity Portfolio Build</p>
            </div>

            <?php if (!empty($missingExtensions)): ?>
                <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <strong>Missing PHP Extensions:</strong> <?= htmlspecialchars(implode(', ', $missingExtensions)) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($installed): ?>
                <div style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.95rem;">
                    <strong>Setup Complete!</strong> <?= htmlspecialchars($message) ?>
                </div>

                <div style="background: var(--admin-bg); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border); margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <strong style="color: #fff;">Default Admin Login:</strong><br>
                    <span style="color: var(--admin-text-muted);">Username / Email:</span> <code>admin@devanandancs.dev</code> or <code>admin</code><br>
                    <span style="color: var(--admin-text-muted);">Password:</span> <code>Admin@Devanandan2026!</code>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <a href="index.php" class="admin-btn admin-btn-primary" style="flex: 1;">View Public Portfolio &rarr;</a>
                    <a href="admin/login.php" class="admin-btn admin-btn-secondary" style="flex: 1;">Open Admin Panel &rarr;</a>
                </div>
            <?php else: ?>
                <div style="background: var(--admin-bg); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border); margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <p style="margin: 0 0 0.5rem 0; color: #fff;"><strong>Database Connection Info:</strong></p>
                    <div style="color: var(--admin-text-muted); line-height: 1.7;">
                        Host: <code><?= DB_HOST ?>:<?= DB_PORT ?></code><br>
                        Database: <code><?= DB_NAME ?></code><br>
                        User: <code><?= DB_USER ?></code>
                    </div>
                </div>

                <form method="POST">
                    <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
                        Initialize Database &amp; Seed Data &rarr;
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
