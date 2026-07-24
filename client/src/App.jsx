import { Navigate, Route, Routes } from 'react-router-dom';
import Layout from './components/Layout.jsx';
import { useAuth } from './context/AuthContext.jsx';
import Courses from './pages/Courses.jsx';
import Dashboard from './pages/Dashboard.jsx';
import Login from './pages/Login.jsx';
import Mentoring from './pages/Mentoring.jsx';
import Profile from './pages/Profile.jsx';

function Protected() { const { user } = useAuth(); return user ? <Layout /> : <Navigate to="/login" replace />; }
export default function App() { return <Routes><Route path="/login" element={<Login />} /><Route element={<Protected />}><Route path="/" element={<Dashboard />} /><Route path="/profile" element={<Profile />} /><Route path="/courses" element={<Courses />} /><Route path="/mentoring" element={<Mentoring />} /></Route><Route path="*" element={<Navigate to="/" replace />} /></Routes>; }