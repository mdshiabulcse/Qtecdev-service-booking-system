<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <h1 class="text-h4 mb-4">Our Services</h1>

        <!-- Add Service Button (Admin only) -->
        <v-btn
          v-if="authStore.isAdmin"
          color="primary"
          class="mb-4"
          @click="openAddDialog"
        >
          <v-icon start>mdi-plus</v-icon>
          Add New Service
        </v-btn>
      </v-col>

      <!-- Loading State -->
      <v-col v-if="loading" cols="12">
        <v-progress-linear indeterminate color="primary"></v-progress-linear>
      </v-col>

      <!-- Error State -->
      <v-col v-else-if="error" cols="12">
        <v-alert type="error" variant="tonal">
          Failed to load services: {{ error }}
          <v-btn color="error" variant="text" @click="fetchServices">
            Retry
          </v-btn>
        </v-alert>
      </v-col>

      <!-- Empty State -->
      <v-col v-else-if="filteredServices.length === 0" cols="12">
        <v-alert type="info" variant="tonal">
          No services available at the moment.
          <v-btn
            v-if="authStore.isAdmin"
            color="info"
            variant="text"
            @click="openAddDialog"
          >
            Create your first service
          </v-btn>
        </v-alert>
      </v-col>

      <!-- Services List -->
      <template v-else>
        <v-col
          v-for="service in filteredServices"
          :key="service.id"
          cols="12"
          md="6"
        >
          <v-card variant="outlined" class="pa-4">
            <v-row align="center">
              <v-col cols="12" md="8">
                <v-card-title class="text-h6 px-0">{{ service.name }}</v-card-title>
                <v-card-text class="px-0">
                  <div class="text-body-1 mb-2">{{ service.description }}</div>
                  <div class="d-flex align-center gap-2">
                    <span class="font-weight-bold">{{ service.price }}</span>
                    <v-chip
                      small
                      :color="service.status ? 'success' : 'error'"
                    >
                      {{ service.status ? 'Active' : 'Inactive' }}
                    </v-chip>
                  </div>
                </v-card-text>
              </v-col>

              <v-col cols="12" md="4">
                <div class="d-flex flex-column gap-2">
                  <!-- Booking Section -->
                  <template v-if="service.status">
                    <v-dialog v-if="authStore.isAuthenticated" v-model="bookingDialogs[service.id]" max-width="500">
                      <template v-slot:activator="{ props }">
                        <v-btn
                          color="primary"
                          variant="flat"
                          block
                          v-bind="props"
                        >
                          Book Now
                        </v-btn>
                      </template>
                      <v-card>
                        <v-card-title>Book Service</v-card-title>
                        <v-card-text>
                          <v-form ref="bookingForms">
                            <v-menu
                              v-model="dateMenus[service.id]"
                              :close-on-content-click="false"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                            >
                              <template v-slot:activator="{ props }">
                                <v-text-field
                                  v-model="bookingDates[service.id]"
                                  label="Booking Date & Time"
                                  prepend-icon="mdi-calendar"
                                  readonly
                                  v-bind="props"
                                  :rules="[v => !!v || 'Date is required']"
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="bookingDates[service.id]"
                                @update:modelValue="dateMenus[service.id] = false"
                              ></v-date-picker>
                            </v-menu>
                          </v-form>
                        </v-card-text>
                        <v-card-actions>
                          <v-spacer></v-spacer>
                          <v-btn
                            color="grey-darken-1"
                            variant="text"
                            @click="bookingDialogs[service.id] = false"
                          >
                            Cancel
                          </v-btn>
                          <v-btn
                            color="primary"
                            variant="flat"
                            @click="confirmBooking(service.id)"
                            :loading="bookingLoading[service.id]"
                          >
                            Confirm Booking
                          </v-btn>
                        </v-card-actions>
                      </v-card>
                    </v-dialog>
                    <v-btn
                      v-else
                      color="primary"
                      variant="flat"
                      block
                      @click="router.push('/login?redirect=/services')"
                    >
                      Login to Book
                    </v-btn>
                  </template>

                  <!-- Admin Actions -->
                  <template v-if="authStore.isAdmin">
                    <v-btn
                      color="warning"
                      variant="outlined"
                      block
                      @click="openEditDialog(service)"
                    >
                      <v-icon start>mdi-pencil</v-icon>
                      Edit
                    </v-btn>
                    <v-btn
                      color="error"
                      variant="outlined"
                      block
                      @click="openDeleteDialog(service.id)"
                    >
                      <v-icon start>mdi-delete</v-icon>
                      Delete
                    </v-btn>
                  </template>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </template>
    </v-row>

    <!-- Add/Edit Service Dialog -->
    <v-dialog v-model="serviceDialog" max-width="600">
      <v-card>
        <v-card-title class="text-h5">
          {{ isEditing ? 'Edit Service' : 'Add New Service' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="serviceForm" @submit.prevent="saveService">
            <v-text-field
              v-model="currentService.name"
              label="Service Name"
              required
              :rules="[v => !!v || 'Name is required']"
            ></v-text-field>

            <v-textarea
              v-model="currentService.description"
              label="Description"
              required
              :rules="[v => !!v || 'Description is required']"
            ></v-textarea>

            <v-text-field
              v-model="currentService.price"
              label="Price"
              type="number"
              min="0"
              step="0.01"
              required
              :rules="[v => !!v || 'Price is required']"
            ></v-text-field>

            <v-switch
              v-model="currentService.status"
              label="Active Service"
              color="success"
            ></v-switch>
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="grey-darken-1"
            variant="text"
            @click="serviceDialog = false"
          >
            Cancel
          </v-btn>
          <v-btn
            color="primary"
            variant="flat"
            @click="saveService"
            :loading="saving"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

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
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  getServices,
  createService,
  updateService,
  deleteService
} from '@/api/services'
import { createBooking } from '@/api/bookings'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const authStore = useAuthStore()

// Service data
const services = ref([])
const loading = ref(true)
const error = ref(null)

// Filter services based on user role
const filteredServices = computed(() => {
  return authStore.isAdmin
    ? services.value
    : services.value.filter(service => service.status)
})

// Dialog states
const serviceDialog = ref(false)
const deleteDialog = ref(false)
const serviceForm = ref(null)

// Booking related states
const bookingDialogs = ref({})
const bookingDates = ref({})
const dateMenus = ref({})
const bookingLoading = ref({})
const bookingForms = ref({})

// Operation states
const saving = ref(false)
const deleting = ref(false)
const isEditing = ref(false)

// Current service being edited
const currentService = ref({
  name: '',
  description: '',
  price: 0,
  status: true
})

// Service to delete
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

const openAddDialog = () => {
  isEditing.value = false
  currentService.value = {
    name: '',
    description: '',
    price: 0,
    status: true
  }
  serviceDialog.value = true
}

const openEditDialog = (service) => {
  isEditing.value = true
  currentService.value = { ...service }
  serviceDialog.value = true
}

const saveService = async () => {
  const { valid } = await serviceForm.value.validate()
  if (!valid) return

  saving.value = true
  try {
    if (isEditing.value) {
      await updateService(currentService.value.id, currentService.value)
    } else {
      await createService(currentService.value)
    }
    await fetchServices()
    serviceDialog.value = false
  } catch (err) {
    console.error('Error saving service:', err)
    error.value = err.message || 'Failed to save service'
  } finally {
    saving.value = false
  }
}

const openDeleteDialog = (serviceId) => {
  serviceToDelete.value = serviceId
  deleteDialog.value = true
}

const confirmDelete = async () => {
  if (!serviceToDelete.value) return

  deleting.value = true
  try {
    await deleteService(serviceToDelete.value)
    await fetchServices()
    deleteDialog.value = false
  } catch (err) {
    console.error('Error deleting service:', err)
    error.value = err.message || 'Failed to delete service'
  } finally {
    deleting.value = false
  }
}

const confirmBooking = async (serviceId) => {
  if (!bookingDates.value[serviceId]) return

  bookingLoading.value[serviceId] = true
  try {
    await createBooking({
      service_id: serviceId,
      booking_date: bookingDates.value[serviceId],
      status: 'pending'
    })
    bookingDialogs.value[serviceId] = false
    // Show success message
    alert('Booking created successfully!')
  } catch (err) {
    console.error('Error creating booking:', err)
    error.value = err.message || 'Failed to create booking'
  } finally {
    bookingLoading.value[serviceId] = false
  }
}

onMounted(() => {
  fetchServices()
})
</script>

<style scoped>
.v-card {
  transition: box-shadow 0.2s;
}

.v-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>
