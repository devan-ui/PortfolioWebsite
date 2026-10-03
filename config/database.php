<?php
/**
 * Database Connection Handler using PDO
 * Devanandan CS Portfolio Website
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    /**
     * Get singleton PDO connection
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // If database doesn't exist, try connecting to host only to create it
                if ($e->getCode() == 1049) {
                    try {
                        $initDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
                        $initPdo = new PDO($initDsn, DB_USER, DB_PASS, $options);
                        $initPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                    } catch (PDOException $inner) {
                        self::handleError($inner->getMessage());
                    }
                } else {
                    self::handleError($e->getMessage());
                }
            }
        }
        return self::$instance;
    }

    /**
     * Render user-friendly error or throw exception based on APP_DEBUG
     */
    private static function handleError(string $msg): void {
        if (defined('APP_DEBUG') && APP_DEBUG) {
            die("<div style='font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:8px;max-width:700px;margin:50px auto;'>" .
                "<h3 style='margin-top:0;'>Database Connection Error</h3>" .
                "<p><strong>Message:</strong> " . htmlspecialchars($msg) . "</p>" .
                "<p>Please ensure MySQL is running in XAMPP and the database <code>" . DB_NAME . "</code> exists.</p>" .
                "</div>");
        } else {
            die("Database connection failed. Please contact the administrator.");
        }
    }
}

/**
 * Convenient global helper function to get PDO database instance
 */
function get_db(): PDO {
    return Database::getConnection();
}
