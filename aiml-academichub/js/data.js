/* AIML ACADEMICHUB - Central Database Engine Mock Data */

window.AcademicHubData = {
  department: {
    name: "Artificial Intelligence & Machine Learning (AIML)",
    code: "AIML",
    established: 2020,
    hod: "Dr. Sarah Jenkins, Ph.D. (IIT Bombay)",
    totalStudents: 240,
    totalFaculty: 14,
    phdFaculty: 9,
    gpuClusters: 3,
    nbaStatus: "Tier-I Accredited (Score 785/1000)",
    naacGrade: "A++ (CGPA 3.78)"
  },

  faculty: [
    { id: "FAC-AI-01", name: "Dr. Sarah Jenkins", designation: "Professor & HOD", qualification: "Ph.D. Computer Vision", experience: "18 Yrs", workload: 14, mentoringCount: 18, pubCount: 24, grants: "₹45.0 Lakhs", email: "sarah.jenkins@aiml.edu" },
    { id: "FAC-AI-02", name: "Prof. Alan Turing", designation: "Associate Professor", qualification: "M.Tech, Ph.D.* (Deep Learning)", experience: "12 Yrs", workload: 16, mentoringCount: 20, pubCount: 16, grants: "₹18.5 Lakhs", email: "alan.turing@aiml.edu" },
    { id: "FAC-AI-03", name: "Dr. Geoffrey Hinton", designation: "Professor", qualification: "Ph.D. Neural Networks", experience: "22 Yrs", workload: 12, mentoringCount: 15, pubCount: 38, grants: "₹65.0 Lakhs", email: "geoffrey.hinton@aiml.edu" },
    { id: "FAC-AI-04", name: "Prof. Andrew Ng", designation: "Assistant Professor", qualification: "M.Tech AI & Data Science", experience: "8 Yrs", workload: 18, mentoringCount: 22, pubCount: 11, grants: "₹12.0 Lakhs", email: "andrew.ng@aiml.edu" },
    { id: "FAC-AI-05", name: "Dr. Fei-Fei Li", designation: "Professor", qualification: "Ph.D. Spatial Intelligence", experience: "15 Yrs", workload: 14, mentoringCount: 18, pubCount: 29, grants: "₹50.0 Lakhs", email: "feifei.li@aiml.edu" },
    { id: "FAC-AI-06", name: "Prof. Yann LeCun", designation: "Assistant Professor", qualification: "M.Tech Pattern Recognition", experience: "7 Yrs", workload: 18, mentoringCount: 20, pubCount: 9, grants: "₹8.0 Lakhs", email: "yann.lecun@aiml.edu" }
  ],

  students: [
    { usn: "1VA21AI001", name: "Aarav Sharma", sem: 7, div: "A", gpa: 9.42, attendance: 92, backlogs: 0, status: "Placed", company: "NVIDIA (₹44 LPA)", guide: "Dr. Sarah Jenkins", project: "Real-time Autonomous Edge Vision on TensorRT" },
    { usn: "1VA21AI002", name: "Ananya Rao", sem: 7, div: "A", gpa: 9.15, attendance: 88, backlogs: 0, status: "Placed", company: "Google AI (₹52 LPA)", guide: "Dr. Geoffrey Hinton", project: "Multi-Modal Transformer for Medical Image Segmentation" },
    { usn: "1VA21AI003", name: "Rohan Verma", sem: 7, div: "A", gpa: 8.65, attendance: 84, backlogs: 0, status: "Placed", company: "Microsoft (₹38 LPA)", guide: "Prof. Alan Turing", project: "LLM-based Automated Code Refactoring & Security Audit" },
    { usn: "1VA21AI004", name: "Sneha Kulkarni", sem: 7, div: "A", gpa: 7.20, attendance: 68, backlogs: 1, status: "Seeking", company: "-", guide: "Prof. Andrew Ng", project: "Reinforcement Learning for Autonomous Drone Navigation" },
    { usn: "1VA21AI005", name: "Vikramaditya Nair", sem: 7, div: "B", gpa: 8.90, attendance: 95, backlogs: 0, status: "Placed", company: "Amazon AWS (₹32 LPA)", guide: "Dr. Fei-Fei Li", project: "Scalable MLOps Pipeline for Real-time Fraud Detection" },
    { usn: "1VA21AI006", name: "Priya Sundaram", sem: 7, div: "B", gpa: 9.60, attendance: 96, backlogs: 0, status: "Placed", company: "Apple (₹48 LPA)", guide: "Dr. Sarah Jenkins", project: "On-Device Neural Architecture Search for Mobile Vision" },
    { usn: "1VA21AI007", name: "Karthik Mehta", sem: 5, div: "A", gpa: 8.10, attendance: 71, backlogs: 0, status: "Interning", company: "Intel Labs", guide: "Prof. Yann LeCun", project: "Neuromorphic Computing Simulators" },
    { usn: "1VA21AI008", name: "Divya Nambiar", sem: 5, div: "B", gpa: 8.85, attendance: 89, backlogs: 0, status: "Interning", company: "Samsung R&D", guide: "Dr. Fei-Fei Li", project: "Generative Adversarial Voice Conversion" }
  ],

  academics: {
    courses: [
      { code: "21AI71", title: "Deep Learning Architectures", sem: 7, credits: 4, faculty: "Dr. Geoffrey Hinton", passPercentage: 96.5, avgMarks: 82.4 },
      { code: "21AI72", title: "Computer Vision & Video Analytics", sem: 7, credits: 4, faculty: "Dr. Sarah Jenkins", passPercentage: 94.2, avgMarks: 79.8 },
      { code: "21AI73", title: "Natural Language Processing & LLMs", sem: 7, credits: 3, faculty: "Prof. Alan Turing", passPercentage: 91.0, avgMarks: 76.5 },
      { code: "21AI51", title: "Machine Learning Algorithms", sem: 5, credits: 4, faculty: "Prof. Andrew Ng", passPercentage: 89.5, avgMarks: 74.2 },
      { code: "21AI52", title: "MLOps & Cloud Infrastructure", sem: 5, credits: 3, faculty: "Dr. Fei-Fei Li", passPercentage: 95.0, avgMarks: 81.0 }
    ],
    coPoMatrix: [
      { course: "21AI71 Deep Learning", co: "CO1: Formulate CNNs & RNNs", po1: 3, po2: 3, po3: 2, po4: 3, po5: 3, pso1: 3, pso2: 2, attainment: "2.85 (95%)" },
      { course: "21AI71 Deep Learning", co: "CO2: Implement Transformers", po1: 3, po2: 3, po3: 3, po4: 2, po5: 3, pso1: 3, pso2: 3, attainment: "2.72 (91%)" },
      { course: "21AI72 Computer Vision", co: "CO1: Object Detection YOLOv8", po1: 3, po2: 2, po3: 3, po4: 3, po5: 3, pso1: 3, pso2: 2, attainment: "2.90 (96%)" },
      { course: "21AI73 Natural Language", co: "CO1: Fine-tune LLaMA 3", po1: 3, po2: 3, po3: 2, po4: 2, po5: 3, pso1: 3, pso2: 3, attainment: "2.68 (89%)" }
    ]
  },

  projects: [
    { title: "Real-time Autonomous Edge Vision on TensorRT", team: ["1VA21AI001", "1VA21AI006"], guide: "Dr. Sarah Jenkins", domain: "Computer Vision", status: "Approved", phase1: 95, phase2: 94, phase3: 98 },
    { title: "Multi-Modal Transformer for Medical Image Segmentation", team: ["1VA21AI002"], guide: "Dr. Geoffrey Hinton", domain: "Medical AI", status: "Approved", phase1: 96, phase2: 98, phase3: 97 },
    { title: "LLM-based Automated Code Refactoring & Security Audit", team: ["1VA21AI003"], guide: "Prof. Alan Turing", domain: "Generative AI", status: "Approved", phase1: 88, phase2: 90, phase3: 92 },
    { title: "Reinforcement Learning for Autonomous Drone Navigation", team: ["1VA21AI004"], guide: "Prof. Andrew Ng", domain: "Robotics & RL", status: "Review Needed", phase1: 72, phase2: 70, phase3: 75 },
    { title: "Scalable MLOps Pipeline for Real-time Fraud Detection", team: ["1VA21AI005"], guide: "Dr. Fei-Fei Li", domain: "Cloud MLOps", status: "Approved", phase1: 92, phase2: 91, phase3: 94 }
  ],

  placements: [
    { company: "NVIDIA", tier: "Dream Super", ctc: "44.0 LPA", offers: 3, roles: "AI Systems Engineer" },
    { company: "Google AI", tier: "Dream Super", ctc: "52.0 LPA", offers: 2, roles: "Research Scientist" },
    { company: "Microsoft Research", tier: "Dream Super", ctc: "38.0 LPA", offers: 4, roles: "Applied ML Scientist" },
    { company: "Amazon AWS", tier: "Dream", ctc: "32.0 LPA", offers: 6, roles: "Cloud AI Solutions Architect" },
    { company: "Intel Labs", tier: "Dream", ctc: "24.0 LPA", offers: 5, roles: "Edge AI Engineer" },
    { company: "Samsung R&D", tier: "Core", ctc: "18.5 LPA", offers: 8, roles: "Vision Algorithm Engineer" }
  ],

  research: {
    publications: [
      { title: "Optimizing Vision Transformers for Embedded AI Accelerators", authors: "Dr. Sarah Jenkins, Aarav Sharma", journal: "IEEE Transactions on Pattern Analysis and Machine Intelligence (TPAMI)", impactFactor: "23.6", indexed: "Scopus / SCI", year: 2025 },
      { title: "Generative Diffusion Models for High-Resolution MRI Super-Resolution", authors: "Dr. Geoffrey Hinton, Ananya Rao", journal: "NeurIPS 2024 Workshop on Deep Learning in Medical Imaging", impactFactor: "A* Conference", indexed: "Scopus", year: 2024 },
      { title: "Zero-Shot Code Generation using Quantized 8-Bit LLMs", authors: "Prof. Alan Turing, Rohan Verma", journal: "ACM Transactions on Software Engineering and Methodology", impactFactor: "4.8", indexed: "Scopus / WoS", year: 2024 }
    ],
    patents: [
      { title: "Real-time Autonomous Edge AI Vision System for Precision Agriculture", applicationNo: "202441098231 A", inventors: "Dr. Sarah Jenkins, Priya Sundaram", status: "Published", filingDate: "2024-03-15" },
      { title: "Distributed Privacy-Preserving Federated Learning Engine for Healthcare", applicationNo: "202341077120 B", inventors: "Dr. Geoffrey Hinton, Dr. Fei-Fei Li", status: "Granted", filingDate: "2023-09-10" }
    ],
    grants: [
      { agency: "DST-SERB (Govt. of India)", title: "Edge-AI Hardware Acceleration for Medical Diagnostics", amount: "₹45,00,000", status: "Active (2024-2027)", pi: "Dr. Sarah Jenkins" },
      { agency: "ISRO Space Applications Centre", title: "Autonomous Terrain Mapping using Satellite Hyperspectral AI", amount: "₹38,50,000", status: "Active (2023-2026)", pi: "Dr. Fei-Fei Li" }
    ]
  },

  labs: [
    { name: "Deep Learning & Supercomputing Center", location: "Lab 401 (3rd Floor)", assets: "NVIDIA DGX H100 Server (8x H100 80GB), 30x RTX 4090 Workstations", capacity: 35, utilization: "94.2%", status: "Operational" },
    { name: "Computer Vision & Robotics Lab", location: "Lab 402 (3rd Floor)", assets: "TurtleBot 4, Intel RealSense D435i Camera Array, Jetson Orin AGX Kits", capacity: 30, utilization: "88.6%", status: "Operational" },
    { name: "NLP & Conversational AI Lab", location: "Lab 403 (3rd Floor)", assets: "35x High-End Workstations (Core i9, 64GB RAM, RTX 3090)", capacity: 35, utilization: "91.0%", status: "Operational" }
  ],

  events: [
    { title: "National Workshop on MLOps & Large Language Model Fine-Tuning", type: "Workshop", date: "2025-02-14", participants: 180, coordinator: "Prof. Alan Turing", speaker: "Dr. Pradeep Kumar (NVIDIA AI Lead)" },
    { title: "AIML Hackathon 2025: GenAI for Healthcare", type: "Hackathon", date: "2025-01-20", participants: 240, coordinator: "Prof. Andrew Ng", sponsor: "Microsoft Azure" },
    { title: "International Conference on Advances in Autonomous Systems (ICAAS)", type: "Conference", date: "2024-11-12", participants: 320, coordinator: "Dr. Sarah Jenkins", publisher: "IEEE Xplore" }
  ],

  accreditation: {
    nba: {
      criteria: [
        { id: 1, title: "Vision, Mission & Program Educational Objectives (PEOs)", maxMarks: 50, obtained: 46, status: "Verified", details: "Curriculum aligned with IEEE/ACM guidelines & industry PEOs." },
        { id: 2, title: "Program Curriculum & Teaching-Learning Processes", maxMarks: 100, obtained: 92, status: "Verified", details: "CO-PO attainment calculation active for 100% of courses." },
        { id: 3, title: "Course Outcomes & Program Outcomes (CO-PO Attainment)", maxMarks: 175, obtained: 161, status: "Verified", details: "Direct & Indirect attainment meets target threshold 2.5/3.0." },
        { id: 4, title: "Students' Performance (Pass %, Placements, Higher Studies)", maxMarks: 100, obtained: 94, status: "Verified", details: "92% placement rate with average CTC ₹16.8 LPA." },
        { id: 5, title: "Faculty Contributions (Cadre Ratio, Ph.D., Research Output)", maxMarks: 200, obtained: 182, status: "Verified", details: "Faculty retention rate 95%, 64% holding Ph.D. degree." },
        { id: 6, title: "Facilities & Technical Support (GPU Cluster Infrastructure)", maxMarks: 80, obtained: 78, status: "Verified", details: "NVIDIA DGX H100 cluster & 3 modern dedicated AIML labs." },
        { id: 7, title: "Continuous Improvement in Student Learning", maxMarks: 75, obtained: 70, status: "Verified", details: "Feedback action taken reports & remedial classes for defaulters." }
      ],
      evidences: [
        { id: "EVID-NBA-01", criterion: "Criterion 2", docName: "Course_Articulation_Matrix_Deep_Learning_21AI71.pdf", size: "2.4 MB", type: "PDF", status: "Verified", uploadedBy: "Dr. Geoffrey Hinton" },
        { id: "EVID-NBA-02", criterion: "Criterion 4", docName: "Placement_Statistics_and_Offer_Letters_2024-25.pdf", size: "14.8 MB", type: "PDF", status: "Verified", uploadedBy: "Prof. Andrew Ng" },
        { id: "EVID-NBA-03", criterion: "Criterion 5", docName: "IEEE_Scopus_Publication_Certificates_2024.pdf", size: "8.1 MB", type: "PDF", status: "Verified", uploadedBy: "Dr. Sarah Jenkins" },
        { id: "EVID-NBA-04", criterion: "Criterion 6", docName: "NVIDIA_DGX_H100_Invoice_and_Asset_Register.pdf", size: "3.5 MB", type: "PDF", status: "Verified", uploadedBy: "Prof. Yann LeCun" }
      ]
    },
    naac: {
      cgpa: 3.78,
      grade: "A++",
      criteriaCount: 7
    }
  }
};
