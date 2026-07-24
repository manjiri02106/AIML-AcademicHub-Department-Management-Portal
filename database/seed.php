<?php
// backend/scripts/seed.php

$host = 'localhost';
$db   = 'aiml_academichub';
$user = 'root'; // Change as needed
$pass = '';     // Change as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`;");
    $pdo->exec("USE `$db`;");

    // Load and execute schema.sql
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    if ($schema) {
        $pdo->exec($schema);
        echo "Schema created successfully.\n";
    }

    // Insert dummy user and faculty
    $pdo->exec("INSERT INTO users (username, password, role) VALUES ('faculty1', 'password123', 'faculty') ON DUPLICATE KEY UPDATE id=id;");
    $user_id = $pdo->lastInsertId() ?: 1;

    $pdo->exec("INSERT INTO faculty_profiles (id, user_id, full_name, email, phone, designation, qualifications, research_interests) 
                VALUES (1, $user_id, 'Dr. Alan Turing', 'alan@aiml.edu', '9876543210', 'Professor', 'PhD in AI', 'Machine Learning, Neural Networks') 
                ON DUPLICATE KEY UPDATE id=id;");

    // Insert courses
    $pdo->exec("INSERT INTO courses (id, course_code, course_name, semester) VALUES 
                (1, 'CS101', 'Introduction to AI', 1),
                (2, 'CS201', 'Deep Learning', 3)
                ON DUPLICATE KEY UPDATE id=id;");

    // Allocate courses to faculty
    $pdo->exec("INSERT INTO course_allocations (faculty_id, course_id, academic_year) VALUES 
                (1, 1, '2023-2024'),
                (1, 2, '2023-2024')
                ON DUPLICATE KEY UPDATE id=id;");

    // Insert students
    $pdo->exec("INSERT INTO students (id, roll_number, name) VALUES 
                (1, 'AIML001', 'Alice Smith'),
                (2, 'AIML002', 'Bob Jones')
                ON DUPLICATE KEY UPDATE id=id;");

    // Add mentoring records
    $pdo->exec("INSERT INTO mentoring_records (faculty_id, student_id, session_date, discussion_points, action_items) VALUES 
                (1, 1, '2024-01-15', 'Discussed project ideas', 'Start reading literature'),
                (1, 2, '2024-01-16', 'Reviewed attendance', 'Improve attendance')
                ON DUPLICATE KEY UPDATE id=id;");

    echo "Seed data inserted successfully.\n";

} catch (\PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
