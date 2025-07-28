<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="pa-4">
          <v-card-title class="text-center">
            {{ isEdit ? 'Edit Service' : 'Create Service' }}
          </v-card-title>

          <v-alert
            v-if="error"
            type="error"
            class="mb-4"
          >{{ error }}</v-alert>

          <v-form @submit.prevent="handleSubmit">
            <v-text-field
              v-model="form.name"
              label="Name"
              :error-messages="errors.name"
              required
            ></v-text-field>

            <v-textarea
              v-model="form.description"
              label="Description"
              :error-messages="errors.description"
              required
            ></v-textarea>

            <v-text-field
              v-model="form.price"
              label="Price"
              type="number"
              min="0"
              step="0.01"
              :error-messages="errors.price"
              required
            ></v-text-field>

            <v-select
              v-model="form.status"
              :items="statusOptions"
              label="Status"
              :error-messages="errors.status"
              required
            ></v-select>

            <v-btn
              type="submit"
              color="primary"
              block
              class="mt-4"
              :loading="isSubmitting"
            >
              {{ isEdit ? 'Update Service' : 'Create Service' }}
            </v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getService, createService, updateService } from '@/api/services'

const route = useRoute()
const router = useRouter()

const isEdit = computed(() => route.name === 'service-edit')

const form = ref({
  name: '',
  description: '',
  price: '',
  status: true
})
const errors = ref({})
const error = ref('')
const isSubmitting = ref(false)

const statusOptions = [
  { title: 'Active', value: true },
  { title: 'Inactive', value: false }
]

onMounted(async () => {
  if (isEdit.value) {
    try {
      const response = await getService(route.params.id)
      form.value = {
        name: response.data.name,
        description: response.data.description,
        price: response.data.price,
        status: response.data.status
      }
    } catch (err) {
      error.value = 'Failed to load service'
    }
  }
})

const handleSubmit = async () => {
  isSubmitting.value = true
  errors.value = {}
  error.value = ''

  try {
    if (isEdit.value) {
      await updateService(route.params.id, form.value)
    } else {
      await createService(form.value)
    }
    router.push('/services')
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      error.value = err.response?.data?.message || 'Operation failed'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
