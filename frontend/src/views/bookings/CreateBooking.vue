<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="pa-4">
          <v-card-title class="text-center">
            {{ service ? `Book ${service.name}` : 'New Booking' }}
          </v-card-title>

          <v-alert
            v-if="error"
            type="error"
            class="mb-4"
          >{{ error }}</v-alert>

          <v-form @submit.prevent="handleSubmit">
            <!-- Service Selection (hidden if service is pre-selected) -->
            <v-select
              v-if="!service"
              v-model="form.service_id"
              :items="services"
              item-title="name"
              item-value="id"
              label="Select Service"
              :error-messages="errors.service_id"
              required
            ></v-select>

            <!-- Date and Time Picker -->
            <v-text-field
              v-model="form.booking_date"
              label="Booking Date & Time"
              type="datetime-local"
              :min="minDate"
              :error-messages="errors.booking_date"
              required
            ></v-text-field>

            <v-btn
              type="submit"
              color="primary"
              :loading="isSubmitting"
              block
              class="mt-4"
            >
              Confirm Booking
            </v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getService, getServices } from '@/api/services'
import { createBooking } from '@/api/bookings'
import { useAuthStore } from '@/store/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// Form data
const form = ref({
  service_id: route.params.serviceId || null,
  booking_date: ''
})

// UI state
const error = ref('')
const errors = ref({})
const isSubmitting = ref(false)
const services = ref([])
const service = ref(null)

// Minimum date (now)
const minDate = computed(() => {
  const now = new Date()
  // Add 1 hour to current time
  now.setHours(now.getHours() + 1)
  return now.toISOString().slice(0, 16)
})

// Initialize component
onMounted(async () => {
  if (route.params.serviceId) {
    try {
      const response = await getService(route.params.serviceId)
      service.value = response.data
      form.value.service_id = service.value.id
    } catch (err) {
      error.value = 'Failed to load service details'
    }
  } else {
    try {
      const response = await getServices()
      services.value = response.data.filter(s => s.status)
    } catch (err) {
      error.value = 'Failed to load available services'
    }
  }
})

// Handle form submission
const handleSubmit = async () => {
  isSubmitting.value = true
  error.value = ''
  errors.value = {}

  try {
    // Simple validation
    if (!form.value.service_id) {
      errors.value.service_id = ['Please select a service']
      return
    }

    if (!form.value.booking_date) {
      errors.value.booking_date = ['Please select date and time']
      return
    }

    // Submit to your API controller
    const response = await createBooking({
      service_id: form.value.service_id,
      booking_date: form.value.booking_date
    }, {
      headers: {
        Authorization: `Bearer ${authStore.token}`
      }
    })

    // Redirect to bookings page on success
    router.push('/bookings')

  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      error.value = err.response?.data?.message || 'Failed to create booking'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
