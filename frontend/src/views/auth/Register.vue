<template>
  <v-container class="fill-height">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="pa-4">
          <v-card-title class="text-center">Register</v-card-title>

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

            <v-text-field
              v-model="form.email"
              label="Email"
              type="email"
              :error-messages="errors.email"
              required
            ></v-text-field>

            <v-text-field
              v-model="form.password"
              label="Password"
              type="password"
              :error-messages="errors.password"
              required
            ></v-text-field>

            <v-text-field
              v-model="form.password_confirmation"
              label="Confirm Password"
              type="password"
              :error-messages="errors.password_confirmation"
              required
            ></v-text-field>

            <v-btn
              type="submit"
              color="primary"
              block
              class="mt-4"
              :loading="isSubmitting"
            >
              Register
            </v-btn>
          </v-form>

          <div class="text-center mt-4">
            Already have an account? <router-link to="/login">Login here</router-link>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})
const errors = ref({})
const error = ref('')
const isSubmitting = ref(false)

const handleSubmit = async () => {
  isSubmitting.value = true
  errors.value = {}
  error.value = ''

  try {
    const success = await authStore.register(form.value)
    if (success) {
      router.push('/login')
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      error.value = err.response?.data?.message || 'Registration failed'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
