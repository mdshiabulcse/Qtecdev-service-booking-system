<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="text-h4 mb-4">Our Services</h1>

        <v-btn
          v-if="authStore.isAdmin"
          color="primary"
          class="mb-4"
          @click="$router.push('/services/new')"
        >
          Add New Service
        </v-btn>
      </v-col>

      <v-col
        v-for="service in services"
        :key="service.id"
        cols="12"
        sm="6"
        md="4"
      >
        <ServiceCard :service="service" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getServices } from '@/api/services'
// import ServiceCard from '@/components/ServiceCard.vue'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const services = ref([])

onMounted(async () => {
  try {
    const response = await getServices()
    services.value = response.data
  } catch (error) {
    console.error('Failed to fetch services:', error)
  }
})
</script>
