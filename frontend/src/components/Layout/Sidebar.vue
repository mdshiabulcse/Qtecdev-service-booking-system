<!-- frontend/src/components/Layout/Sidebar.vue -->
<template>
  <v-navigation-drawer app color="primary" dark>
    <v-list dense nav>
      <v-list-item link to="/">
        <v-list-item-icon>
          <v-icon>mdi-view-dashboard</v-icon>
        </v-list-item-icon>
        <v-list-item-title>Dashboard</v-list-item-title>
      </v-list-item>

      <v-list-item v-if="isTeacher || isAdmin" link to="/students">
        <v-list-item-icon>
          <v-icon>mdi-account-multiple</v-icon>
        </v-list-item-icon>
        <v-list-item-title>Students</v-list-item-title>
      </v-list-item>

      <v-list-item v-if="isTeacher || isAdmin" link to="/attendance">
        <v-list-item-icon>
          <v-icon>mdi-clipboard-check</v-icon>
        </v-list-item-icon>
        <v-list-item-title>Attendance</v-list-item-title>
      </v-list-item>

      <v-list-item v-if="isAdmin" link to="/admin/users">
        <v-list-item-icon>
          <v-icon>mdi-account-cog</v-icon>
        </v-list-item-icon>
        <v-list-item-title>User Management</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script setup>
import {computed} from 'vue'
import {useAuthStore} from '@/stores/auth'

const authStore = useAuthStore()

const isAdmin = computed(() => authStore.isAdmin)
const isTeacher = computed(() => authStore.isTeacher)
</script>
