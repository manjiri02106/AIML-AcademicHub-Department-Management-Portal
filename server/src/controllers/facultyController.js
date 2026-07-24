import Faculty from '../models/Faculty.js';

export async function getOwnProfile(req, res) {
  const profile = await Faculty.findOne({ facultyId: req.user.facultyId });
  if (!profile) return res.status(404).json({ message: 'Faculty profile not found' });
  res.json(profile);
}

export async function updateOwnProfile(req, res) {
  const profile = await Faculty.findOneAndUpdate({ facultyId: req.user.facultyId }, req.body, { new: true, runValidators: true, upsert: true, setDefaultsOnInsert: true });
  res.json(profile);
}

export async function listFaculty(req, res) {
  const faculty = await Faculty.find().sort({ name: 1 });
  res.json(faculty);
}

export async function uploadPhoto(req, res) {
  const profile = await Faculty.findOneAndUpdate({ facultyId: req.user.facultyId }, { profileImage: `/uploads/${req.file.filename}` }, { new: true });
  res.json(profile);
}