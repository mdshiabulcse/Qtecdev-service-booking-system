<template>
  <v-form @submit.prevent="handleSubmit">
    <v-select
      v-model="form.service_id"
      :items="services"
      item-title="name"
      item-value="id"
      label="Select Service"
      :error-messages="errors.service_id"
      required
    ></v-select>

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
    >
      Book Now
    </v-btn>
  </v-form>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { createBooking } from '@/api/bookings'
import { getServices } from '@/api/services'

const props = defineProps({
  serviceId: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['submitted'])

const form = ref({
  service_id: props.serviceId || null,
  booking_date: null
})

const errors = ref({})
const isSubmitting = ref(false)
const services = ref([])
const minDate = new Date().toISOString().slice(0, 16)

onMounted(async () => {
  try {
    const response = await getServices()
    services.value = response.data

    if (props.serviceId) {
      form.value.service_id = parseInt(props.serviceId)
    }
  } catch (error) {
    console.error('Failed to fetch services:', error)
  }
})

const handleSubmit = async () => {
  isSubmitting.value = true
  errors.value = {}

  alert('iinnn',form.value.service_id)

  try {
    await createBooking({
      service_id: form.value.service_id,
      booking_date: form.value.booking_date
    })
    emit('submitted')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      console.error('Booking failed:', error)
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
