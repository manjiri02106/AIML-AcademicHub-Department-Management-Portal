import { Router } from 'express';
import multer from 'multer';
import { getOwnProfile, listFaculty, updateOwnProfile, uploadPhoto } from '../controllers/facultyController.js';
import { authenticate, authorize } from '../middleware/auth.js';

const upload = multer({ dest: 'uploads/', limits: { fileSize: 2 * 1024 * 1024 } });
const router = Router();
router.use(authenticate);
router.get('/me', getOwnProfile);
router.put('/me', updateOwnProfile);
router.post('/me/photo', upload.single('profileImage'), uploadPhoto);
router.get('/', authorize('Admin', 'HOD'), listFaculty);
export default router;