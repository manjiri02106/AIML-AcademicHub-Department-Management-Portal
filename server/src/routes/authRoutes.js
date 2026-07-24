import { Router } from 'express';
import { body } from 'express-validator';
import { login, register } from '../controllers/authController.js';
import { validate } from '../middleware/validation.js';

const router = Router();
router.post('/login', [body('email').isEmail(), body('password').isLength({ min: 6 })], validate, login);
router.post('/register', [body('name').notEmpty(), body('email').isEmail(), body('password').isLength({ min: 6 }), body('facultyId').notEmpty()], validate, register);
export default router;