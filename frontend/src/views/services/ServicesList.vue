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
          <v-icon start>mdi-plus</v-icon>
          Add New Service
        </v-btn>
      </v-col>

      <!-- Loading State -->
      <template v-if="loading">
        <v-col
          v-for="n in 3"
          :key="`skeleton-${n}`"
          cols="12"
          sm="6"
          md="4"
        >
          <v-skeleton-loader type="card" height="300"></v-skeleton-loader>
        </v-col>
      </template>

      <!-- Error State -->
      <template v-else-if="error">
        <v-col cols="12">
          <v-alert type="error" variant="tonal">
            Failed to load services: {{ error }}
            <v-btn color="error" variant="text" @click="fetchServices">
              Retry
            </v-btn>
          </v-alert>
        </v-col>
      </template>

      <!-- Empty State -->
      <template v-else-if="services.length === 0">
        <v-col cols="12">
          <v-alert type="info" variant="tonal">
            No services available at the moment.
            <v-btn
              v-if="authStore.isAdmin"
              color="info"
              variant="text"
              @click="$router.push('/services/new')"
            >
              Create your first service
            </v-btn>
          </v-alert>
        </v-col>
      </template>

      <!-- Services List -->
      <template v-else>
        <v-col
          v-for="service in services"
          :key="service.id"
          cols="12"
          sm="6"
          md="4"
        >
          <v-card class="service-card" elevation="2">
            <v-img
              :src="service.image || '/placeholder-service.jpg'"
              height="200px"
              cover
            ></v-img>

            <v-card-title class="text-h6">{{ service.name }}</v-card-title>

            <v-card-text>
              <div class="text-body-1 mb-2">{{ service.description }}</div>
              <div class="d-flex justify-space-between">
                <span class="font-weight-bold">${{ service.price }}</span>
                <span>{{ service.duration }} minutes</span>
              </div>
            </v-card-text>

            <v-card-actions class="justify-space-between">
              <v-btn
                color="primary"
                variant="flat"
                @click="handleBook(service.id)"
                :disabled="!authStore.isAuthenticated"
              >
                {{ authStore.isAuthenticated ? 'Book Now' : 'Login to Book' }}
              </v-btn>

              <template v-if="authStore.isAdmin">
                <v-btn
                  icon
                  color="warning"
                  @click="handleEdit(service.id)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  icon
                  color="error"
                  @click="openDeleteDialog(service.id)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
            </v-card-actions>
          </v-card>
        </v-col>
      </template>
    </v-row>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="500">
      <v-card>
        <v-card-title class="text-h5">Confirm Delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete this service? This action cannot be undone.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="text"
            @click="deleteDialog = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="error"
            variant="flat"
            @click="confirmDelete"
            :loading="deleting"
          >
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getServices } from '@/api/services'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const authStore = useAuthStore()

// Service data
const services = ref([])
const loading = ref(true)
const error = ref(null)

// Delete dialog state
const deleteDialog = ref(false)
const deleting = ref(false)
const serviceToDelete = ref(null)

const fetchServices = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await getServices()
    services.value = response.data
  } catch (err) {
    error.value = err.message || 'Failed to fetch services'
    console.error('Error fetching services:', err)
  } finally {
    loading.value = false
  }
}

const handleBook = (serviceId) => {
  if (!authStore.isAuthenticated) {
    router.push('/login?redirect=/services')
    return
  }
  router.push(`/bookings/new?service=${serviceId}`)
}

const handleEdit = (serviceId) => {
  router.push(`/services/edit/${serviceId}`)
}

const openDeleteDialog = (serviceId) => {
  serviceToDelete.value = serviceId
  deleteDialog.value = true
}

const confirmDelete = async () => {
  if (!serviceToDelete.value) return

  deleting.value = true
  try {
    // await deleteService(serviceToDelete.value) // Uncomment when you have deleteService API
    services.value = services.value.filter(s => s.id !== serviceToDelete.value)
    deleteDialog.value = false
    // You can add a success snackbar here if needed
  } catch (err) {
    console.error('Error deleting service:', err)
    // You can add an error snackbar here if needed
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchServices()
})
</script>

<style scoped>
.service-card {
  transition: transform 0.2s;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.service-card:hover {
  transform: translateY(-5px);
}

.v-card-actions {
  margin-top: auto;
}
</style>
