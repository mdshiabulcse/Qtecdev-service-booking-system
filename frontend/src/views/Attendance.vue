<!-- frontend/src/views/Attendance.vue -->
<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <span>Record Attendance</span>
            <v-btn color="primary" :loading="isLoading" @click="submitAttendance" :disabled="!canSubmit">
              <v-icon left>mdi-content-save</v-icon>
              Submit Attendance
            </v-btn>
          </v-card-title>

          <v-card-text>
            <!-- Filters -->
            <v-row>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="selectedDate"
                  label="Attendance Date"
                  type="date"
                  required
                  outlined
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="4">
                <v-select
                  v-model="selectedClass"
                  :items="classes"
                  label="Select Class"
                  required
                  outlined
                  clearable
                ></v-select>
              </v-col>
              <v-col cols="12" sm="4">
                <v-select
                  v-model="selectedSection"
                  :items="sections"
                  label="Select Section"
                  required
                  outlined
                  clearable
                  :disabled="!selectedClass"
                ></v-select>
              </v-col>
            </v-row>

            <!-- Bulk Actions -->
            <v-card v-if="todayAttendance.length" class="mt-4">
              <v-card-title class="py-3">
                <span>Bulk Actions</span>
                <v-spacer></v-spacer>
                <span class="text-caption">
                  {{ attendanceSummary.present }} Present •
                  {{ attendanceSummary.absent }} Absent •
                  {{ attendanceSummary.late }} Late •
                  {{ attendanceSummary.percentage }}% Rate
                </span>
              </v-card-title>
              <v-card-actions class="py-2">
                <v-btn small color="success" @click="markAllAs('present')">
                  Mark All Present
                </v-btn>
                <v-btn small color="error" @click="markAllAs('absent')">
                  Mark All Absent
                </v-btn>
                <v-btn small color="warning" @click="markAllAs('late')">
                  Mark All Late
                </v-btn>
                <v-btn small color="secondary" @click="clearAll">
                  Clear All
                </v-btn>
              </v-card-actions>
            </v-card>

            <!-- Students List -->
            <v-card v-if="todayAttendance.length" class="mt-4">
              <v-card-text>
                <v-data-table
                  :headers="headers"
                  :items="todayAttendance"
                  :loading="isLoading"
                  hide-default-footer
                >
                  <template v-slot:item.attendance_status="{ item }">
                    <v-select
                      :value="bulkAttendance[item.id]?.status"
                      :items="attendanceOptions"
                      dense
                      outlined
                      hide-details
                      @update:model-value="updateAttendance(item.id, $event)"
                    ></v-select>
                  </template>

                  <template v-slot:item.attendance_note="{ item }">
                    <v-text-field
                      :value="bulkAttendance[item.id]?.note"
                      dense
                      outlined
                      hide-details
                      placeholder="Optional note"
                      @update:model-value="updateNote(item.id, $event)"
                    ></v-text-field>
                  </template>

                  <template v-slot:item.current_status="{ item }">
                    <v-chip
                      small
                      :color="getStatusColor(item.today_attendance?.status)"
                    >
                      {{ item.today_attendance?.status || 'Not Marked' }}
                    </v-chip>
                  </template>
                </v-data-table>
              </v-card-text>
            </v-card>

            <!-- Empty State -->
            <v-card v-else-if="selectedClass && selectedSection" class="mt-4">
              <v-card-text class="text-center py-8">
                <v-icon size="64" color="grey lighten-1">mdi-account-school</v-icon>
                <div class="text-h6 mt-2">No students found</div>
                <div class="text-body-1">No active students in this class and section</div>
              </v-card-text>
            </v-card>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Success Dialog -->
    <v-dialog v-model="showSuccessDialog" max-width="400">
      <v-card>
        <v-card-title class="text-h5">Success</v-card-title>
        <v-card-text>
          Attendance recorded successfully for {{ attendanceSummary.total }} students.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="showSuccessDialog = false">OK</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useAttendanceStore } from '@/stores/attendance'
import { useStudentStore } from '@/stores/students'

const attendanceStore = useAttendanceStore()
const studentStore = useStudentStore()

const selectedDate = ref(new Date().toISOString().split('T')[0])
const selectedClass = ref('')
const selectedSection = ref('')
const showSuccessDialog = ref(false)

const classes = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']
const sections = ['A', 'B', 'C', 'D']
const attendanceOptions = [
  { text: 'Present', value: 'present' },
  { text: 'Absent', value: 'absent' },
  { text: 'Late', value: 'late' },
  { text: 'Half Day', value: 'half_day' }
]

const headers = [
  { text: 'Student ID', value: 'student_id' },
  { text: 'Name', value: 'name' },
  { text: 'Current Status', value: 'current_status' },
  { text: 'Mark Attendance', value: 'attendance_status' },
  { text: 'Note', value: 'attendance_note' }
]

const todayAttendance = computed(() => attendanceStore.todayAttendance)
const bulkAttendance = computed(() => attendanceStore.bulkAttendance)
const attendanceSummary = computed(() => attendanceStore.attendanceSummary)
const isLoading = computed(() => attendanceStore.isLoading)
const canSubmit = computed(() => attendanceStore.canSubmit)

const getStatusColor = (status) => {
  const colors = {
    present: 'green',
    absent: 'red',
    late: 'orange',
    half_day: 'blue'
  }
  return colors[status] || 'grey'
}

const updateAttendance = (studentId, status) => {
  attendanceStore.updateAttendance(studentId, status)
}

const updateNote = (studentId, note) => {
  const currentStatus = bulkAttendance.value[studentId]?.status || 'present'
  attendanceStore.updateAttendance(studentId, currentStatus, note)
}

const markAllAs = (status) => {
  attendanceStore.markAllAs(status)
}

const clearAll = () => {
  attendanceStore.clearAll()
}

const loadClassStudents = async () => {
  if (!selectedClass.value || !selectedSection.value) return

  await studentStore.loadClassStudents({
    class: selectedClass.value,
    section: selectedSection.value
  })

  // Load today's attendance for the class
  await attendanceStore.loadClassAttendance({
    class: selectedClass.value,
    section: selectedSection.value,
    date: selectedDate.value
  })
}

const submitAttendance = async () => {
  try {
    await attendanceStore.recordBulkAttendance()
    showSuccessDialog.value = true
    await loadClassStudents() // Refresh to show updated status
  } catch (error) {
    console.error('Failed to submit attendance:', error)
    alert('Failed to submit attendance. Please try again.')
  }
}

// Watchers
watch([selectedClass, selectedSection], () => {
  if (selectedClass.value && selectedSection.value) {
    loadClassStudents()
  }
})

watch(selectedDate, () => {
  if (selectedClass.value && selectedSection.value) {
    loadClassStudents()
  }
})

onMounted(() => {
  // Initialize with today's data if class/section is selected
  if (selectedClass.value && selectedSection.value) {
    loadClassStudents()
  }
})
</script>
