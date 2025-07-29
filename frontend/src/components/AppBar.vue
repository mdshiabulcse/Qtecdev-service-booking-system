<template>
  <v-app-bar color="primary" prominent>
    <v-app-bar-title>
      <router-link to="/" class="text-white text-decoration-none">
        Service Booking System
      </router-link>
    </v-app-bar-title>

    <template v-if="authStore.isAuthenticated">
      <v-btn
        v-for="item in navItems"
        :key="item.title"
        :to="item.to"
        color="white"
        variant="text"
      >
        {{ item.title }}
      </v-btn>

      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn
            color="white"
            variant="text"
            v-bind="props"
          >
            <v-icon>mdi-account</v-icon>
            {{ authStore.user.name }}
          </v-btn>
        </template>

        <v-list>
          <v-list-item
            v-if="authStore.isAdmin"
            to="/admin"
            title="Admin Dashboard"
          ></v-list-item>
          <v-list-item
            to="/bookings"
            title="My Bookings"
          ></v-list-item>
          <v-list-item
            @click="logout"
            title="Logout"
          ></v-list-item>
        </v-list>
      </v-menu>
    </template>

    <template v-else>
      <v-btn
        to="/login"
        color="white"
        variant="text"
      >
        Login
      </v-btn>
      <v-btn
        to="/register"
        color="white"
        variant="text"
      >
        Register
      </v-btn>
    </template>
  </v-app-bar>
</template>

<script setup>
import { useAuthStore } from '@/store/auth'
import { computed } from 'vue'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const navItems = computed(() => {
  const items = [
    { title: 'Services', to: '/services' },
    { title: 'Bookings', to: '/bookings' }
  ]
  return items
})

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.text-decoration-none {
  text-decoration: none;
}
</style>
