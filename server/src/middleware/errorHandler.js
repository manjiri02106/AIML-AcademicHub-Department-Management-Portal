export function errorHandler(error, req, res, next) {
  console.error(error);
  if (error.code === 11000) return res.status(409).json({ message: 'A record with that value already exists' });
  res.status(error.status || 500).json({ message: error.message || 'Internal server error' });
}