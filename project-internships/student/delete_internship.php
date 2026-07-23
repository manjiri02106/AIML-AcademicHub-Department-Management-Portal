<?php
/**
 * Delete Internship Handler - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$internship_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['token'] ?? '';

if ($internship_id > 0 && verify_csrf_token($token)) {
    $stmt = $conn->prepare("DELETE FROM internships WHERE id = ? AND student_id = ?");
    $stmt->bind_param("ii", $internship_id, $student_id);
    
    if ($stmt->execute()) {
        set_flash_message('success', 'Internship record deleted successfully.');
    } else {
        set_flash_message('error', 'Could not delete internship: ' . $stmt->error);
    }
    $stmt->close();
} else {
    set_flash_message('error', 'Invalid request parameters.');
}

header("Location: view_internships.php");
exit;
?>
