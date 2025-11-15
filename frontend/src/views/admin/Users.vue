<!-- src/views/admin/Users.vue -->
<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <span>User Management</span>
            <v-btn color="primary" @click="showDialog = true">
              <v-icon left>mdi-plus</v-icon>
              Add User
            </v-btn>
          </v-card-title>

          <v-card-text>
            <!-- Search -->
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="search"
                  label="Search Users"
                  prepend-inner-icon="mdi-magnify"
                  outlined
                  dense
                  clearable
                  @input="loadUsers"
                ></v-text-field>
              </v-col>
            </v-row>

            <!-- Users Table -->
            <v-data-table
              :headers="headers"
              :items="users"
              :loading="loading"
              :options.sync="options"
              :server-items-length="pagination.total"
              :items-per-page="10"
              class="elevation-1"
            >
              <template v-slot:item.role="{ item }">
                <v-chip small :color="getRoleColor(item.role)">
                  {{ item.role }}
                </v-chip>
              </template>

              <template v-slot:item.created_at="{ item }">
                {{ formatDate(item.created_at) }}
              </template>

              <template v-slot:item.actions="{ item }">
                <v-btn
                  icon
                  small
                  color="warning"
                  @click="editUser(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  icon
                  small
                  color="error"
                  @click="deleteUser(item)"
                  :disabled="item.id === currentUser?.id"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- User Dialog -->
    <v-dialog v-model="showDialog" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ editingUser ? 'Edit User' : 'Add New User' }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="userForm.name"
                  label="Full Name"
                  :rules="[v => !!v || 'Name is required']"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="userForm.email"
                  label="Email"
                  type="email"
                  :rules="[v => !!v || 'Email is required', v => /.+@.+\..+/.test(v) || 'Email must be valid']"
                  required
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="userForm.phone"
                  label="Phone"
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <v-select
                  v-model="userForm.role"
                  :items="roles"
                  label="Role"
                  :rules="[v => !!v || 'Role is required']"
                  required
                ></v-select>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="userForm.password"
                  label="Password"
                  type="password"
                  :rules="[v => !editingUser || v.length >= 8 || 'Password must be at least 8 characters']"
                  :required="!editingUser"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
          <v-btn color="blue darken-1" text @click="saveUser" :loading="saving">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { adminService } from '@/services/api'

const authStore = useAuthStore()

const showDialog = ref(false)
const editingUser = ref(null)
const valid = ref(false)
const saving = ref(false)
const loading = ref(false)
const search = ref('')
const options = ref({})
const users = ref([])
const pagination = ref({})

const userForm = reactive({
  name: '',
  email: '',
  phone: '',
  role: '',
  password: ''
})

const roles = [
  { text: 'Admin', value: 'admin' },
  { text: 'Teacher', value: 'teacher' },
  { text: 'Parent', value: 'parent' }
]

const headers = [
  { text: 'Name', value: 'name' },
  { text: 'Email', value: 'email' },
  { text: 'Phone', value: 'phone' },
  { text: 'Role', value: 'role' },
  { text: 'Created', value: 'created_at' },
  { text: 'Actions', value: 'actions', sortable: false }
]

const currentUser = computed(() => authStore.user)

const getRoleColor = (role) => {
  const colors = {
    admin: 'error',
    teacher: 'primary',
    parent: 'success'
  }
  return colors[role] || 'default'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const loadUsers = async () => {
  loading.value = true
  try {
    const response = await adminService.getUsers({
      search: search.value,
      page: options.value.page,
      per_page: options.value.itemsPerPage
    })
    users.value = response.data.users.data
    pagination.value = {
      total: response.data.total,
      current_page: response.data.users.current_page,
      per_page: response.data.users.per_page
    }
  } catch (error) {
    console.error('Failed to load users:', error)
  } finally {
    loading.value = false
  }
}

const editUser = (user) => {
  editingUser.value = user
  Object.assign(userForm, user)
  userForm.password = ''
  showDialog.value = true
}

const deleteUser = async (user) => {
  if (user.id === currentUser.value?.id) {
    alert('You cannot delete your own account')
    return
  }

  if (confirm(`Are you sure you want to delete ${user.name}?`)) {
    try {
      await adminService.deleteUser(user.id)
      await loadUsers()
    } catch (error) {
      console.error('Failed to delete user:', error)
      alert('Failed to delete user')
    }
  }
}

const saveUser = async () => {
  if (!valid.value) return

  saving.value = true
  try {
    const data = { ...userForm }
    if (!data.password) {
      delete data.password
    }

    if (editingUser.value) {
      await adminService.updateUser(editingUser.value.id, data)
    } else {
      await adminService.createUser(data)
    }

    closeDialog()
    await loadUsers()
  } catch (error) {
    console.error('Failed to save user:', error)
    alert('Failed to save user')
  } finally {
    saving.value = false
  }
}

const closeDialog = () => {
  showDialog.value = false
  editingUser.value = null
  Object.keys(userForm).forEach(key => {
    userForm[key] = ''
  })
}

onMounted(() => {
  loadUsers()
})
</script>
