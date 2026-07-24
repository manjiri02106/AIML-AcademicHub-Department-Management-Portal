import { AlertCircle, LoaderCircle, Search } from 'lucide-react';
export function Loading() { return <div className="loading"><LoaderCircle className="spin" size={24} />Loading workspace...</div>; }
export function ErrorMessage({ message }) { return <div className="error-box"><AlertCircle size={18} />{message || 'Something went wrong. Please try again.'}</div>; }
export function SearchInput({ value, onChange, placeholder = 'Search records...' }) { return <label className="search-box"><Search size={17} /><input value={value} onChange={e => onChange(e.target.value)} placeholder={placeholder} /></label>; }
export function EmptyState({ children }) { return <div className="empty-state">{children}</div>; }