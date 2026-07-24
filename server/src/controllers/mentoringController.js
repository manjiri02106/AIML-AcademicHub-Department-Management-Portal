import MentorRecord from '../models/MentorRecord.js';

export async function listRecords(req, res) {
  const { search = '', status, page = 1, limit = 8 } = req.query;
  const query = { facultyId: req.user.facultyId };
  if (status) query.status = status;
  if (search) query.studentId = new RegExp(search, 'i');
  const skip = (Number(page) - 1) * Number(limit);
  const [items, total] = await Promise.all([MentorRecord.find(query).sort({ meetingDate: -1 }).skip(skip).limit(Number(limit)), MentorRecord.countDocuments(query)]);
  res.json({ items, total, page: Number(page), pages: Math.ceil(total / Number(limit)) });
}

export async function getRecord(req, res) { res.json(await MentorRecord.findOne({ _id: req.params.id, facultyId: req.user.facultyId })); }
export async function createRecord(req, res) { res.status(201).json(await MentorRecord.create({ ...req.body, facultyId: req.user.facultyId })); }
export async function updateRecord(req, res) { res.json(await MentorRecord.findOneAndUpdate({ _id: req.params.id, facultyId: req.user.facultyId }, req.body, { new: true, runValidators: true })); }
export async function deleteRecord(req, res) { await MentorRecord.findOneAndDelete({ _id: req.params.id, facultyId: req.user.facultyId }); res.status(204).send(); }