<?php
require_once __DIR__ . '/config/database.php';

$testUsers = [
    ['name' => 'HOD User', 'email' => 'hod@aiml.edu', 'password' => 'Hod@123', 'role' => 'HOD'],
    ['name' => 'Faculty User', 'email' => 'faculty@aiml.edu', 'password' => 'Faculty@123', 'role' => 'Faculty'],
    ['name' => 'Student User', 'email' => 'student@aiml.edu', 'password' => 'Student@123', 'role' => 'Student'],
    ['name' => 'TPO User', 'email' => 'tpo@aiml.edu', 'password' => 'Tpo@123', 'role' => 'TPO'],
    ['name' => 'Lab Coordinator', 'email' => 'lab@aiml.edu', 'password' => 'Lab@123', 'role' => 'Lab Coordinator'],
    ['name' => 'IQAC Coordinator', 'email' => 'iqac@aiml.edu', 'password' => 'Iqac@123', 'role' => 'IQAC/NBA Coordinator'],
];

try {
    $stmt = $pdo->prepare('INSERT INTO users (name, full_name, email, password, password_hash, role, role_id, is_active, status) VALUES (?, ?, ?, ?, ?, ?, ?, 1, "active")');
    
    $roleMap = [
        'HOD' => 2,
        'Faculty' => 3,
        'Student' => 4,
        'TPO' => 5,
        'Lab Coordinator' => 6,
        'IQAC/NBA Coordinator' => 7,
    ];

    foreach ($testUsers as $user) {
        $hash = password_hash($user['password'], PASSWORD_DEFAULT);
        $roleId = $roleMap[$user['role']] ?? null;
        $stmt->execute([
            $user['name'],
            $user['name'], // full_name
            $user['email'],
            $hash, // password (new column)
            $hash, // password_hash (old column)
            $user['role'],
            $roleId
        ]);
    }
    
    echo "<h2>✅ Test users created successfully!</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Role</th><th>Email</th><th>Password</th></tr>";
    
    foreach ($testUsers as $user) {
        echo "<tr><td>{$user['role']}</td><td>{$user['email']}</td><td>{$user['password']}</td></tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
