<template>
  <v-app>
    <AppBar />
    <NavigationDrawer />

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/store/auth'
import AppBar from '@/components/AppBar.vue'
import NavigationDrawer from '@/components/NavigationDrawer.vue'

const authStore = useAuthStore()

onMounted(async () => {
  if (authStore.token) {
    await authStore.init()
  }
})
</script>
