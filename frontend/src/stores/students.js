// src/stores/students.js
import { defineStore } from 'pinia'
import { studentService } from '@/services/api'

export const useStudentStore = defineStore('students', {
  state: () => ({
    students: [],
    currentStudent: null,
    isLoading: false,
    pagination: {}
  }),

  actions: {
    async loadStudents(params = {}) {
      this.isLoading = true
      try {
        const response = await studentService.getAll(params)
        this.students = response.data.data
        this.pagination = response.data.meta || {}
        return response.data
      } catch (error) {
        console.error('Failed to load students:', error)
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async loadStudent(id) {
      try {
        const response = await studentService.get(id)
        this.currentStudent = response.data.data
        return response.data.data
      } catch (error) {
        console.error('Failed to load student:', error)
        throw error
      }
    },

    async createStudent(studentData) {
      try {
        const response = await studentService.create(studentData)
        this.students.push(response.data.data)
        return response.data.data
      } catch (error) {
        console.error('Failed to create student:', error)
        throw error
      }
    },

    async updateStudent({ id, data }) {
      try {
        const response = await studentService.update(id, data)
        const index = this.students.findIndex(s => s.id === id)
        if (index !== -1) {
          this.students.splice(index, 1, response.data.data)
        }
        return response.data.data
      } catch (error) {
        console.error('Failed to update student:', error)
        throw error
      }
    },

    async deleteStudent(id) {
      try {
        await studentService.delete(id)
        this.students = this.students.filter(s => s.id !== id)
      } catch (error) {
        console.error('Failed to delete student:', error)
        throw error
      }
    },

    async loadClassStudents({ class: className, section }) {
      this.isLoading = true
      try {
        const response = await studentService.getByClassSection(className, section)
        this.students = response.data.data
        return response.data.data
      } catch (error) {
        console.error('Failed to load class students:', error)
        throw error
      } finally {
        this.isLoading = false
      }
    }
  }
})
