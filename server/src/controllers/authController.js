import jwt from 'jsonwebtoken';
import User from '../models/User.js';

function signUser(user) {
  return jwt.sign({ id: user._id, facultyId: user.facultyId, role: user.role, name: user.name }, process.env.JWT_SECRET, { expiresIn: '8h' });
}

export async function login(req, res) {
  const user = await User.findOne({ email: req.body.email }).select('+password');
  if (!user || !(await user.comparePassword(req.body.password))) return res.status(401).json({ message: 'Invalid email or password' });
  res.json({ token: signUser(user), user: { id: user._id, facultyId: user.facultyId, name: user.name, email: user.email, role: user.role } });
}

export async function register(req, res) {
  const user = await User.create(req.body);
  res.status(201).json({ token: signUser(user), user: { id: user._id, facultyId: user.facultyId, name: user.name, email: user.email, role: user.role } });
}