import mongoose from 'mongoose';

const mentorRecordSchema = new mongoose.Schema({
  facultyId: { type: String, required: true, index: true },
  studentId: { type: String, required: true, trim: true },
  meetingDate: { type: Date, required: true },
  discussion: { type: String, required: true },
  issues: { type: String, default: '' },
  suggestions: { type: String, default: '' },
  followUpDate: { type: Date },
  status: { type: String, enum: ['Open', 'Follow-up', 'Resolved'], default: 'Open' },
  remarks: { type: String, default: '' }
}, { timestamps: true });

export default mongoose.model('MentorRecord', mentorRecordSchema);