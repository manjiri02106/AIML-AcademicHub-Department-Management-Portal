<?php
/**
 * Database Connection & Auto-Setup Configuration
 * Module: Projects & Internships
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'academichub_db');

// Enable mysqli error reporting off for clean error handling
mysqli_report(MYSQLI_REPORT_OFF);

// Attempt database connection
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die("<div style='padding: 24px; font-family: system-ui, sans-serif; background: #fee2e2; color: #991b1b; border-radius: 12px; margin: 30px auto; max-width: 650px; border: 1px solid #fca5a5; box-shadow: 0 10px 25px rgba(0,0,0,0.1);'>
            <h3 style='margin-bottom: 8px;'><i class='fas fa-exclamation-triangle'></i> Database Connection Failed</h3>
            <p style='margin-bottom: 8px;'>Could not connect to XAMPP MySQL server at <code>" . DB_HOST . "</code>.</p>
            <p style='font-size: 13px; color: #b91c1c;'>Please verify that <strong>Apache & MySQL</strong> are started in the XAMPP Control Panel.</p>
         </div>");
}

// Create database if not exists
$create_db_sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
mysqli_query($conn, $create_db_sql);
mysqli_select_db($conn, DB_NAME);
mysqli_set_charset($conn, "utf8mb4");

// Auto-seed schema and data if tables do not exist
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'students'");
if (!$check_table || mysqli_num_rows($check_table) === 0) {
    $sql_file = __DIR__ . '/../database/academichub.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        // Remove USE statement to avoid issues
        $sql_content = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql_content);
        $sql_content = preg_replace('/USE [^;]+;/i', '', $sql_content);

        if (mysqli_multi_query($conn, $sql_content)) {
            do {
                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_more_results($conn) && mysqli_next_result($conn));
        }
    }
}

return $conn;
?>
