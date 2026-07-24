import 'dotenv/config';
import { connectDatabase } from './config/db.js';
import CourseAllocation from './models/CourseAllocation.js';
import Faculty from './models/Faculty.js';
import User from './models/User.js';

await connectDatabase();
const facultyId = 'FAC-001';
await User.deleteMany({ email: 'faculty@aiml.edu' });
await Faculty.deleteMany({ facultyId });
await User.create({ facultyId, name: 'Dr. Alan Turing', email: 'faculty@aiml.edu', password: 'password', role: 'Faculty' });
await Faculty.create({ facultyId, name: 'Dr. Alan Turing', email: 'faculty@aiml.edu', phone: '+91 98765 43210', designation: 'Associate Professor', qualification: 'Ph.D. in Computer Science', specialization: 'Machine Learning and Computer Vision', experience: 9, researchInterests: 'Responsible AI, computer vision, applied deep learning', publications: '18 peer-reviewed publications', officeLocation: 'AIML Block · Room 204' });
await CourseAllocation.deleteMany({ facultyId });
await CourseAllocation.insertMany([{ facultyId, courseCode: 'AIML301', courseName: 'Deep Learning', semester: 5, academicYear: '2024-25', section: 'A', credits: 4, lectureHours: 3, practicalHours: 2 }, { facultyId, courseCode: 'AIML203', courseName: 'Data Structures', semester: 3, academicYear: '2024-25', section: 'B', credits: 4, lectureHours: 3, practicalHours: 2 }, { facultyId, courseCode: 'AIML405', courseName: 'Computer Vision', semester: 7, academicYear: '2024-25', section: 'A', credits: 3, lectureHours: 3, practicalHours: 0 }]);
console.log('Seed complete. Login: faculty@aiml.edu / password');
process.exit(0);