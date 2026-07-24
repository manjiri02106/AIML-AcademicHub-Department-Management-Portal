CREATE DATABASE IF NOT EXISTS academic_hub
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE academic_hub;

CREATE TABLE companies (
    company_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL,
    industry VARCHAR(100) NOT NULL,
    website VARCHAR(255),
    contact_person VARCHAR(120),
    contact_email VARCHAR(150),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_companies_name (company_name)
) ENGINE=InnoDB;

CREATE TABLE placement_drives (
    drive_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_id INT UNSIGNED NOT NULL,
    drive_title VARCHAR(180) NOT NULL,
    job_role VARCHAR(150) NOT NULL,
    package_lpa DECIMAL(8,2) NOT NULL,
    drive_date DATE NOT NULL,
    application_deadline DATE NOT NULL,
    eligibility_criteria TEXT,
    status ENUM('Scheduled', 'Open', 'Closed', 'Completed') NOT NULL DEFAULT 'Scheduled',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_drives_company
        FOREIGN KEY (company_id) REFERENCES companies (company_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    INDEX idx_drives_company (company_id),
    INDEX idx_drives_date (drive_date)
) ENGINE=InnoDB;

CREATE TABLE students (
    student_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(30) NOT NULL,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100) NOT NULL,
    branch VARCHAR(50) NOT NULL,
    skills TEXT NOT NULL,
    resume_path VARCHAR(255) NOT NULL,
    course VARCHAR(120) NOT NULL,
    graduation_year YEAR NOT NULL,
    cgpa DECIMAL(4,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_students_roll_number (roll_number),
    UNIQUE KEY uq_students_email (email)
) ENGINE=InnoDB;

CREATE TABLE applications (
    application_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    drive_id INT UNSIGNED NOT NULL,
    application_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Applied', 'Shortlisted', 'Interview', 'Selected', 'Rejected', 'Withdrawn') NOT NULL DEFAULT 'Applied',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_applications_student
        FOREIGN KEY (student_id) REFERENCES students (student_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_applications_drive
        FOREIGN KEY (drive_id) REFERENCES placement_drives (drive_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    UNIQUE KEY uq_applications_student_drive (student_id, drive_id),
    INDEX idx_applications_status (status)
) ENGINE=InnoDB;

CREATE TABLE offers (
    offer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id INT UNSIGNED NOT NULL,
    offer_date DATE NOT NULL,
    joining_date DATE,
    offered_package_lpa DECIMAL(8,2) NOT NULL,
    offer_status ENUM('Pending', 'Accepted', 'Declined', 'Expired') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_offers_application
        FOREIGN KEY (application_id) REFERENCES applications (application_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    UNIQUE KEY uq_offers_application (application_id)
) ENGINE=InnoDB;

INSERT INTO companies (company_name, industry, website, contact_person, contact_email)
VALUES
    ('TechNova Solutions', 'Information Technology', 'https://technova.example.com', 'Anita Sharma', 'anita.sharma@technova.example.com'),
    ('DataSphere Analytics', 'Data Science', 'https://datasphere.example.com', 'Rahul Mehta', 'rahul.mehta@datasphere.example.com'),
    ('CloudPeak Systems', 'Cloud Computing', 'https://cloudpeak.example.com', 'Priya Nair', 'priya.nair@cloudpeak.example.com');

INSERT INTO placement_drives
    (company_id, drive_title, job_role, package_lpa, drive_date, application_deadline, eligibility_criteria, status)
VALUES
    (1, 'TechNova Graduate Hiring 2026', 'Software Engineer', 8.50, '2026-08-15', '2026-08-05', 'B.Tech students with CGPA 7.00 or above and no active backlogs.', 'Open'),
    (2, 'DataSphere Campus Drive 2026', 'Junior Data Analyst', 7.25, '2026-08-22', '2026-08-12', 'AIML or Computer Science students with CGPA 7.50 or above.', 'Scheduled'),
    (3, 'CloudPeak Associate Recruitment', 'Cloud Support Associate', 6.80, '2026-09-01', '2026-08-20', 'Final-year students with basic Linux and networking knowledge.', 'Scheduled');

INSERT INTO students
    (roll_number, first_name, last_name, email, phone, department, branch, skills, resume_path, course, graduation_year, cgpa)
VALUES
    ('AIML2026001', 'Aarav', 'Patel', 'aarav.patel@example.com', '9876500001', 'CSE', 'A', 'Python, SQL, Machine Learning', 'uploads/resumes/sample-aarav.pdf', 'CSE', 2026, 8.72),
    ('AIML2026002', 'Meera', 'Iyer', 'meera.iyer@example.com', '9876500002', 'IT', 'B', 'Java, React, Problem Solving', 'uploads/resumes/sample-meera.pdf', 'IT', 2026, 9.10),
    ('AIML2026003', 'Kabir', 'Singh', 'kabir.singh@example.com', '9876500003', 'ECE', 'C', 'Python, Data Analysis, Communication', 'uploads/resumes/sample-kabir.pdf', 'ECE', 2026, 7.85),
    ('AIML2026004', 'Sana', 'Khan', 'sana.khan@example.com', '9876500004', 'CSE', 'D', 'C++, AWS, Linux', 'uploads/resumes/sample-sana.pdf', 'CSE', 2026, 8.35);

INSERT INTO applications (student_id, drive_id, application_date, status)
VALUES
    (1, 1, '2026-07-10 10:15:00', 'Shortlisted'),
    (2, 1, '2026-07-11 11:30:00', 'Selected'),
    (2, 2, '2026-07-12 09:45:00', 'Interview'),
    (3, 2, '2026-07-13 14:20:00', 'Applied'),
    (4, 3, '2026-07-14 16:00:00', 'Applied');

INSERT INTO offers (application_id, offer_date, joining_date, offered_package_lpa, offer_status)
VALUES
    (2, '2026-07-20', '2026-09-15', 8.50, 'Accepted');