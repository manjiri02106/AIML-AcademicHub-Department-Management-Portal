import { createContext, useContext, useEffect, useState } from 'react';
import { authApi } from '../services/api.js';

const AuthContext = createContext(null);
export function AuthProvider({ children }) {
  const [user, setUser] = useState(() => JSON.parse(localStorage.getItem('aiml_user') || 'null'));
  const login = async credentials => { const { data } = await authApi.login(credentials); localStorage.setItem('aiml_token', data.token); localStorage.setItem('aiml_user', JSON.stringify(data.user)); setUser(data.user); };
  const logout = () => { localStorage.removeItem('aiml_token'); localStorage.removeItem('aiml_user'); setUser(null); };
  useEffect(() => { if (!localStorage.getItem('aiml_token')) setUser(null); }, []);
  return <AuthContext.Provider value={{ user, login, logout }}>{children}</AuthContext.Provider>;
}
export const useAuth = () => useContext(AuthContext);