import { defineStore } from 'pinia'
import { login, register, logout, checkAuth } from '@/api/auth'
import router from '@/router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAdmin: false,
    token: localStorage.getItem('token') || null,
    isAuthenticated: false
  }),
  actions: {
    async init() {
      if (this.token) {
        try {
          const response = await checkAuth()
          this.user = response.data.user
          this.isAdmin = response.data.user.is_admin
          this.isAuthenticated = true
          return true
        } catch (error) {
          this.clearAuth()
          return false
        }
      }
      return false
    },
    async login(credentials) {
      try {
        const response = await login(credentials)
        this.user = response.data.user
        this.isAdmin = response.data.user.is_admin
        this.token = response.data.token
        this.isAuthenticated = true
        localStorage.setItem('token', response.data.token)
        return true
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },
    async register(userData) {
      try {
        await register(userData)
        return true
      } catch (error) {
        throw error
      }
    },
    async logout() {
      try {
        await logout()
      } finally {
        this.clearAuth()
      }
    },
    clearAuth() {
      this.user = null
      this.isAdmin = false
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
      router.push('/login')
    }
  }
})
