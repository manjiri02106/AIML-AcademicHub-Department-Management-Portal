/* ==========================================================================
   AIML AcademicHub Department Management Portal - Data Store
   Initial Data Models & Mock Datasets for AIML Department Portal
   ========================================================================== */

const initialPortalData = {
  faculty: {
    id: "FAC-AIML-101",
    name: "Dr. A. Sharma",
    designation: "Associate Professor & HOD",
    department: "Artificial Intelligence & Machine Learning",
    email: "a.sharma@academichub.edu",
    phone: "+91 98765 43210",
    avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=250",
    currentAcademicYear: "2026-2027",
    currentSemester: "Semester VI (BE AIML)"
  },

  summaryMetrics: {
    totalSubjects: 6,
    todaysClasses: 4,
    pendingAttendance: 2,
    pendingMarksUpload: 1
  },

  students: [
    { rollNo: "AIML2401", name: "Aarav Patel", gender: "M", status: "present", remarks: "", ut1: 18, ut2: 19, assignment: 24, practical: 48, endSem: 88 },
    { rollNo: "AIML2402", name: "Ananya Iyer", gender: "F", status: "present", remarks: "", ut1: 20, ut2: 20, assignment: 25, practical: 50, endSem: 95 },
    { rollNo: "AIML2403", name: "Aditya Verma", gender: "M", status: "present", remarks: "", ut1: 15, ut2: 16, assignment: 22, practical: 42, endSem: 76 },
    { rollNo: "AIML2404", name: "Bhavya Shah", gender: "F", status: "absent", remarks: "Medical Leave", ut1: 12, ut2: 14, assignment: 20, practical: 38, endSem: 64 },
    { rollNo: "AIML2405", name: "Devendra Kulkarni", gender: "M", status: "present", remarks: "", ut1: 19, ut2: 18, assignment: 24, practical: 46, endSem: 85 },
    { rollNo: "AIML2406", name: "Divya Deshmukh", gender: "F", status: "late", remarks: "Transport delay", ut1: 14, ut2: 15, assignment: 21, practical: 40, endSem: 72 },
    { rollNo: "AIML2407", name: "Eshaan Nair", gender: "M", status: "present", remarks: "", ut1: 17, ut2: 17, assignment: 23, practical: 44, endSem: 81 },
    { rollNo: "AIML2408", name: "Gautam Joshi", gender: "M", status: "present", remarks: "", ut1: 16, ut2: 18, assignment: 22, practical: 45, endSem: 80 },
    { rollNo: "AIML2409", name: "Ishita Roy", gender: "F", status: "present", remarks: "", ut1: 19, ut2: 20, assignment: 25, practical: 49, endSem: 92 },
    { rollNo: "AIML2410", name: "Kabir Mehta", gender: "M", status: "absent", remarks: "Sports Event", ut1: 11, ut2: 13, assignment: 18, practical: 35, endSem: 58 },
    { rollNo: "AIML2411", name: "Kavya Reddy", gender: "F", status: "present", remarks: "", ut1: 18, ut2: 19, assignment: 24, practical: 47, endSem: 87 },
    { rollNo: "AIML2412", name: "Manish Agarwal", gender: "M", status: "present", remarks: "", ut1: 15, ut2: 16, assignment: 21, practical: 42, endSem: 74 },
    { rollNo: "AIML2413", name: "Neha Gupta", gender: "F", status: "present", remarks: "", ut1: 20, ut2: 19, assignment: 25, practical: 49, endSem: 94 },
    { rollNo: "AIML2414", name: "Omkar Jadhav", gender: "M", status: "late", remarks: "", ut1: 13, ut2: 14, assignment: 20, practical: 39, endSem: 68 },
    { rollNo: "AIML2415", name: "Pooja Hegde", gender: "F", status: "present", remarks: "", ut1: 17, ut2: 18, assignment: 23, practical: 45, endSem: 83 },
    { rollNo: "AIML2416", name: "Pranav Rao", gender: "M", status: "present", remarks: "", ut1: 16, ut2: 17, assignment: 22, practical: 43, endSem: 79 },
    { rollNo: "AIML2417", name: "Rohan Saxena", gender: "M", status: "present", remarks: "", ut1: 18, ut2: 19, assignment: 24, practical: 46, endSem: 86 },
    { rollNo: "AIML2418", name: "Riya Sen", gender: "F", status: "present", remarks: "", ut1: 19, ut2: 19, assignment: 24, practical: 48, endSem: 90 },
    { rollNo: "AIML2419", name: "Siddharth Banerjee", gender: "M", status: "present", remarks: "", ut1: 17, ut2: 16, assignment: 23, practical: 44, endSem: 82 },
    { rollNo: "AIML2420", name: "Tanvi More", gender: "F", status: "present", remarks: "", ut1: 18, ut2: 20, assignment: 25, practical: 47, endSem: 89 }
  ],

  subjects: [
    {
      id: "SUB-601",
      code: "AIML-601",
      name: "Deep Learning & Neural Networks",
      semester: "Sem VI",
      credits: 4,
      faculty: "Dr. A. Sharma",
      lectureHours: 3,
      practicalHours: 2,
      enrolledCount: 64,
      avgAttendance: "88%"
    },
    {
      id: "SUB-602",
      code: "AIML-602",
      name: "Natural Language Processing",
      semester: "Sem VI",
      credits: 4,
      faculty: "Prof. S. Rane",
      lectureHours: 3,
      practicalHours: 2,
      enrolledCount: 64,
      avgAttendance: "92%"
    },
    {
      id: "SUB-603",
      code: "AIML-603",
      name: "Computer Vision & Image AI",
      semester: "Sem VI",
      credits: 3,
      faculty: "Dr. A. Sharma",
      lectureHours: 3,
      practicalHours: 2,
      enrolledCount: 64,
      avgAttendance: "85%"
    },
    {
      id: "SUB-604",
      code: "AIML-604",
      name: "Reinforcement Learning",
      semester: "Sem VI",
      credits: 3,
      faculty: "Prof. V. Deshpande",
      lectureHours: 3,
      practicalHours: 0,
      enrolledCount: 64,
      avgAttendance: "90%"
    },
    {
      id: "SUB-605",
      code: "AIML-605",
      name: "Generative AI & LLMs",
      semester: "Sem VI",
      credits: 4,
      faculty: "Dr. A. Sharma",
      lectureHours: 3,
      practicalHours: 2,
      enrolledCount: 64,
      avgAttendance: "94%"
    },
    {
      id: "SUB-606",
      code: "AIML-606",
      name: "AI Ethics & Governance",
      semester: "Sem VI",
      credits: 2,
      faculty: "Prof. R. Bhattacharya",
      lectureHours: 2,
      practicalHours: 0,
      enrolledCount: 64,
      avgAttendance: "86%"
    }
  ],

  courseFiles: [
    {
      id: "FILE-101",
      title: "Unit 3 - Transformer Architecture & Attention Mechanism Notes",
      category: "Notes",
      fileType: "pdf",
      subject: "Deep Learning & Neural Networks",
      semester: "Sem VI",
      uploadDate: "2026-07-20",
      fileSize: "4.2 MB",
      downloads: 142
    },
    {
      id: "FILE-102",
      title: "Lab Manual - OpenCV & PyTorch Image Segmentation Experiments",
      category: "Lab Manual",
      fileType: "pdf",
      subject: "Computer Vision & Image AI",
      semester: "Sem VI",
      uploadDate: "2026-07-18",
      fileSize: "8.5 MB",
      downloads: 98
    },
    {
      id: "FILE-103",
      title: "Lecture Presentation - Fine-Tuning Llama-3 & Quantization PPT",
      category: "PPT",
      fileType: "ppt",
      subject: "Generative AI & LLMs",
      semester: "Sem VI",
      uploadDate: "2026-07-22",
      fileSize: "15.1 MB",
      downloads: 215
    },
    {
      id: "FILE-104",
      title: "Unit Test 1 Question Bank & Solution Keys 2026",
      category: "Question Bank",
      fileType: "pdf",
      subject: "Natural Language Processing",
      semester: "Sem VI",
      uploadDate: "2026-07-12",
      fileSize: "2.8 MB",
      downloads: 310
    },
    {
      id: "FILE-105",
      title: "Assignment 2 - Q-Learning Algorithm Code Template",
      category: "Assignments",
      fileType: "zip",
      subject: "Reinforcement Learning",
      semester: "Sem VI",
      uploadDate: "2026-07-15",
      fileSize: "1.4 MB",
      downloads: 84
    },
    {
      id: "FILE-106",
      title: "Previous Year University Question Papers (2023 - 2025)",
      category: "Previous Papers",
      fileType: "pdf",
      subject: "Deep Learning & Neural Networks",
      semester: "Sem VI",
      uploadDate: "2026-07-05",
      fileSize: "12.0 MB",
      downloads: 450
    }
  ],

  notices: [
    {
      id: "NOT-001",
      title: "Submission Deadline: Unit Test 1 Marks Portal Closing on July 28",
      category: "Exam",
      priority: "urgent",
      date: "2026-07-24",
      author: "Exam Cell AIML",
      pinned: true,
      content: "All AIML faculty members are requested to complete the Unit Test 1 marks upload and publication by July 28, 5:00 PM without fail."
    },
    {
      id: "NOT-002",
      title: "Expert Guest Lecture on Agentic AI Systems by Google DeepMind Researcher",
      category: "Workshop",
      priority: "academic",
      date: "2026-07-22",
      author: "Dept. Head",
      pinned: true,
      content: "An offline workshop on Autonomous Coding Agents will be held in Auditorium 2 on July 30 from 10:00 AM to 1:00 PM for Sem VI & VIII students."
    },
    {
      id: "NOT-003",
      title: "Mid-Term Attendance Audit Notification (< 75% Defaulter List)",
      category: "Academic",
      priority: "warning",
      date: "2026-07-19",
      author: "Academic Coordinator",
      pinned: false,
      content: "Faculty batch in-charges must generate and sign the monthly defaulters list by Friday."
    }
  ],

  recentActivities: [
    { type: "attendance", text: "Marked attendance for Sem VI - Deep Learning (Div A)", time: "10 mins ago", icon: "fa-user-check", bg: "var(--accent-blue)", color: "var(--primary-blue)" },
    { type: "file", text: "Uploaded 'Fine-Tuning Llama-3 PPT' to Generative AI repository", time: "1 hour ago", icon: "fa-file-upload", bg: "var(--success-bg)", color: "var(--success-color)" },
    { type: "marks", text: "Published Unit Test 1 Marks for Computer Vision", time: "3 hours ago", icon: "fa-award", bg: "var(--warning-bg)", color: "var(--warning-color)" },
    { type: "notice", text: "Posted Notice regarding Expert Guest Lecture on Agentic AI", time: "Yesterday", icon: "fa-bullhorn", bg: "var(--info-bg)", color: "var(--info-color)" }
  ],

  upcomingLectures: [
    { subject: "Deep Learning (AIML-601)", time: "01:30 PM - 02:30 PM", room: "Lab 402", batch: "Div A (B1)" },
    { subject: "Generative AI & LLMs (AIML-605)", time: "03:00 PM - 04:00 PM", room: "LH-301", batch: "Div A (All)" },
    { subject: "Computer Vision Practical", time: "04:15 PM - 06:15 PM", room: "AI GPU Lab", batch: "Div B (B2)" }
  ]
};
