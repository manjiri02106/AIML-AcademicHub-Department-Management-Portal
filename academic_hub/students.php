<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION)) {
    $_SESSION = [];
}

function students_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['students_csrf_token'])) {
    $_SESSION['students_csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['students_csrf_token'];
$message = null;
$messageType = 'success';
$uploadedResumePath = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        if (!hash_equals($csrfToken, (string) ($_POST['csrf_token'] ?? ''))) {
            throw new InvalidArgumentException('Your session expired. Please refresh the page and try again.');
        }

        $prn = trim((string) ($_POST['prn'] ?? ''));
        $fullName = trim((string) ($_POST['name'] ?? ''));
        $department = (string) ($_POST['department'] ?? '');
        $branch = (string) ($_POST['branch'] ?? '');
        $cgpa = filter_input(INPUT_POST, 'cgpa', FILTER_VALIDATE_FLOAT);
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $skills = trim((string) ($_POST['skills'] ?? ''));
        $departments = ['CSE', 'IT', 'ECE', 'ME', 'CE'];
        $branches = ['A', 'B', 'C', 'D'];

        if ($prn === '' || !preg_match('/^[A-Za-z0-9-]{4,30}$/', $prn)) {
            throw new InvalidArgumentException('Please provide a valid PRN.');
        }
        if ($fullName === '' || mb_strlen($fullName) < 2) {
            throw new InvalidArgumentException('Please provide your full name.');
        }
        if (!in_array($department, $departments, true) || !in_array($branch, $branches, true)) {
            throw new InvalidArgumentException('Please select a valid department and branch.');
        }
        if ($cgpa === false || $cgpa < 0 || $cgpa > 10) {
            throw new InvalidArgumentException('CGPA must be between 0 and 10.');
        }
        if (!preg_match('/^[0-9+() .-]{10,20}$/', $phone)) {
            throw new InvalidArgumentException('Please provide a valid phone number.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please provide a valid email address.');
        }

        $skillList = array_values(array_filter(array_map('trim', explode(',', $skills))));
        if (!$skillList || strlen($skills) > 2000) {
            throw new InvalidArgumentException('Please add at least one skill.');
        }

        $resume = $_FILES['resume'] ?? null;
        $allowedResumeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $resumeExtension = strtolower(pathinfo((string) ($resume['name'] ?? ''), PATHINFO_EXTENSION));

        if (!$resume || $resume['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Please upload a resume.');
        }
        if ($resume['size'] > 5 * 1024 * 1024 || !array_key_exists($resumeExtension, $allowedResumeTypes)) {
            throw new InvalidArgumentException('Resume must be a PDF, DOC, or DOCX file up to 5MB.');
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $resumeMimeType = $fileInfo ? finfo_file($fileInfo, $resume['tmp_name']) : false;
        if ($fileInfo) {
            finfo_close($fileInfo);
        }
        if ($resumeMimeType !== $allowedResumeTypes[$resumeExtension]) {
            throw new InvalidArgumentException('The uploaded resume file type is not valid.');
        }

        $uploadDirectory = __DIR__ . '/uploads/resumes';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('The resume upload directory could not be created.');
        }

        $storedFileName = bin2hex(random_bytes(16)) . '.' . $resumeExtension;
        $uploadedResumePath = 'uploads/resumes/' . $storedFileName;
        if (!move_uploaded_file($resume['tmp_name'], $uploadDirectory . '/' . $storedFileName)) {
            throw new RuntimeException('The resume could not be uploaded.');
        }

        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
        $course = $department;
        $graduationYear = (int) date('Y');

        $statement = $conn->prepare(
            'INSERT INTO students
                (roll_number, first_name, last_name, email, phone, department, branch, skills, resume_path, course, graduation_year, cgpa)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->bind_param('ssssssssssid', $prn, $firstName, $lastName, $email, $phone, $department, $branch, $skills, $uploadedResumePath, $course, $graduationYear, $cgpa);
        $statement->execute();
        $statement->close();
        $message = 'Student registered successfully.';
    } catch (InvalidArgumentException | RuntimeException $exception) {
        if ($uploadedResumePath) {
            @unlink(__DIR__ . '/' . $uploadedResumePath);
        }
        $message = $exception->getMessage();
        $messageType = 'danger';
    } catch (mysqli_sql_exception $exception) {
        if ($uploadedResumePath) {
            @unlink(__DIR__ . '/' . $uploadedResumePath);
        }
        error_log('Student registration query failed: ' . $exception->getMessage());
        $message = $exception->getCode() === 1062
            ? 'The PRN or email address is already registered.'
            : 'The student could not be registered. Please try again.';
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Academic ERP Placement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .form-section {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(17, 42, 77, 0.06);
            border-radius: 22px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 18px 45px rgba(17, 42, 77, 0.08);
        }
        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #1e88e5;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .form-control, .form-select {
            border: 1px solid #dee2e6;
            border-radius: 14px;
            padding: 0.6rem 0.9rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
        .required-indicator {
            color: #dc3545;
            margin-left: 0.25rem;
        }
        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .btn-submit {
            padding: 0.7rem 2rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        .skills-input-group {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .skills-input-group .form-control {
            min-width: 0;
        }
        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .skill-badge {
            background: #e7f1ff;
            border: 1px solid #0d6efd;
            color: #0d6efd;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .skill-badge button {
            background: none;
            border: none;
            color: #0d6efd;
            cursor: pointer;
            padding: 0;
            font-size: 0.9rem;
        }
        .skill-badge button:hover {
            color: #dc3545;
        }
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        @media (max-width: 575px) {
            .form-section {
                padding: 1.25rem;
                border-radius: 18px;
            }
            .form-title {
                font-size: 1.1rem;
            }
            .skills-input-group .btn {
                flex: 0 0 48px;
            }
        }
    </style>
</head>
<body class="bg-light">

<?php include __DIR__ . '/includes/navbar.php'; ?>

<div class="container-fluid">
    <div class="row gx-4">
        <div class="col-12 col-lg-3">
            <?php include __DIR__ . '/includes/sidebar.php'; ?>
        </div>
        <div class="col-12 col-lg-9">
            <div class="py-4">
                <div class="mb-4">
                    <p class="text-uppercase text-primary mb-2 small fw-semibold">Student Management</p>
                    <h1 class="h3 mb-0">Student Registration</h1>
                    <p class="text-muted mb-0">Register or update your profile to participate in placement drives.</p>
                </div>

                <?php if ($message !== null): ?>
                    <div class="alert alert-<?php echo students_escape($messageType); ?> alert-dismissible fade show" role="alert">
                        <?php echo students_escape($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form class="needs-validation" id="studentForm" method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo students_escape($csrfToken); ?>">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h5 class="form-title">
                            <i class="bi bi-person-circle me-2"></i> Personal Information
                        </h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="prn" class="form-label">PRN <span class="required-indicator">*</span></label>
                                <input type="text" class="form-control" id="prn" name="prn" placeholder="e.g., 2021B101234" autocomplete="off" required>
                                <div class="invalid-feedback">
                                    Please provide a valid PRN.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Full Name <span class="required-indicator">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" autocomplete="name" minlength="2" required>
                                <div class="invalid-feedback">
                                    Please provide your full name.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email Address <span class="required-indicator">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="your.email@example.com" autocomplete="email" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">Phone Number <span class="required-indicator">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+91 9876543210" autocomplete="tel" pattern="[0-9+() .-]{10,}" required>
                                <div class="invalid-feedback">
                                    Please provide a valid phone number.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information Section -->
                    <div class="form-section">
                        <h5 class="form-title">
                            <i class="bi bi-book me-2"></i> Academic Information
                        </h5>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="department" class="form-label">Department <span class="required-indicator">*</span></label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="CSE">Computer Science & Engineering</option>
                                    <option value="IT">Information Technology</option>
                                    <option value="ECE">Electronics & Communication</option>
                                    <option value="ME">Mechanical Engineering</option>
                                    <option value="CE">Civil Engineering</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a department.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="branch" class="form-label">Branch <span class="required-indicator">*</span></label>
                                <select class="form-select" id="branch" name="branch" required>
                                    <option value="">Select Branch</option>
                                    <option value="A">Branch A</option>
                                    <option value="B">Branch B</option>
                                    <option value="C">Branch C</option>
                                    <option value="D">Branch D</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a branch.
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="cgpa" class="form-label">CGPA <span class="required-indicator">*</span></label>
                                <input type="number" class="form-control" id="cgpa" name="cgpa" placeholder="e.g., 8.5" min="0" max="10" step="0.01" inputmode="decimal" required>
                                <small class="form-text">Out of 10</small>
                                <div class="invalid-feedback">
                                    Please provide a valid CGPA.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information Section -->
                    <div class="form-section">
                        <h5 class="form-title">
                            <i class="bi bi-briefcase me-2"></i> Professional Information
                        </h5>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="skillsInput" class="form-label">Skills <span class="required-indicator">*</span></label>
                                <div class="skills-input-group">
                                    <input type="text" class="form-control" id="skillsInput" placeholder="Enter a skill and press Enter (e.g., Python, Java, React)" autocomplete="off">
                                    <button type="button" class="btn btn-outline-primary" id="addSkillBtn" aria-label="Add skill">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                <input type="hidden" id="skills" name="skills">
                                <div class="skills-list" id="skillsList"></div>
                                <div class="invalid-feedback d-none" id="skillsError">
                                    Please add at least one skill.
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="resume" class="form-label">Resume <span class="required-indicator">*</span></label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                                <small class="form-text">Accepted formats: PDF, DOC, DOCX (Max 5MB)</small>
                                <div class="invalid-feedback">
                                    Please upload a resume.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="bi bi-check-circle me-2"></i> Register
                        </button>
                        <button type="reset" class="btn btn-outline-secondary btn-submit">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Clear Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Skills management
    let skills = [];
    const skillsInput = document.getElementById('skillsInput');
    const addSkillBtn = document.getElementById('addSkillBtn');
    const skillsList = document.getElementById('skillsList');
    const skillsField = document.getElementById('skills');
    const form = document.getElementById('studentForm');

    function addSkill(skillText) {
        if (skillText.trim() && !skills.includes(skillText.trim())) {
            skills.push(skillText.trim());
            renderSkills();
            skillsInput.value = '';
            skillsInput.focus();
        }
    }

    function removeSkill(skill) {
        skills = skills.filter(s => s !== skill);
        renderSkills();
    }

    function renderSkills() {
        skillsList.replaceChildren(...skills.map(skill => {
            const badge = document.createElement('div');
            const label = document.createElement('span');
            const removeButton = document.createElement('button');
            const icon = document.createElement('i');

            badge.className = 'skill-badge';
            label.textContent = skill;
            removeButton.type = 'button';
            removeButton.className = 'remove-skill';
            removeButton.dataset.skill = skill;
            removeButton.setAttribute('aria-label', `Remove ${skill}`);
            icon.className = 'bi bi-x-lg';
            removeButton.appendChild(icon);
            badge.append(label, removeButton);
            return badge;
        }));
        skillsField.value = skills.join(', ');
        skillsField.setCustomValidity(skills.length ? '' : 'Add at least one skill.');
    }

    addSkillBtn.addEventListener('click', () => {
        addSkill(skillsInput.value);
    });

    skillsInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addSkill(skillsInput.value);
        }
    });

    skillsList.addEventListener('click', (e) => {
        const removeButton = e.target.closest('.remove-skill');
        if (removeButton) {
            removeSkill(removeButton.dataset.skill);
        }
    });

    // Form validation
    form.addEventListener('submit', (e) => {
        form.classList.add('was-validated');

        if (!form.checkValidity()) {
            e.stopPropagation();
            document.getElementById('skillsError').classList.toggle('d-none', skills.length > 0);
            return;
        }

        if (form.checkValidity() && skills.length > 0) {
            alert('Form submitted successfully!');
        }
    });

    form.addEventListener('reset', () => {
        skills = [];
        renderSkills();
        form.classList.remove('was-validated');
        document.getElementById('skillsError').classList.add('d-none');
    });

    document.getElementById('resume').addEventListener('change', (e) => {
        const file = e.target.files[0];
        const maxSize = 5 * 1024 * 1024;
        e.target.setCustomValidity(file && file.size > maxSize ? 'Resume must be 5MB or smaller.' : '');
    });

    // Remove validation class when user starts typing
    const inputs = form.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            if (input.value) {
                input.classList.remove('is-invalid');
            }
        });
    });
</script>
</body>
</html>
