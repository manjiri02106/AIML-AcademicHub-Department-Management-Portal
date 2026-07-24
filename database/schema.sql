CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'hod', 'faculty', 'student', 'tpo', 'lab_coordinator', 'iqac') NOT NULL
);

CREATE TABLE IF NOT EXISTS faculty_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    designation VARCHAR(50),
    qualifications TEXT,
    research_interests TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE,
    course_name VARCHAR(100),
    semester INT
);

CREATE TABLE IF NOT EXISTS course_allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT,
    course_id INT,
    academic_year VARCHAR(20),
    FOREIGN KEY (faculty_id) REFERENCES faculty_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(20) UNIQUE,
    name VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS mentoring_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT,
    student_id INT,
    session_date DATE,
    discussion_points TEXT,
    action_items TEXT,
    FOREIGN KEY (faculty_id) REFERENCES faculty_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
