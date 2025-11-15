// src/stores/attendance.js
import { defineStore } from 'pinia'
import { attendanceService } from '@/services/api'

export const useAttendanceStore = defineStore('attendance', {
  state: () => ({
    todayAttendance: [],
    selectedDate: new Date().toISOString().split('T')[0],
    selectedClass: '',
    selectedSection: '',
    bulkAttendance: {},
    isLoading: false,
    todaySummary: {}
  }),

  getters: {
    attendanceSummary: (state) => {
      const attendanceData = Object.values(state.bulkAttendance)
      const present = attendanceData.filter(a => a.status === 'present').length
      const absent = attendanceData.filter(a => a.status === 'absent').length
      const late = attendanceData.filter(a => a.status === 'late').length
      const halfDay = attendanceData.filter(a => a.status === 'half_day').length
      const total = state.todayAttendance.length

      return {
        present,
        absent,
        late,
        halfDay,
        notMarked: total - (present + absent + late + halfDay),
        total,
        percentage: total > 0 ? Math.round(((present + late + (halfDay * 0.5)) / total) * 100) : 0
      }
    },

    canSubmit: (state) => {
      return Object.keys(state.bulkAttendance).length > 0 &&
        state.selectedDate &&
        state.selectedClass &&
        state.selectedSection
    }
  },

  actions: {
    async recordBulkAttendance() {
      this.isLoading = true

      try {
        const attendanceData = Object.keys(this.bulkAttendance).map(studentId => ({
          student_id: parseInt(studentId),
          date: this.selectedDate,
          ...this.bulkAttendance[studentId]
        }))

        const response = await attendanceService.recordBulk({
          date: this.selectedDate,
          attendances: attendanceData
        })

        this.bulkAttendance = {}
        return response.data
      } catch (error) {
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async loadTodaySummary() {
      try {
        const response = await attendanceService.getTodayAttendance()
        this.todaySummary = response.data.data
        return response.data.data
      } catch (error) {
        console.error('Failed to load today summary:', error)
        throw error
      }
    },

    async loadClassAttendance({ class: className, section, date }) {
      this.isLoading = true
      try {
        const response = await attendanceService.getClassAttendance(className, section, date)
        this.todayAttendance = response.data.data
        return response.data.data
      } catch (error) {
        console.error('Failed to load class attendance:', error)
        throw error
      } finally {
        this.isLoading = false
      }
    },

    markAllAs(status) {
      if (!this.todayAttendance.length) return

      this.todayAttendance.forEach(student => {
        this.bulkAttendance[student.id] = { status, note: '' }
      })
    },

    clearAll() {
      this.bulkAttendance = {}
    },

    updateAttendance(studentId, status, note = '') {
      this.bulkAttendance[studentId] = { status, note }
    }
  }
})
