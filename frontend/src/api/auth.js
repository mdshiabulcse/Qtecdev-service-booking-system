import api from './index'

export const register = (userData) => api.post('/register', userData)
export const login = (credentials) => api.post('/login', credentials)
export const logout = () => api.post('/logout')
export const checkAuth = () => api.get('/check-auth')
