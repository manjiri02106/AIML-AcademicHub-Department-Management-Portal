-- ==========================================================
-- Academic Hub - Projects & Internships Database Schema
-- Compatible with MySQL / MariaDB (XAMPP)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `academichub_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `academichub_db`;

-- --------------------------------------------------------
-- Table: students
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `roll_number` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `department` VARCHAR(100) NOT NULL,
  `academic_year` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: faculty
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faculty` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `faculty_code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `department` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: projects
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `abstract` TEXT NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `technology_stack` VARCHAR(255) NOT NULL,
  `guide_name` VARCHAR(100) DEFAULT NULL,
  `guide_id` INT DEFAULT NULL,
  `github_link` VARCHAR(255) DEFAULT NULL,
  `document_path` VARCHAR(255) DEFAULT NULL,
  `images_path` VARCHAR(255) DEFAULT NULL,
  `start_date` DATE NOT NULL,
  `expected_completion_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Ongoing', 'Completed', 'Rejected') DEFAULT 'Pending',
  `created_by_student_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by_student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`guide_id`) REFERENCES `faculty`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: project_team_members
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_team_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `role` VARCHAR(100) DEFAULT 'Team Member',
  `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: project_guides
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `project_guides` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `faculty_id` INT NOT NULL,
  `allocated_date` DATE NOT NULL,
  `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculty`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: guide_allocations
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `guide_allocations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `project_id` INT DEFAULT NULL,
  `faculty_id` INT NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `allocation_date` DATE NOT NULL,
  `status` ENUM('Active', 'Completed', 'Pending') DEFAULT 'Active',
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculty`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: internships
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `internships` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `company_name` VARCHAR(150) NOT NULL,
  `role` VARCHAR(100) NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `mode` ENUM('Online', 'Offline', 'Hybrid') NOT NULL DEFAULT 'Offline',
  `stipend` VARCHAR(50) DEFAULT 'Unpaid',
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `offer_letter_path` VARCHAR(255) DEFAULT NULL,
  `certificate_path` VARCHAR(255) DEFAULT NULL,
  `company_website` VARCHAR(255) DEFAULT NULL,
  `supervisor_name` VARCHAR(100) DEFAULT NULL,
  `supervisor_contact` VARCHAR(100) DEFAULT NULL,
  `remarks` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Approved', 'Ongoing', 'Completed', 'Rejected') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: milestones
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `milestones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `milestone_name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `due_date` DATE DEFAULT NULL,
  `status` ENUM('Pending', 'Ongoing', 'Completed', 'Approved', 'Rejected') DEFAULT 'Pending',
  `order_sequence` INT DEFAULT 1,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: progress_tracking
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `progress_tracking` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `milestone_id` INT DEFAULT NULL,
  `week_number` INT NOT NULL,
  `progress_percent` INT NOT NULL DEFAULT 0,
  `work_submitted` TEXT NOT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL,
  `comments` TEXT DEFAULT NULL,
  `faculty_remarks` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`milestone_id`) REFERENCES `milestones`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: notifications
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_type` ENUM('student', 'faculty') NOT NULL,
  `user_id` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: reports
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `report_type` VARCHAR(50) NOT NULL,
  `generated_by` VARCHAR(100) NOT NULL,
  `file_path` VARCHAR(255) DEFAULT NULL,
  `parameters_json` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Seed Data for Testing & Demonstration
-- --------------------------------------------------------

INSERT INTO `students` (`id`, `roll_number`, `name`, `email`, `department`, `academic_year`) VALUES
(1, 'CS2026-001', 'Aarav Sharma', 'aarav.sharma@academichub.edu', 'AI & Machine Learning', '2025-2026'),
(2, 'CS2026-002', 'Ananya Patel', 'ananya.patel@academichub.edu', 'AI & Machine Learning', '2025-2026'),
(3, 'CS2026-003', 'Rohan Verma', 'rohan.verma@academichub.edu', 'Computer Science', '2025-2026'),
(4, 'CS2026-004', 'Priya Kulkarni', 'priya.kulkarni@academichub.edu', 'Information Technology', '2025-2026');

INSERT INTO `faculty` (`id`, `faculty_code`, `name`, `email`, `department`, `designation`) VALUES
(1, 'FAC-AIML-01', 'Dr. Rajesh Deshmukh', 'rajesh.deshmukh@academichub.edu', 'AI & Machine Learning', 'Professor & Head'),
(2, 'FAC-AIML-02', 'Prof. Sunita Rao', 'sunita.rao@academichub.edu', 'AI & Machine Learning', 'Associate Professor'),
(3, 'FAC-CS-01', 'Dr. Vikram Joshi', 'vikram.joshi@academichub.edu', 'Computer Science', 'Assistant Professor');

INSERT INTO `projects` (`id`, `title`, `abstract`, `department`, `technology_stack`, `guide_name`, `guide_id`, `github_link`, `document_path`, `images_path`, `start_date`, `expected_completion_date`, `status`, `created_by_student_id`) VALUES
(1, 'AI-Powered Smart Academic Portal', 'An integrated portal using deep learning and automated analytics for tracking student project deliverables and internship milestones.', 'AI & Machine Learning', 'Python, PHP, MySQL, TensorFlow, Vanilla JS', 'Dr. Rajesh Deshmukh', 1, 'https://github.com/academic-hub/smart-portal', 'uploads/documents/sample_doc.pdf', 'uploads/images/sample_preview.png', '2026-01-10', '2026-05-30', 'Ongoing', 1),
(2, 'Automated Resume Screening System', 'NLP-driven tool that extracts skills, education, and match scores for automated campus recruitment processing.', 'AI & Machine Learning', 'Python, Flask, React, SpaCy', 'Prof. Sunita Rao', 2, 'https://github.com/academic-hub/resume-screener', NULL, NULL, '2026-02-01', '2026-04-15', 'Completed', 2),
(3, 'Decentralized Academic Credentials', 'Blockchain implementation for tamper-proof digital degree and internship certificate issuance.', 'Computer Science', 'Solidity, Ethereum, Node.js, Web3.js', 'Dr. Vikram Joshi', 3, 'https://github.com/academic-hub/credential-chain', NULL, NULL, '2026-03-01', '2026-06-30', 'Pending', 3);

INSERT INTO `project_team_members` (`project_id`, `student_id`, `role`) VALUES
(1, 1, 'Team Lead & Backend Lead'),
(1, 2, 'ML Model Developer'),
(2, 2, 'Lead Developer'),
(3, 3, 'Smart Contract Engineer'),
(3, 4, 'Frontend Integration Specialist');

INSERT INTO `guide_allocations` (`student_id`, `project_id`, `faculty_id`, `department`, `allocation_date`, `status`) VALUES
(1, 1, 1, 'AI & Machine Learning', '2026-01-12', 'Active'),
(2, 2, 2, 'AI & Machine Learning', '2026-02-02', 'Completed'),
(3, 3, 3, 'Computer Science', '2026-03-02', 'Active');

INSERT INTO `internships` (`id`, `student_id`, `company_name`, `role`, `duration`, `location`, `mode`, `stipend`, `start_date`, `end_date`, `status`, `company_website`, `supervisor_name`, `supervisor_contact`, `remarks`) VALUES
(1, 1, 'TechCorp Analytics Ltd.', 'Data Science Intern', '3 Months', 'Bengaluru, India', 'Hybrid', '₹25,000/month', '2026-05-01', '2026-07-31', 'Ongoing', 'https://techcorp.example.com', 'Suresh Menon', '+91 98765 43210', 'Performance praised by team lead.'),
(2, 2, 'CloudInnovate Systems', 'Full Stack Developer Intern', '6 Months', 'Remote', 'Online', '₹20,000/month', '2025-12-01', '2026-05-31', 'Completed', 'https://cloudinnovate.example.com', 'Meera Nair', 'meera@cloudinnovate.com', 'Excellent project completion certificate issued.'),
(3, 3, 'CyberShield Security', 'Security Analyst Intern', '2 Months', 'Pune, India', 'Offline', '₹15,000/month', '2026-06-01', '2026-07-31', 'Approved', 'https://cybershield.example.com', 'Amit Gupta', 'amit@cybershield.com', 'Offer letter verified by HOD.');

INSERT INTO `milestones` (`id`, `project_id`, `milestone_name`, `description`, `due_date`, `status`, `order_sequence`) VALUES
(1, 1, 'Proposal & Requirements', 'Submit final problem statement, technology stack, and literature survey.', '2026-01-20', 'Completed', 1),
(2, 1, 'Architecture & Database Design', 'DB ER Diagram, API Schema, and Wireframes.', '2026-02-15', 'Completed', 2),
(3, 1, 'Core Feature Implementation', 'Build project & internship dashboard, tracking & guide allocation.', '2026-03-30', 'Ongoing', 3),
(4, 1, 'Testing & Final Review', 'Unit testing, user verification, final report document.', '2026-05-15', 'Pending', 4);

INSERT INTO `progress_tracking` (`id`, `project_id`, `student_id`, `milestone_id`, `week_number`, `progress_percent`, `work_submitted`, `comments`, `faculty_remarks`, `status`, `reviewed_at`) VALUES
(1, 1, 1, 1, 1, 25, 'Completed literature survey of existing ERP systems.', 'First draft of requirements attached.', 'Approved. Proceed with system architecture.', 'Approved', '2026-01-22 10:30:00'),
(2, 1, 1, 2, 4, 55, 'Designed MySQL database schema and mockups.', 'ER Diagram verified with guide.', 'Schema looks comprehensive.', 'Approved', '2026-02-17 14:15:00'),
(3, 1, 1, 3, 8, 75, 'Implemented student submission forms and faculty review portal.', 'Modules ready for testing.', NULL, 'Pending', NULL);

INSERT INTO `notifications` (`user_type`, `user_id`, `title`, `message`, `is_read`) VALUES
('student', 1, 'Guide Allocated', 'Dr. Rajesh Deshmukh has been assigned as your project guide.', 1),
('student', 1, 'Progress Approved', 'Your Week 4 progress submission was approved by Dr. Rajesh Deshmukh.', 0),
('faculty', 1, 'New Project Submitted', 'Student Aarav Sharma has submitted a new project for approval.', 0);
