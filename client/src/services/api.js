import axios from 'axios';

const api = axios.create({ baseURL: import.meta.env.VITE_API_URL || 'http://localhost:5000/api' });
api.interceptors.request.use(config => { const token = localStorage.getItem('aiml_token'); if (token) config.headers.Authorization = `Bearer ${token}`; return config; });
api.interceptors.response.use(response => response, error => { if (error.response?.status === 401) localStorage.removeItem('aiml_token'); return Promise.reject(error); });
export const authApi = { login: data => api.post('/auth/login', data), register: data => api.post('/auth/register', data) };
export const dashboardApi = { get: () => api.get('/faculty/dashboard') };
export const facultyApi = { getMe: () => api.get('/faculty/me'), updateMe: data => api.put('/faculty/me', data), uploadPhoto: data => api.post('/faculty/me/photo', data) };
export const courseApi = { list: params => api.get('/courses', { params }), create: data => api.post('/courses', data), update: (id, data) => api.put(`/courses/${id}`, data), remove: id => api.delete(`/courses/${id}`) };
export const mentoringApi = { list: params => api.get('/mentoring', { params }), get: id => api.get(`/mentoring/${id}`), create: data => api.post('/mentoring', data), update: (id, data) => api.put(`/mentoring/${id}`, data), remove: id => api.delete(`/mentoring/${id}`) };
export default api;