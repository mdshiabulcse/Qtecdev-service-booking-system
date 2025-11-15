<!-- frontend/src/views/Students.vue -->
<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <span>Student Management</span>
            <v-btn color="primary" @click="showDialog = true">
              <v-icon left>mdi-plus</v-icon>
              Add Student
            </v-btn>
          </v-card-title>

          <v-card-text>
            <!-- Filters -->
            <v-row>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="filters.search"
                  label="Search Students"
                  prepend-inner-icon="mdi-magnify"
                  outlined
                  dense
                  clearable
                  @input="loadStudents"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="3">
                <v-select
                  v-model="filters.class"
                  :items="classes"
                  label="Filter by Class"
                  outlined
                  dense
                  clearable
                  @change="loadStudents"
                ></v-select>
              </v-col>
              <v-col cols="12" sm="3">
                <v-select
                  v-model="filters.section"
                  :items="sections"
                  label="Filter by Section"
                  outlined
                  dense
                  clearable
                  @change="loadStudents"
                ></v-select>
              </v-col>
              <v-col cols="12" sm="2">
                <v-select
                  v-model="filters.is_active"
                  :items="statusOptions"
                  label="Status"
                  outlined
                  dense
                  clearable
                  @change="loadStudents"
                ></v-select>
              </v-col>
            </v-row>

            <!-- Students Table -->
            <v-data-table
              :headers="headers"
              :items="students"
              :loading="isLoading"
              :options.sync="options"
              :server-items-length="pagination.total"
              :items-per-page="15"
              class="elevation-1"
            >
              <template v-slot:item.photo="{ item }">
                <v-avatar size="36">
                  <v-img
                    v-if="item.photo"
                    :src="item.photo"
                    :alt="item.name"
                  ></v-img>
                  <v-icon v-else>mdi-account-circle</v-icon>
                </v-avatar>
              </template>

              <template v-slot:item.is_active="{ item }">
                <v-chip small :color="item.is_active ? 'success' : 'error'">
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </v-chip>
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn
                  icon
                  small
                  color="info"
                  @click="viewStudent(item)"
                >
                  <v-icon>mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  icon
                  small
                  color="warning"
                  @click="editStudent(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  icon
                  small
                  color="error"
                  @click="deleteStudent(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Student Dialog -->
    <v-dialog v-model="showDialog" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ editingStudent ? 'Edit Student' : 'Add New Student' }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.student_id"
                  label="Student ID"
                  :rules="[v => !!v || 'Student ID is required']"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.name"
                  label="Full Name"
                  :rules="[v => !!v || 'Name is required']"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.email"
                  label="Email"
                  type="email"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="form.gender"
                  :items="genders"
                  label="Gender"
                  :rules="[v => !!v || 'Gender is required']"
                  required
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="form.class"
                  :items="classes"
                  label="Class"
                  :rules="[v => !!v || 'Class is required']"
                  required
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="form.section"
                  :items="sections"
                  label="Section"
                  :rules="[v => !!v || 'Section is required']"
                  required
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.parent_name"
                  label="Parent Name"
                  :rules="[v => !!v || 'Parent name is required']"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="form.parent_phone"
                  label="Parent Phone"
                  :rules="[v => !!v || 'Parent phone is required']"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-textarea
                  v-model="form.address"
                  label="Address"
                  rows="2"
                ></v-textarea>
              </v-col>
              <v-col cols="12">
                <v-file-input
                  v-model="form.photo_file"
                  label="Student Photo"
                  accept="image/*"
                  prepend-icon="mdi-camera"
                ></v-file-input>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
          <v-btn color="blue darken-1" text @click="saveStudent" :loading="saving">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useStudentStore } from '@/stores/students'

const studentStore = useStudentStore()

const showDialog = ref(false)
const editingStudent = ref(null)
const valid = ref(false)
const saving = ref(false)
const form = ref({})
const options = ref({})

const filters = reactive({
  search: '',
  class: '',
  section: '',
  is_active: null
})

const classes = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']
const sections = ['A', 'B', 'C', 'D']
const genders = [
  { text: 'Male', value: 'male' },
  { text: 'Female', value: 'female' },
  { text: 'Other', value: 'other' }
]
const statusOptions = [
  { text: 'Active', value: true },
  { text: 'Inactive', value: false }
]

const headers = [
  { text: 'Photo', value: 'photo', sortable: false },
  { text: 'Student ID', value: 'student_id' },
  { text: 'Name', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Class', value: 'class' },
  { text: 'Section', value: 'section' },
  { text: 'Status', value: 'is_active' },
  { text: 'Actions', value: 'actions', sortable: false }
]

const students = computed(() => studentStore.students)
const isLoading = computed(() => studentStore.isLoading)
const pagination = computed(() => studentStore.pagination)

const loadStudents = async () => {
  await studentStore.loadStudents({
    ...filters,
    page: options.value.page,
    per_page: options.value.itemsPerPage
  })
}

const editStudent = (student) => {
  editingStudent.value = student
  form.value = { ...student }
  showDialog.value = true
}

const viewStudent = (student) => {
  // Implement view student details
  console.log('View student:', student)
}

const deleteStudent = async (student) => {
  if (confirm(`Are you sure you want to delete ${student.name}?`)) {
    try {
      await studentStore.deleteStudent(student.id)
    } catch (error) {
      console.error('Failed to delete student:', error)
    }
  }
}

const saveStudent = async () => {
  if (!valid.value) return

  saving.value = true
  try {
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== undefined) {
        formData.append(key, form.value[key])
      }
    })

    if (editingStudent.value) {
      await studentStore.updateStudent({
        id: editingStudent.value.id,
        data: formData
      })
    } else {
      await studentStore.createStudent(formData)
    }

    closeDialog()
    await loadStudents()
  } catch (error) {
    console.error('Failed to save student:', error)
  } finally {
    saving.value = false
  }
}

const closeDialog = () => {
  showDialog.value = false
  editingStudent.value = null
  form.value = {}
}

// Watchers
watch(filters, () => {
  options.value.page = 1
  loadStudents()
}, { deep: true })

watch(options, () => {
  loadStudents()
}, { deep: true })

onMounted(() => {
  loadStudents()
})
</script>
