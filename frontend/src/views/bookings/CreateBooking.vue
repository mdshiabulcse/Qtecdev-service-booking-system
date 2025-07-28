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

          <BookingForm
            :service-id="route.params.serviceId"
            @submitted="handleSubmission"
          />
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getService } from '@/api/services'
import BookingForm from '@/components/BookingForm.vue'

const route = useRoute()
const router = useRouter()

const service = ref(null)
const error = ref('')

onMounted(async () => {
  if (route.params.serviceId) {
    try {
      const response = await getService(route.params.serviceId)
      service.value = response.data
    } catch (err) {
      error.value = 'Failed to load service'
    }
  }
})

const handleSubmission = () => {
  router.push('/bookings')
}
</script>
