import mongoose from 'mongoose';

const allocationSchema = new mongoose.Schema({
  facultyId: { type: String, required: true, index: true },
  courseCode: { type: String, required: true, trim: true },
  courseName: { type: String, required: true, trim: true },
  semester: { type: Number, required: true, min: 1, max: 8 },
  academicYear: { type: String, required: true },
  section: { type: String, required: true, trim: true },
  credits: { type: Number, required: true, min: 0 },
  lectureHours: { type: Number, required: true, min: 0 },
  practicalHours: { type: Number, required: true, min: 0 },
  status: { type: String, enum: ['Active', 'Archived'], default: 'Active' }
}, { timestamps: true });

export default mongoose.model('CourseAllocation', allocationSchema);