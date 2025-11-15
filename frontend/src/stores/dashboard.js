// frontend/src/stores/dashboard.js
import { defineStore } from 'pinia'
import { dashboardService } from '@/services/api'

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    stats: {},
    recentActivities: [],
    monthlyChart: [],
    classPerformance: [],
    isLoading: false
  }),

  actions: {
    async loadDashboardData() {
      this.isLoading = true
      try {
        const [stats, activities, chart, performance] = await Promise.all([
          dashboardService.getStats(),
          dashboardService.getRecentActivities(),
          dashboardService.getMonthlyChart(),
          dashboardService.getClassPerformance()
        ])

        this.stats = stats.data.data
        this.recentActivities = activities.data.data
        this.monthlyChart = chart.data.data
        this.classPerformance = performance.data.data
      } catch (error) {
        console.error('Failed to load dashboard data:', error)
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async refreshDashboard() {
      await this.loadDashboardData()
    }
  }
})
