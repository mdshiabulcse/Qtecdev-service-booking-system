<!-- src/views/Register.vue -->
<template>
  <v-container fluid class="fill-height login-bg">
    <v-row justify="center" align="center">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="elevation-12">
          <v-toolbar color="primary" dark flat>
            <v-toolbar-title>Register - School System</v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-form @submit.prevent="register">
              <v-text-field
                v-model="form.name"
                label="Full Name"
                required
                prepend-icon="mdi-account"
                :error-messages="errors.name"
              ></v-text-field>

              <v-text-field
                v-model="form.email"
                label="Email"
                type="email"
                required
                prepend-icon="mdi-email"
                :error-messages="errors.email"
              ></v-text-field>

              <v-text-field
                v-model="form.phone"
                label="Phone"
                type="tel"
                prepend-icon="mdi-phone"
                :error-messages="errors.phone"
              ></v-text-field>

              <v-text-field
                v-model="form.password"
                label="Password"
                type="password"
                required
                prepend-icon="mdi-lock"
                :error-messages="errors.password"
              ></v-text-field>

              <v-text-field
                v-model="form.password_confirmation"
                label="Confirm Password"
                type="password"
                required
                prepend-icon="mdi-lock-check"
                :error-messages="errors.password_confirmation"
              ></v-text-field>

              <v-btn
                type="submit"
                color="primary"
                block
                large
                :loading="loading"
              >
                Register
              </v-btn>
            </v-form>
          </v-card-text>
          <v-card-actions class="text-center">
            <v-spacer></v-spacer>
            <span>Already have an account? <router-link to="/login">Login</router-link></span>
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: ''
})
const errors = ref({})

const register = async () => {
  loading.value = true
  errors.value = {}

  try {
    await authStore.register(form.value)
    router.push('/')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value = { general: error.response?.data?.message || 'Registration failed' }
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-bg {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
