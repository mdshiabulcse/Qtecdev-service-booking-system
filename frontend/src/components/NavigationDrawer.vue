<template>
  <v-navigation-drawer v-model="drawer" temporary>
    <v-list>
      <v-list-item
        v-for="item in navItems"
        :key="item.title"
        :to="item.to"
        :title="item.title"
      ></v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const drawer = ref(false)

const navItems = computed(() => {
  const items = [
    { title: 'Home', to: '/' },
    { title: 'Services', to: '/services' }
  ]

  if (authStore.isAuthenticated) {
    items.push({ title: 'My Bookings', to: '/bookings' })

    if (authStore.isAdmin) {
      items.push(
        { title: 'Admin Dashboard', to: '/admin' },
        { title: 'Manage Services', to: '/services/new' }
      )
    }
  } else {
    items.push(
      { title: 'Login', to: '/login' },
      { title: 'Register', to: '/register' }
    )
  }

  return items
})

defineExpose({
  drawer
})
</script>
