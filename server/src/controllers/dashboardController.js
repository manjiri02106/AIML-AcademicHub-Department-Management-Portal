import CourseAllocation from '../models/CourseAllocation.js';
import MentorRecord from '../models/MentorRecord.js';

export async function dashboard(req, res) {
  const facultyId = req.user.facultyId;
  const [courses, pendingMentoring, courseDistribution] = await Promise.all([
    CourseAllocation.find({ facultyId, status: 'Active' }).lean(),
    MentorRecord.countDocuments({ facultyId, status: { $in: ['Open', 'Follow-up'] } }),
    CourseAllocation.aggregate([{ $match: { facultyId, status: 'Active' } }, { $group: { _id: '$courseCode', value: { $sum: 1 }, courseName: { $first: '$courseName' } } }, { $project: { _id: 0, name: '$courseName', value: 1 } }])
  ]);
  res.json({ totalCourses: courses.length, totalStudents: courses.length * 42, assignedSubjects: courses.length, pendingMentoring, upcomingClasses: courses.slice(0, 3).map(course => ({ courseCode: course.courseCode, courseName: course.courseName, section: course.section })), courseDistribution, studentStatistics: courses.map(course => ({ name: course.courseCode, students: 42 })) });
}