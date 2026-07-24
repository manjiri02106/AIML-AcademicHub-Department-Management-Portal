import { BookOpen, GraduationCap, LayoutDashboard, LogOut, Menu, MessageSquare, UserRound, X } from 'lucide-react';
import { NavLink, Outlet } from 'react-router-dom';
import { useState } from 'react';
import { useAuth } from '../context/AuthContext.jsx';

const links = [{ to: '/', label: 'Dashboard', icon: LayoutDashboard }, { to: '/profile', label: 'Profile', icon: UserRound }, { to: '/courses', label: 'Course Allocation', icon: BookOpen }, { to: '/mentoring', label: 'Mentoring Records', icon: MessageSquare }];
export default function Layout() {
  const [open, setOpen] = useState(false); const { user, logout } = useAuth();
  return <div className="app-shell"><aside className={`sidebar ${open ? 'is-open' : ''}`}><div className="brand"><div className="brand-mark"><GraduationCap size={20} /></div><span>AIML Hub</span></div><nav>{links.map(({ to, label, icon: Icon }) => <NavLink key={to} to={to} end={to === '/'} onClick={() => setOpen(false)} className={({ isActive }) => `nav-link ${isActive ? 'active' : ''}`}><Icon size={19} />{label}</NavLink>)}</nav><button className="logout" onClick={logout}><LogOut size={18} />Sign out</button></aside>{open && <button className="scrim" aria-label="Close navigation" onClick={() => setOpen(false)} />}<main className="main-content"><header className="top-header"><button className="icon-button mobile-menu" onClick={() => setOpen(!open)} aria-label="Toggle navigation">{open ? <X size={20} /> : <Menu size={20} />}</button><div><p className="eyebrow">FACULTY PORTAL</p><h1>AIML AcademicHub</h1></div><div className="user-chip"><span className="avatar">{user?.name?.slice(0, 2).toUpperCase()}</span><span className="user-name">{user?.name}</span></div></header><div className="page-container"><Outlet /></div></main></div>;
}