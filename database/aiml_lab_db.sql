-- ====================================================================
-- AIML AcademicHub - Department Management Portal
-- Module: Laboratory Management System Database Schema & Initial Data
-- Compatible with MySQL / MariaDB (XAMPP Localhost)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `aiml_lab_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aiml_lab_db`;

-- --------------------------------------------------------
-- Table structure for `labs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `maintenance_logs`;
DROP TABLE IF EXISTS `schedules`;
DROP TABLE IF EXISTS `equipment`;
DROP TABLE IF EXISTS `labs`;

CREATE TABLE `labs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lab_name` VARCHAR(150) NOT NULL,
  `location` VARCHAR(150) NOT NULL,
  `capacity` VARCHAR(50) NOT NULL DEFAULT '35 Students',
  `incharge_name` VARCHAR(150) NOT NULL,
  `incharge_email` VARCHAR(150) NOT NULL,
  `installed_software` TEXT NULL,
  `facilities` TEXT NULL,
  `status` ENUM('Active', 'Maintenance', 'Inactive') DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `equipment`
-- --------------------------------------------------------

CREATE TABLE `equipment` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `equipment_code` VARCHAR(50) NOT NULL UNIQUE,
  `equipment_name` VARCHAR(150) NOT NULL,
  `category` ENUM('Computer Lab Equipment', 'Physics & BXEE Equipment', 'Chemistry Equipment') NOT NULL,
  `lab_id` INT NOT NULL,
  `purchase_date` DATE NOT NULL,
  `warranty` VARCHAR(50) NOT NULL DEFAULT '3 Years',
  `quantity` INT NOT NULL DEFAULT 1,
  `status` ENUM('Available', 'In Use', 'Under Maintenance') DEFAULT 'Available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`lab_id`) REFERENCES `labs`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `maintenance_logs`
-- --------------------------------------------------------

CREATE TABLE `maintenance_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `equipment_id` INT NOT NULL,
  `equipment_name` VARCHAR(150) NOT NULL,
  `issue` VARCHAR(255) NOT NULL,
  `reported_date` DATE NOT NULL,
  `repair_status` ENUM('Pending', 'In Progress', 'Completed', 'Replaced') DEFAULT 'Pending',
  `cost` DECIMAL(10,2) DEFAULT 0.00,
  `remarks` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `schedules`
-- --------------------------------------------------------

CREATE TABLE `schedules` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `year_name` VARCHAR(50) NOT NULL DEFAULT '1st Year',
  `division` ENUM('A', 'B', 'C') NOT NULL,
  `batch` ENUM('Batch 1', 'Batch 2', 'Batch 3') NOT NULL,
  `subject` ENUM('Physics', 'Gen AI', 'ITP', 'FCSN') NOT NULL,
  `faculty_name` VARCHAR(150) NOT NULL,
  `lab_id` INT NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
  `time_slot` ENUM('8:00 AM – 10:00 AM', '10:30 AM – 12:30 PM', '1:15 PM – 3:15 PM') NOT NULL,
  `duration` VARCHAR(20) NOT NULL DEFAULT '2 Hours',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`lab_id`) REFERENCES `labs`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ====================================================================
-- SEED DATA INSERTION
-- ====================================================================

-- 1. Insert 6 Laboratories
INSERT INTO `labs` (`id`, `lab_name`, `location`, `capacity`, `incharge_name`, `incharge_email`, `installed_software`, `facilities`, `status`) VALUES
(1, 'Computer Lab 1', 'B-401, 4th Floor', '35-40 Students', 'Dipika Bhatt', 'dipika.bhatt@academichub.edu', 'Windows 11, Python, Visual Studio Code, MySQL Workbench, XAMPP, Dev C++, Notepad++, Google Chrome, Microsoft Office', 'Air Conditioning, High Speed Internet, LCD Projector, White Board, UPS Backup, LAN Connectivity, WiFi, CCTV, Fire Extinguisher', 'Active'),
(2, 'Computer Lab 2', 'B-402, 4th Floor', '35-40 Students', 'Pushkraj Sonawane', 'pushkraj.sonawane@academichub.edu', 'Windows 11, Python, Visual Studio Code, MySQL Workbench, XAMPP, Dev C++, Notepad++, Google Chrome, Microsoft Office', 'Air Conditioning, High Speed Internet, LCD Projector, White Board, UPS Backup, LAN Connectivity, WiFi, CCTV, Fire Extinguisher', 'Active'),
(3, 'Computer Lab 3', 'B-403, 4th Floor', '35-40 Students', 'Shrutika Saudagar', 'shrutika.saudagar@academichub.edu', 'Windows 11, Python, Visual Studio Code, MySQL Workbench, XAMPP, Dev C++, Notepad++, Google Chrome, Microsoft Office', 'Air Conditioning, High Speed Internet, LCD Projector, White Board, UPS Backup, LAN Connectivity, WiFi, CCTV, Fire Extinguisher', 'Active'),
(4, 'Computer Lab 4', 'B-404, 4th Floor', '35-40 Students', 'Omkar', 'omkar@academichub.edu', 'Windows 11, Python, Visual Studio Code, MySQL Workbench, XAMPP, Dev C++, Notepad++, Google Chrome, Microsoft Office', 'Air Conditioning, High Speed Internet, LCD Projector, White Board, UPS Backup, LAN Connectivity, WiFi, CCTV, Fire Extinguisher', 'Active'),
(5, 'Physics & BXEE Laboratory', 'B-408, 4th Floor', '30-35 Students', 'Haridas Rangnath', 'haridas.rangnath@academichub.edu', 'LabVIEW, MATLAB Hardware Support, Circuit Simulator', 'Air Conditioning, High Speed Internet, LCD Projector, White Board, UPS Backup, LAN Connectivity, WiFi, CCTV, Fire Extinguisher', 'Active'),
(6, 'Chemistry Laboratory', 'B-406, 4th Floor', '30-35 Students', 'Sarika Desai', 'sarika.desai@academichub.edu', 'ChemDraw, Digital Balance Software', 'Air Conditioning, High Speed Internet, White Board, Exhaust Fume Hood, First Aid Kit, Eye Wash Station, Fire Extinguisher', 'Active');


-- 2. Insert Equipment Records
INSERT INTO `equipment` (`id`, `equipment_code`, `equipment_name`, `category`, `lab_id`, `purchase_date`, `warranty`, `quantity`, `status`) VALUES
-- Computer Lab Equipment
(1, 'EQ-1001', 'Desktop Computer', 'Computer Lab Equipment', 1, '2024-01-15', '3 Years', 35, 'Under Maintenance'),
(2, 'EQ-1002', 'GPU Workstation', 'Computer Lab Equipment', 1, '2024-02-10', '3 Years', 5, 'Available'),
(3, 'EQ-1003', 'LED Monitor', 'Computer Lab Equipment', 2, '2023-11-20', '3 Years', 40, 'Under Maintenance'),
(4, 'EQ-1004', 'Keyboard', 'Computer Lab Equipment', 2, '2023-11-20', '2 Years', 40, 'Available'),
(5, 'EQ-1005', 'Mouse', 'Computer Lab Equipment', 3, '2023-11-20', '2 Years', 40, 'Available'),
(6, 'EQ-1006', 'Projector', 'Computer Lab Equipment', 3, '2024-03-05', '2 Years', 1, 'Under Maintenance'),
(7, 'EQ-1007', 'Laser Printer', 'Computer Lab Equipment', 4, '2023-08-12', '2 Years', 2, 'Under Maintenance'),
(8, 'EQ-1008', 'UPS', 'Computer Lab Equipment', 4, '2023-09-01', '3 Years', 2, 'Under Maintenance'),
(9, 'EQ-1009', 'Router', 'Computer Lab Equipment', 1, '2024-01-05', '5 Years', 3, 'Available'),
(10, 'EQ-1010', 'Network Switch', 'Computer Lab Equipment', 2, '2024-01-05', '5 Years', 4, 'Available'),

-- Physics & BXEE Equipment
(11, 'EQ-2001', 'Digital Multimeter', 'Physics & BXEE Equipment', 5, '2023-07-10', '2 Years', 15, 'Available'),
(12, 'EQ-2002', 'Ammeter', 'Physics & BXEE Equipment', 5, '2023-07-10', '2 Years', 20, 'Available'),
(13, 'EQ-2003', 'Voltmeter', 'Physics & BXEE Equipment', 5, '2023-07-10', '2 Years', 20, 'Available'),
(14, 'EQ-2004', 'Transformer Trainer Kit', 'Physics & BXEE Equipment', 5, '2023-09-15', '3 Years', 6, 'Available'),
(15, 'EQ-2005', 'DC Motor', 'Physics & BXEE Equipment', 5, '2023-09-15', '3 Years', 8, 'Available'),
(16, 'EQ-2006', 'AC Motor', 'Physics & BXEE Equipment', 5, '2023-09-15', '3 Years', 8, 'Available'),
(17, 'EQ-2007', 'Energy Meter', 'Physics & BXEE Equipment', 5, '2023-10-01', '2 Years', 10, 'Available'),
(18, 'EQ-2008', 'Power Supply Unit', 'Physics & BXEE Equipment', 5, '2023-10-01', '3 Years', 12, 'Available'),
(19, 'EQ-2009', 'Electrical Circuit Board', 'Physics & BXEE Equipment', 5, '2023-11-05', '2 Years', 15, 'Available'),
(20, 'EQ-2010', 'Clamp Meter', 'Physics & BXEE Equipment', 5, '2023-11-05', '2 Years', 5, 'Available'),

-- Chemistry Equipment
(21, 'EQ-3001', 'Beakers', 'Chemistry Equipment', 6, '2023-06-01', '1 Year', 100, 'Available'),
(22, 'EQ-3002', 'Test Tubes', 'Chemistry Equipment', 6, '2023-06-01', '1 Year', 200, 'Available'),
(23, 'EQ-3003', 'Conical Flask', 'Chemistry Equipment', 6, '2023-06-01', '1 Year', 80, 'Available'),
(24, 'EQ-3004', 'Measuring Cylinder', 'Chemistry Equipment', 6, '2023-06-01', '1 Year', 50, 'Available'),
(25, 'EQ-3005', 'Burette', 'Chemistry Equipment', 6, '2023-06-01', '1 Year', 40, 'Available'),
(26, 'EQ-3006', 'Pipette', 'Chemistry Equipment', 6, '2023-06-01', '1 Year', 40, 'Available'),
(27, 'EQ-3007', 'pH Meter', 'Chemistry Equipment', 6, '2023-08-20', '2 Years', 10, 'Available'),
(28, 'EQ-3008', 'Hot Plate', 'Chemistry Equipment', 6, '2023-08-20', '2 Years', 8, 'Available'),
(29, 'EQ-3009', 'Electronic Weighing Balance', 'Chemistry Equipment', 6, '2023-09-12', '3 Years', 4, 'Available'),
(30, 'EQ-3010', 'Chemical Reagents', 'Chemistry Equipment', 6, '2024-01-10', '1 Year', 25, 'Available');


-- 3. Insert Maintenance Logs (ONLY 5 Computer-related items)
INSERT INTO `maintenance_logs` (`id`, `equipment_id`, `equipment_name`, `issue`, `reported_date`, `repair_status`, `cost`, `remarks`) VALUES
(1, 1, 'Desktop Computer', 'PC Not Booting', '2026-07-10', 'In Progress', 1500.00, 'RAM replacement and power supply diagnostic required for Lab 1 PC-04.'),
(2, 6, 'Projector', 'Projector Not Working', '2026-07-12', 'Pending', 3500.00, 'Lamp bulb burnt out during lecture session in Lab 3.'),
(3, 7, 'Laser Printer', 'Printer Paper Jam', '2026-07-15', 'In Progress', 500.00, 'Roller assembly cleaning and gear alignment needed in Lab 4.'),
(4, 3, 'LED Monitor', 'Monitor Flickering', '2026-07-18', 'Completed', 800.00, 'Replaced faulty HDMI cable and display driver board for Lab 2 PC-12.'),
(5, 8, 'UPS', 'UPS Battery Failure', '2026-07-20', 'Pending', 2800.00, 'Battery backup failing under full load in Lab 4 UPS bank.');


-- 4. Insert Lab Schedules (1st Year, 2nd Semester Practicals only)
INSERT INTO `schedules` (`id`, `year_name`, `division`, `batch`, `subject`, `faculty_name`, `lab_id`, `day_of_week`, `time_slot`, `duration`) VALUES
-- Monday
('1', '1st Year', 'A', 'Batch 1', 'Gen AI', 'Dipika Bhatt', 1, 'Monday', '8:00 AM – 10:00 AM', '2 Hours'),
('2', '1st Year', 'A', 'Batch 2', 'ITP', 'Pushkraj Sonawane', 2, 'Monday', '8:00 AM – 10:00 AM', '2 Hours'),
('3', '1st Year', 'A', 'Batch 3', 'Physics', 'Haridas Rangnath', 5, 'Monday', '8:00 AM – 10:00 AM', '2 Hours'),

('4', '1st Year', 'B', 'Batch 1', 'FCSN', 'Omkar', 4, 'Monday', '1:15 PM – 3:15 PM', '2 Hours'),
('5', '1st Year', 'B', 'Batch 2', 'Gen AI', 'Dipika Bhatt', 1, 'Monday', '1:15 PM – 3:15 PM', '2 Hours'),

-- Tuesday
('6', '1st Year', 'A', 'Batch 1', 'Physics', 'Haridas Rangnath', 5, 'Tuesday', '10:30 AM – 12:30 PM', '2 Hours'),
('7', '1st Year', 'A', 'Batch 2', 'FCSN', 'Omkar', 4, 'Tuesday', '10:30 AM – 12:30 PM', '2 Hours'),
('8', '1st Year', 'A', 'Batch 3', 'ITP', 'Shrutika Saudagar', 3, 'Tuesday', '10:30 AM – 12:30 PM', '2 Hours'),

('9', '1st Year', 'C', 'Batch 1', 'Gen AI', 'Dipika Bhatt', 1, 'Tuesday', '1:15 PM – 3:15 PM', '2 Hours'),
('10', '1st Year', 'C', 'Batch 2', 'Physics', 'Haridas Rangnath', 5, 'Tuesday', '1:15 PM – 3:15 PM', '2 Hours'),

-- Wednesday
('11', '1st Year', 'B', 'Batch 1', 'ITP', 'Pushkraj Sonawane', 2, 'Wednesday', '8:00 AM – 10:00 AM', '2 Hours'),
('12', '1st Year', 'B', 'Batch 3', 'Physics', 'Haridas Rangnath', 5, 'Wednesday', '8:00 AM – 10:00 AM', '2 Hours'),

('13', '1st Year', 'C', 'Batch 3', 'FCSN', 'Omkar', 4, 'Wednesday', '10:30 AM – 12:30 PM', '2 Hours'),

-- Thursday
('14', '1st Year', 'A', 'Batch 1', 'FCSN', 'Omkar', 4, 'Thursday', '8:00 AM – 10:00 AM', '2 Hours'),
('15', '1st Year', 'A', 'Batch 2', 'Physics', 'Haridas Rangnath', 5, 'Thursday', '8:00 AM – 10:00 AM', '2 Hours'),
('16', '1st Year', 'A', 'Batch 3', 'Gen AI', 'Dipika Bhatt', 1, 'Thursday', '8:00 AM – 10:00 AM', '2 Hours'),

('17', '1st Year', 'B', 'Batch 2', 'FCSN', 'Omkar', 4, 'Thursday', '1:15 PM – 3:15 PM', '2 Hours'),

-- Friday
('18', '1st Year', 'C', 'Batch 1', 'Physics', 'Haridas Rangnath', 5, 'Friday', '10:30 AM – 12:30 PM', '2 Hours'),
('19', '1st Year', 'C', 'Batch 2', 'ITP', 'Shrutika Saudagar', 3, 'Friday', '10:30 AM – 12:30 PM', '2 Hours'),
('20', '1st Year', 'C', 'Batch 3', 'Gen AI', 'Dipika Bhatt', 1, 'Friday', '10:30 AM – 12:30 PM', '2 Hours');
