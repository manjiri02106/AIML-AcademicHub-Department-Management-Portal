<?php
/**
 * Delete Project Handler - Student Portal
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

$student_id = get_active_student_id();
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['token'] ?? '';

if ($project_id > 0 && verify_csrf_token($token)) {
    // Delete project
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND created_by_student_id = ?");
    $stmt->bind_param("ii", $project_id, $student_id);
    
    if ($stmt->execute()) {
        set_flash_message('success', 'Project deleted successfully.');
    } else {
        set_flash_message('error', 'Could not delete project: ' . $stmt->error);
    }
    $stmt->close();
} else {
    set_flash_message('error', 'Invalid request parameters.');
}

header("Location: view_projects.php");
exit;
?>
