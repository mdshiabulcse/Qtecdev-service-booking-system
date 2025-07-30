<template>
  <v-container class="fill-height">
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
      {{ snackbar.message }}
    </v-snackbar>
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="pa-4">
          <v-card-title class="text-center">Login</v-card-title>

          <v-alert
            v-if="error"
            type="error"
            class="mb-4"
          >{{ error }}</v-alert>

          <!-- Add test accounts info -->
          <v-alert
            type="info"
            class="mb-4"
          >
            <strong>Test Accounts:</strong><br>
            Admin: admin@shiabul.com / 12345678<br>
            User: user@shiabul.com / 12345678
          </v-alert>

          <v-form @submit.prevent="handleSubmit">
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

            <v-btn
              type="submit"
              color="primary"
              block
              class="mt-4"
              :loading="isSubmitting"
            >
              Login
            </v-btn>
          </v-form>

          <div class="text-center mt-4">
            Don't have an account? <router-link to="/register">Register here</router-link>
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
const snackbar = ref({
  show: false,
  message: '',
  color: 'success'
})
const form = ref({
  email: '',
  password: ''
})
const errors = ref({})
const error = ref('')
const isSubmitting = ref(false)

const showSnackbar = (message, color = 'success') => {
  snackbar.value = {
    show: true,
    message,
    color
  }
}
const handleSubmit = async () => {
  isSubmitting.value = true
  errors.value = {}
  error.value = ''

  try {
    const success = await authStore.login(form.value)
    if (success) {
      showSnackbar('Login successful! Redirecting to dashboard...', 'success');
      router.push('/')
    }
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      error.value = err.response?.data?.message || 'Login failed'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
