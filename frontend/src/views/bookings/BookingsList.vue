<template>
  <v-container>
    <!-- Success/Error Snackbar -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
      {{ snackbar.message }}
    </v-snackbar>

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
<!--              <v-btn-->
<!--                :to="`/services/${booking.service_id}`"-->
<!--                size="small"-->
<!--                variant="text"-->
<!--                class="mr-2"-->
<!--              >-->
<!--                View Service-->
<!--              </v-btn>-->
              <v-btn
                v-if="authStore.isAdmin"
                size="small"
                variant="text"
                color="primary"
                @click="openEditDialog(booking)"
              >
                Change Status
              </v-btn>
            </td>
          </tr>
          </tbody>
        </v-table>
      </v-col>
    </v-row>

    <!-- Status Change Dialog -->
    <v-dialog v-model="editDialog" max-width="500">
      <v-card>
        <v-card-title>Change Booking Status</v-card-title>
        <v-card-text>
          <v-select
            v-model="editedBooking.status"
            :items="statusOptions"
            label="Status"
            required
          ></v-select>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" @click="editDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="updateStatus">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getBookings, updateBookingStatus } from '@/api/bookings'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const bookings = ref([])
const editDialog = ref(false)
const editedBooking = ref({
  id: null,
  status: ''
})
const statusOptions = ['pending', 'confirmed', 'cancelled']
const snackbar = ref({
  show: false,
  message: '',
  color: 'success'
})

onMounted(async () => {
  try {
    const response = await getBookings()
    bookings.value = response.data
  } catch (error) {
    console.error('Failed to fetch bookings:', error)
    showSnackbar('Failed to fetch bookings', 'error')
  }
})

const openEditDialog = (booking) => {
  editedBooking.value = {
    id: booking.id,
    status: booking.status
  }
  editDialog.value = true
}

const updateStatus = async () => {
  try {
    const response = await updateBookingStatus(editedBooking.value.id, {
      status: editedBooking.value.status
    })

    // Update the local bookings array
    const index = bookings.value.findIndex(b => b.id === editedBooking.value.id)
    if (index !== -1) {
      bookings.value[index].status = editedBooking.value.status
    }

    editDialog.value = false
    showSnackbar(response.data.message)
  } catch (error) {
    console.error('Failed to update booking status:', error)
    showSnackbar(error.response?.data?.message || 'Failed to update booking status', 'error')
  }
}

const showSnackbar = (message, color = 'success') => {
  snackbar.value = {
    show: true,
    message,
    color
  }
}

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
