<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="text-h4 mb-4">
          {{ authStore.isAdmin ? 'All Bookings' : 'My Bookings' }}
        </h1>
      </v-col>

      <v-col cols="12">
        <v-table>
          <thead>
          <tr>
            <th v-if="authStore.isAdmin">User</th>
            <th>Service</th>
            <th>Booking Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="booking in bookings" :key="booking.id">
            <td v-if="authStore.isAdmin">
              {{ booking.user?.name }} ({{ booking.user?.email }})
            </td>
            <td>{{ booking.service?.name }}</td>
            <td>{{ formatDate(booking.booking_date) }}</td>
            <td>
              <v-chip :color="getStatusColor(booking.status)">
                {{ booking.status }}
              </v-chip>
            </td>
            <td>
              <v-btn
                :to="`/services/${booking.service_id}`"
                size="small"
                variant="text"
              >
                View Service
              </v-btn>
            </td>
          </tr>
          </tbody>
        </v-table>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getBookings } from '@/api/bookings'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const bookings = ref([])

onMounted(async () => {
  try {
    const response = await getBookings()
    bookings.value = response.data
  } catch (error) {
    console.error('Failed to fetch bookings:', error)
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString()
}

const getStatusColor = (status) => {
  switch (status) {
    case 'confirmed': return 'success'
    case 'cancelled': return 'error'
    default: return 'primary'
  }
}
</script>
