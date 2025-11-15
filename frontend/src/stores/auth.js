// src/stores/auth.js
import { defineStore } from 'pinia'
import { authService } from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),

  persist: true,

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isTeacher: (state) => state.user?.role === 'teacher',
    isParent: (state) => state.user?.role === 'parent',
    userRole: (state) => state.user?.role
  },

  actions: {
    async login(credentials) {
      try {
        const response = await authService.login(credentials)
        this.user = response.data.user
        this.token = response.data.token
        this.isAuthenticated = true
        return response
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    async register(userData) {
      try {
        const response = await authService.register(userData)
        this.user = response.data.user
        this.token = response.data.token
        this.isAuthenticated = true
        return response
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    async logout() {
      try {
        await authService.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },

    async checkAuth() {
      if (!this.token) {
        this.clearAuth()
        return false
      }

      try {
        const response = await authService.checkAuth()
        this.user = response.data.user
        this.isAuthenticated = true
        return true
      } catch (error) {
        this.clearAuth()
        return false
      }
    },

    initializeAuth() {
      return this.checkAuth()
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
    }
  }
})
