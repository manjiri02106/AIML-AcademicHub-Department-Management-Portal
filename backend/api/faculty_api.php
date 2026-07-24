<?php
// backend/api/faculty_api.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Simulate logged in faculty
$faculty_id = 1; 

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_dashboard_stats':
            $stmt = $pdo->prepare("SELECT COUNT(*) as course_count FROM course_allocations WHERE faculty_id = ?");
            $stmt->execute([$faculty_id]);
            $courses = $stmt->fetchColumn();

            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT student_id) as mentee_count FROM mentoring_records WHERE faculty_id = ?");
            $stmt->execute([$faculty_id]);
            $mentees = $stmt->fetchColumn();

            echo json_encode(['status' => 'success', 'data' => ['courses' => $courses, 'mentees' => $mentees]]);
            break;

        case 'get_profile':
            $stmt = $pdo->prepare("SELECT * FROM faculty_profiles WHERE id = ?");
            $stmt->execute([$faculty_id]);
            $profile = $stmt->fetch();
            echo json_encode(['status' => 'success', 'data' => $profile]);
            break;

        case 'update_profile':
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE faculty_profiles SET full_name = ?, phone = ?, qualifications = ?, research_interests = ? WHERE id = ?");
            $stmt->execute([$input['full_name'], $input['phone'], $input['qualifications'], $input['research_interests'], $faculty_id]);
            echo json_encode(['status' => 'success', 'message' => 'Profile updated']);
            break;

        case 'get_courses':
            $stmt = $pdo->prepare("
                SELECT c.course_code, c.course_name, c.semester, ca.academic_year 
                FROM course_allocations ca
                JOIN courses c ON ca.course_id = c.id
                WHERE ca.faculty_id = ?
            ");
            $stmt->execute([$faculty_id]);
            $courses = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $courses]);
            break;

        case 'get_mentoring_records':
            $stmt = $pdo->prepare("
                SELECT m.id, s.roll_number, s.name as student_name, m.session_date, m.discussion_points, m.action_items 
                FROM mentoring_records m
                JOIN students s ON m.student_id = s.id
                WHERE m.faculty_id = ?
                ORDER BY m.session_date DESC
            ");
            $stmt->execute([$faculty_id]);
            $records = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $records]);
            break;

        case 'add_mentoring_record':
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO mentoring_records (faculty_id, student_id, session_date, discussion_points, action_items) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$faculty_id, $input['student_id'], $input['session_date'], $input['discussion_points'], $input['action_items']]);
            echo json_encode(['status' => 'success', 'message' => 'Record added']);
            break;

        case 'get_students':
            // For the add mentoring dropdown
            $stmt = $pdo->query("SELECT id, roll_number, name FROM students");
            $students = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $students]);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} catch (\Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
