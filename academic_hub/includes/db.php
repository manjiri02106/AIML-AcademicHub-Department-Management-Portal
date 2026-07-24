<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbHost = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$dbName = 'academic_hub';

try {
	$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);
	$conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
	error_log('Database connection failed: ' . $exception->getMessage());
	http_response_code(500);
	exit('Database connection failed.');
}

// Integrate with main AcademicHub authentication and helper functions
require_once __DIR__ . '/../../includes/functions.php';

// Force login check to ensure only authorized roles can access placement modules
requireRole(['TPO', 'HOD', 'Administrator', 'Faculty', 'Student']);

