import CourseAllocation from '../models/CourseAllocation.js';

export async function listCourses(req, res) {
  const { search = '', semester, page = 1, limit = 8 } = req.query;
  const query = req.user.role === 'Faculty' ? { facultyId: req.user.facultyId } : {};
  if (semester) query.semester = Number(semester);
  if (search) query.$or = [{ courseCode: new RegExp(search, 'i') }, { courseName: new RegExp(search, 'i') }, { section: new RegExp(search, 'i') }];
  const skip = (Number(page) - 1) * Number(limit);
  const [items, total] = await Promise.all([CourseAllocation.find(query).sort({ semester: 1, courseCode: 1 }).skip(skip).limit(Number(limit)), CourseAllocation.countDocuments(query)]);
  res.json({ items, total, page: Number(page), pages: Math.ceil(total / Number(limit)) });
}

export async function createCourse(req, res) { res.status(201).json(await CourseAllocation.create(req.body)); }
export async function updateCourse(req, res) { res.json(await CourseAllocation.findByIdAndUpdate(req.params.id, req.body, { new: true, runValidators: true })); }
export async function deleteCourse(req, res) { await CourseAllocation.findByIdAndDelete(req.params.id); res.status(204).send(); }