import { Router } from 'express';
import { createRecord, deleteRecord, getRecord, listRecords, updateRecord } from '../controllers/mentoringController.js';
import { authenticate } from '../middleware/auth.js';

const router = Router();
router.use(authenticate);
router.get('/', listRecords);
router.get('/:id', getRecord);
router.post('/', createRecord);
router.put('/:id', updateRecord);
router.delete('/:id', deleteRecord);
export default router;