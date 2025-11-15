<template>
  <v-container class="home-container">
    <!-- Hero Section -->
    <v-row class="hero-section" align="center" justify="center">
      <v-col cols="12" class="text-center">
        <h1 class="text-h3 font-weight-bold mb-4">Service Booking System</h1>
        <p class="text-h6 mb-8">Book services with ease and convenience</p>

        <v-btn
          x-large
          color="primary"
          class="mr-4"
          @click="navigateToServices"
        >
          Browse Services
        </v-btn>

        <v-btn
          x-large
          outlined
          color="primary"
          @click="navigateToBookings"
        >
          My Bookings
        </v-btn>
      </v-col>
    </v-row>

    <!-- Features Section -->
    <v-row class="mt-12">
      <v-col cols="12" md="4">
        <v-card class="feature-card pa-6" elevation="2">
          <v-icon x-large color="primary" class="mb-4">mdi-calendar-clock</v-icon>
          <h3 class="text-h5 mb-3">Easy Scheduling</h3>
          <p>Book at your convenience with our simple scheduling system.</p>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="feature-card pa-6" elevation="2">
          <v-icon x-large color="primary" class="mb-4">mdi-account-group</v-icon>
          <h3 class="text-h5 mb-3">Professional Services</h3>
          <p>Access a wide range of professional services from trusted providers.</p>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="feature-card pa-6" elevation="2">
          <v-icon x-large color="primary" class="mb-4">mdi-bell-check</v-icon>
          <h3 class="text-h5 mb-3">Instant Notifications</h3>
          <p>Receive real-time updates about your bookings and appointments.</p>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const navigateToServices = () => {
  router.push('/services')
}

const navigateToBookings = () => {
  if (authStore.isAuthenticated) {
    router.push('/bookings')
  } else {
    router.push('/login')
  }
}
</script>

<style scoped>
.home-container {
  max-width: 1200px;
  margin: 0 auto;
}

.hero-section {
  min-height: 60vh;
  background-size: cover;
  background-position: center;
  border-radius: 8px;
  margin-bottom: 32px;
}

.feature-card {
  height: 100%;
  transition: transform 0.3s ease;
  text-align: center;
}

.feature-card:hover {
  transform: translateY(-5px);
}
</style>
