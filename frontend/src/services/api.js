// src/services/api.js
import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json'
  }
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = JSON.parse(localStorage.getItem('auth') || '{}')
    const token = authStore.token
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const authService = {
  login(credentials) {
    return api.post('/login', credentials)
  },
  register(userData) {
    return api.post('/register', userData)
  },
  logout() {
    return api.post('/logout')
  },
  checkAuth() {
    return api.get('/check-auth')
  }
}

export const dashboardService = {
  getStats() {
    return api.get('/dashboard/stats')
  },
  getRecentActivities() {
    return api.get('/dashboard/recent-activities')
  },
  getMonthlyChart() {
    return api.get('/dashboard/monthly-chart')
  },
  getClassPerformance() {
    return api.get('/dashboard/class-performance')
  }
}

export const studentService = {
  getAll(params = {}) {
    return api.get('/students', { params })
  },
  get(id) {
    return api.get(`/students/${id}`)
  },
  create(data) {
    return api.post('/students', data)
  },
  update(id, data) {
    return api.put(`/students/${id}`, data)
  },
  delete(id) {
    return api.delete(`/students/${id}`)
  },
  getByClassSection(className, section) {
    return api.get(`/students/class/${className}/section/${section}`)
  }
}

export const attendanceService = {
  recordBulk(data) {
    return api.post('/attendance/bulk', data)
  },
  getTodayAttendance() {
    return api.get('/attendance/today')
  },
  getMonthlyReport(month, className = null) {
    return api.get('/attendance/monthly-report', {
      params: { month, class: className }
    })
  },
  getClassAttendance(className, section, date = null) {
    return api.get(`/attendance/class/${className}/section/${section}`, {
      params: { date }
    })
  }
}

export const adminService = {
  getUsers(params = {}) {
    return api.get('/admin/users', { params })
  },
  createUser(userData) {
    return api.post('/admin/users', userData)
  },
  updateUser(id, userData) {
    return api.put(`/admin/users/${id}`, userData)
  },
  deleteUser(id) {
    return api.delete(`/admin/users/${id}`)
  },
  getSystemStats() {
    return api.get('/admin/system-stats')
  }
}

export default api
