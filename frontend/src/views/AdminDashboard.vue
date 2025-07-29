<template>
  <v-container>
    <!-- Admin Dashboard Title -->
    <v-row class="mb-6">
      <v-col cols="12">
        <h1 class="text-h3">Admin Dashboard</h1>
        <v-divider class="my-4"></v-divider>
      </v-col>
    </v-row>

    <!-- Users Table -->
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex justify-space-between align-center">
            <span>All Users</span>
            <v-btn
              color="primary"
              @click="fetchUsers"
              :loading="loading"
            >
              <v-icon left>mdi-refresh</v-icon>
              Refresh
            </v-btn>
          </v-card-title>

          <v-card-text>
            <v-table>
              <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
<!--                <th>Actions</th>-->
              </tr>
              </thead>
              <tbody>
              <tr v-for="user in users" :key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <v-chip :color="user.is_admin ? 'primary' : 'secondary'">
                    {{ user.is_admin ? 'Admin' : 'User' }}
                  </v-chip>
                </td>
                <td>{{ formatDate(user.created_at) }}</td>
<!--                <td>-->
<!--                  <v-btn-->
<!--                    icon-->
<!--                    size="small"-->
<!--                    color="primary"-->
<!--                    @click="viewUser(user.id)"-->
<!--                  >-->
<!--                    <v-icon>mdi-eye</v-icon>-->
<!--                  </v-btn>-->
<!--                </td>-->
              </tr>
              </tbody>
            </v-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'
import { getUser } from '@/api/auth'

const authStore = useAuthStore()
const router = useRouter()
const users = ref([])
const loading = ref(false)

const fetchUsers = async () => {
  try {
    loading.value = true
    const response = await getUser()
    users.value = response.data
  } catch (error) {
    console.error('Error fetching users:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    }
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const viewUser = (userId) => {
  router.push(`/admin/users/${userId}`)
}

onMounted(() => {
  if (!authStore.isAdmin) {
    router.push('/')
  } else {
    fetchUsers()
  }
})
</script>

<style scoped>
.v-table {
  width: 100%;
}
</style>
