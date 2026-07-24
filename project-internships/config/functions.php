<?php
/**
 * Global Helper Functions & Safe Query Utility
 * Module: Projects & Internships
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

/**
 * Input sanitization helper
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Safe query runner helper
 */
function safe_query($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if ($res instanceof mysqli_result) {
        return $res;
    }
    return false;
}

/**
 * Safe fetch single assoc row
 */
function safe_fetch_assoc($conn, $sql) {
    $res = safe_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}

/**
 * Generate CSRF Token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        die("CSRF Token verification failed.");
    }
    return true;
}

/**
 * Set Flash Message
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Display Flash Message HTML
 */
function display_flash_message() {
    if (isset($_SESSION['flash_msg'])) {
        $type = $_SESSION['flash_msg']['type'];
        $msg = $_SESSION['flash_msg']['message'];
        unset($_SESSION['flash_msg']);

        $bg_color = '#e0f2fe';
        $border_color = '#7dd3fc';
        $text_color = '#0369a1';
        $icon = 'fa-info-circle';

        if ($type === 'success') {
            $bg_color = '#dcfce7';
            $border_color = '#86efac';
            $text_color = '#15803d';
            $icon = 'fa-check-circle';
        } else if ($type === 'error') {
            $bg_color = '#fee2e2';
            $border_color = '#fca5a5';
            $text_color = '#b91c1c';
            $icon = 'fa-exclamation-triangle';
        } else if ($type === 'warning') {
            $bg_color = '#fef3c7';
            $border_color = '#fde047';
            $text_color = '#b45309';
            $icon = 'fa-exclamation-circle';
        }

        echo "<div class='alert-toast' style='background: {$bg_color}; border: 1px solid {$border_color}; color: {$text_color}; padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; font-weight: 600; box-shadow: 0 8px 20px rgba(0,0,0,0.04); backdrop-filter: blur(8px);'>
                <div style='display: flex; align-items: center; gap: 10px;'><i class='fas {$icon}' style='font-size: 18px;'></i> <span>{$msg}</span></div>
                <button type='button' onclick='this.parentElement.style.display=\"none\"' style='background: none; border: none; font-size: 18px; color: {$text_color}; cursor: pointer;'>&times;</button>
              </div>";
    }
}

/**
 * Status Badge HTML Generator with advanced badges
 */
function render_status_badge($status) {
    $status = trim((string)$status);
    switch (strtolower($status)) {
        case 'completed':
            return "<span class='badge badge-completed'><i class='fas fa-check-circle'></i> Completed</span>";
        case 'pending':
            return "<span class='badge badge-pending'><i class='fas fa-clock'></i> Pending</span>";
        case 'rejected':
            return "<span class='badge badge-rejected'><i class='fas fa-times-circle'></i> Rejected</span>";
        case 'approved':
            return "<span class='badge badge-approved'><i class='fas fa-thumbs-up'></i> Approved</span>";
        case 'ongoing':
            return "<span class='badge badge-ongoing'><i class='fas fa-sync-alt fa-spin'></i> Ongoing</span>";
        case 'active':
            return "<span class='badge badge-approved'><i class='fas fa-user-check'></i> Active</span>";
        default:
            return "<span class='badge badge-secondary'>" . htmlspecialchars($status ?: 'N/A') . "</span>";
    }
}

/**
 * Active Student ID helper
 */
function get_active_student_id() {
    return isset($_SESSION['student_id']) ? (int)$_SESSION['student_id'] : 1;
}

/**
 * Active Faculty ID helper
 */
function get_active_faculty_id() {
    return isset($_SESSION['faculty_id']) ? (int)$_SESSION['faculty_id'] : 1;
}

/**
 * File upload helper function
 */
function upload_file($file_key, $subfolder = 'documents') {
    if (!isset($_FILES[$file_key]) || $_FILES[$file_key]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$file_key];
    $upload_base = __DIR__ . '/../uploads/' . $subfolder . '/';

    if (!file_exists($upload_base)) {
        mkdir($upload_base, 0777, true);
    }

    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['pdf', 'doc', 'docx', 'zip', 'rar', 'png', 'jpg', 'jpeg'];

    if (!in_array($file_ext, $allowed_exts)) {
        return null;
    }

    $new_filename = uniqid('file_', true) . '.' . $file_ext;
    $target_file = $upload_base . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return 'uploads/' . $subfolder . '/' . $new_filename;
    }

    return null;
}

/**
 * Format Date helper
 */
function format_date($date_str) {
    if (empty($date_str) || $date_str === '0000-00-00') return 'N/A';
    return date('d M, Y', strtotime($date_str));
}

/**
 * Fetch Student Dashboard Statistics safely
 */
function get_student_dashboard_stats($conn, $student_id) {
    $stats = [
        'total_projects' => 0,
        'completed_projects' => 0,
        'ongoing_projects' => 0,
        'pending_approvals' => 0,
        'total_internships' => 0,
        'completed_internships' => 0,
        'guide_allocations' => 0,
        'upcoming_reviews' => 0
    ];

    $stmt = $conn->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'Ongoing' THEN 1 ELSE 0 END) as ongoing,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending
        FROM projects WHERE created_by_student_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $stats['total_projects'] = (int)$row['total'];
            $stats['completed_projects'] = (int)$row['completed'];
            $stats['ongoing_projects'] = (int)$row['ongoing'];
            $stats['pending_approvals'] = (int)$row['pending'];
        }
        $stmt->close();
    }

    $stmt2 = $conn->prepare("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
        FROM internships WHERE student_id = ?");
    if ($stmt2) {
        $stmt2->bind_param("i", $student_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        if ($res2 && $row2 = $res2->fetch_assoc()) {
            $stats['total_internships'] = (int)$row2['total'];
            $stats['completed_internships'] = (int)$row2['completed'];
        }
        $stmt2->close();
    }

    $stmt3 = $conn->prepare("SELECT COUNT(*) as allocated FROM guide_allocations WHERE student_id = ? AND status = 'Active'");
    if ($stmt3) {
        $stmt3->bind_param("i", $student_id);
        $stmt3->execute();
        $res3 = $stmt3->get_result();
        if ($res3 && $row3 = $res3->fetch_assoc()) {
            $stats['guide_allocations'] = (int)$row3['allocated'];
        }
        $stmt3->close();
    }

    $stmt4 = $conn->prepare("SELECT COUNT(*) as upcoming FROM milestones m 
        JOIN projects p ON m.project_id = p.id 
        WHERE p.created_by_student_id = ? AND m.status IN ('Pending', 'Ongoing')");
    if ($stmt4) {
        $stmt4->bind_param("i", $student_id);
        $stmt4->execute();
        $res4 = $stmt4->get_result();
        if ($res4 && $row4 = $res4->fetch_assoc()) {
            $stats['upcoming_reviews'] = (int)$row4['upcoming'];
        }
        $stmt4->close();
    }

    return $stats;
}

/**
 * Add Notification
 */
function add_notification($conn, $user_type, $user_id, $title, $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_type, user_id, title, message) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("siss", $user_type, $user_id, $title, $message);
        $stmt->execute();
        $stmt->close();
    }
}
?>
