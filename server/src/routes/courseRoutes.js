import { Router } from 'express';
import { createCourse, deleteCourse, listCourses, updateCourse } from '../controllers/courseController.js';
import { authenticate, authorize } from '../middleware/auth.js';

const router = Router();
router.use(authenticate);
router.get('/', listCourses);
router.post('/', authorize('Admin', 'HOD'), createCourse);
router.put('/:id', authorize('Admin', 'HOD'), updateCourse);
router.delete('/:id', authorize('Admin', 'HOD'), deleteCourse);
export default router;