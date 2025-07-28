<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="text-h3 text-center mb-6">Welcome to Service Booking System</h1>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="pa-4">
          <v-card-title>Our Services</v-card-title>
          <v-card-text>
            Book from our wide range of services with just a few clicks.
          </v-card-text>
          <v-card-actions>
            <v-btn
              color="primary"
              @click="$router.push('/services')"
            >
              View Services
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="pa-4">
          <v-card-title>My Bookings</v-card-title>
          <v-card-text>
            View and manage all your current bookings in one place.
          </v-card-text>
          <v-card-actions>
            <v-btn
              color="primary"
              @click="$router.push('/bookings')"
            >
              View Bookings
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
</script>
