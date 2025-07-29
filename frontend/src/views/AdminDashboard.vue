<template>
  <v-container>
    <v-tabs v-model="tab">
      <v-tab value="services">Services</v-tab>
      <v-tab value="bookings">Bookings</v-tab>
      <v-tab value="users">Users</v-tab>
    </v-tabs>

    <v-window v-model="tab">
      <v-window-item value="services">
        <ServicesList />
      </v-window-item>

      <v-window-item value="bookings">
        <BookingsList />
      </v-window-item>

      <v-window-item value="users">
        <v-table>
          <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <v-chip :color="user.is_admin ? 'primary' : 'default'">
                {{ user.is_admin ? 'Admin' : 'User' }}
              </v-chip>
            </td>
            <td>
              <v-btn
                size="small"
                variant="text"
                @click="editUser(user)"
              >
                Edit
              </v-btn>
            </td>
          </tr>
          </tbody>
        </v-table>
      </v-window-item>
    </v-window>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getUsers } from '@/api/auth'
import ServicesList from '@/views/services/ServicesList.vue'
import BookingsList from '@/views/bookings/BookingsList.vue'

const tab = ref('services')
const users = ref([])

onMounted(async () => {
  try {
    const response = await getUsers()
    users.value = response.data
  } catch (error) {
    console.error('Failed to fetch users:', error)
  }
})

const editUser = (user) => {
  console.log('Edit user:', user)
}
</script>
