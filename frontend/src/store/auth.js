import { defineStore } from 'pinia'
import { login, register, logout, getUser } from '@/api/auth'
import router from '@/router'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAdmin: false,
    token: localStorage.getItem('token') || null
  }),
  actions: {
    async init() {
      if (this.token) {
        try {
          const response = await getUser()
          this.user = response.data
          this.isAdmin = response.data.is_admin
        } catch (error) {
          this.logout()
        }
      }
    },
    async login(credentials) {
      try {
        const response = await login(credentials)
        this.user = response.data.user
        this.isAdmin = response.data.user.is_admin
        this.token = response.data.token
        localStorage.setItem('token', response.data.token)
        router.push('/')
        return true
      } catch (error) {
        return false
      }
    },
    async register(userData) {
      try {
        await register(userData)
        router.push('/login')
        return true
      } catch (error) {
        return false
      }
    },
    async logout() {
      try {
        await logout()
      } finally {
        this.user = null
        this.isAdmin = false
        this.token = null
        localStorage.removeItem('token')
        router.push('/login')
      }
    }
  }
})
