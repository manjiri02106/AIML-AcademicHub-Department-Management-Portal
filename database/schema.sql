CREATE DATABASE IF NOT EXISTS aiml_academichub;
USE aiml_academichub;

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL UNIQUE,
  label VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS role_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  permission_id INT NOT NULL,
  UNIQUE KEY uniq_role_permission (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mobile VARCHAR(20) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  department VARCHAR(100) DEFAULT NULL,
  designation VARCHAR(100) DEFAULT NULL,
  role_id INT DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  joining_date DATE DEFAULT NULL,
  profile_photo VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS department_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(150) DEFAULT NULL,
  department_code VARCHAR(50) DEFAULT NULL,
  institute_name VARCHAR(150) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  academic_year VARCHAR(50) DEFAULT NULL,
  semester VARCHAR(50) DEFAULT NULL,
  divisions VARCHAR(100) DEFAULT NULL,
  sections VARCHAR(100) DEFAULT NULL,
  subjects TEXT DEFAULT NULL,
  course_types VARCHAR(100) DEFAULT NULL,
  theme_color VARCHAR(20) DEFAULT NULL,
  footer_text VARCHAR(255) DEFAULT NULL,
  smtp_email VARCHAR(150) DEFAULT NULL,
  sms_enabled TINYINT(1) DEFAULT 0,
  push_enabled TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS academic_year (
  id INT AUTO_INCREMENT PRIMARY KEY,
  year_label VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS semester (
  id INT AUTO_INCREMENT PRIMARY KEY,
  semester_name VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS backup_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  backup_name VARCHAR(150) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  size_bytes BIGINT DEFAULT 0,
  status VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS system_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  value VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  message VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO roles (id, name) VALUES (1, 'Administrator'), (2, 'HOD'), (3, 'Faculty'), (4, 'Student'), (5, 'TPO'), (6, 'Lab Coordinator'), (7, 'IQAC Coordinator');

INSERT IGNORE INTO permissions (id, slug, label) VALUES
(1, 'dashboard', 'Dashboard'),
(2, 'users', 'User Module'),
(3, 'roles', 'Roles Module'),
(4, 'settings', 'Settings Module'),
(5, 'backup', 'Backup Module'),
(6, 'students', 'Student Module'),
(7, 'faculty', 'Faculty Module'),
(8, 'attendance', 'Attendance'),
(9, 'academics', 'Academics'),
(10, 'projects', 'Projects'),
(11, 'placements', 'Placements'),
(12, 'research', 'Research'),
(13, 'events', 'Events'),
(14, 'reports', 'Reports');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.slug='dashboard' WHERE r.name='Administrator';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.slug='users' WHERE r.name='Administrator';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.slug='roles' WHERE r.name='Administrator';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.slug='settings' WHERE r.name='Administrator';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r JOIN permissions p ON p.slug='backup' WHERE r.name='Administrator';

INSERT IGNORE INTO users (id, full_name, email, mobile, password_hash, department, designation, role_id, status, joining_date)
VALUES (1, 'System Administrator', 'admin@aiml.edu', '9876543210', '$2y$10$KCHh6GdE4r5KzXNvZrSxae8S6bjZi77Xnfd2yjhVCtw7DxLN5Wava', 'AI & ML', 'Administrator', 1, 'active', '2024-01-01');

INSERT IGNORE INTO department_settings (id, department_name, department_code, institute_name, address, email, phone, academic_year, semester, divisions, sections, subjects, course_types, theme_color, footer_text, smtp_email, sms_enabled, push_enabled)
VALUES (1, 'Artificial Intelligence & Machine Learning', 'AIML', 'AIML AcademicHub Institute', 'New Delhi, India', 'dept@aiml.edu', '011-45001234', '2025-2026', 'Semester 1', 'A, B', 'A, B', 'Python, Data Structures, ML Basics', 'UG, PG', '#2563eb', '© 2026 AIML AcademicHub', 'smtp@aiml.edu', 1, 1);
