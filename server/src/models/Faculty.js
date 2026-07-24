import mongoose from 'mongoose';

const facultySchema = new mongoose.Schema({
  facultyId: { type: String, required: true, unique: true, index: true },
  name: { type: String, required: true, trim: true },
  email: { type: String, required: true, lowercase: true, trim: true },
  phone: { type: String, trim: true, default: '' },
  designation: { type: String, default: 'Assistant Professor' },
  department: { type: String, default: 'Artificial Intelligence & Machine Learning' },
  qualification: { type: String, default: '' },
  specialization: { type: String, default: '' },
  experience: { type: Number, min: 0, default: 0 },
  researchInterests: { type: String, default: '' },
  publications: { type: String, default: '' },
  officeLocation: { type: String, default: '' },
  profileImage: { type: String, default: '' }
}, { timestamps: true });

export default mongoose.model('Faculty', facultySchema);