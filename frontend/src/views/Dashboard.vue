<!-- frontend/src/views/Dashboard.vue -->
<template>
  <v-container fluid>
    <!-- Header -->
    <v-row class="mb-6">
      <v-col cols="12">
        <div class="d-flex justify-space-between align-center">
          <div>
            <h1 class="text-h4 font-weight-bold primary--text">School Dashboard</h1>
            <p class="text-subtitle-1 grey--text">Welcome to School Management System</p>
          </div>
          <v-btn color="primary" @click="refreshDashboard" :loading="isLoading">
            <v-icon left>mdi-refresh</v-icon>
            Refresh
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <!-- Statistics Cards -->
    <v-row class="mb-6">
      <v-col v-for="stat in statistics" :key="stat.title" cols="12" sm="6" md="4" lg="2">
        <v-card class="stat-card" :color="stat.color" dark elevation="4">
          <v-card-text class="text-center pa-4">
            <v-icon size="48" class="mb-2">{{ stat.icon }}</v-icon>
            <div class="text-h4 font-weight-bold">{{ stat.value }}</div>
            <div class="text-subtitle-1">{{ stat.title }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Charts and Performance -->
    <v-row class="mb-6">
      <!-- Monthly Attendance Chart -->
      <v-col cols="12" md="8">
        <v-card elevation="4" class="rounded-lg">
          <v-card-title class="d-flex justify-space-between align-center">
            <span class="text-h6">Monthly Attendance Trend</span>
          </v-card-title>
          <v-card-text>
            <AttendanceChart :chart-data="monthlyChart" />
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Class Performance -->
      <v-col cols="12" md="4">
        <v-card elevation="4" class="rounded-lg">
          <v-card-title>
            <span class="text-h6">Class Performance</span>
          </v-card-title>
          <v-card-text>
            <v-list dense>
              <v-list-item
                v-for="classStat in classPerformance"
                :key="classStat.class"
                class="mb-2"
              >
                <v-list-item-content>
                  <v-list-item-title class="font-weight-medium">
                    Class {{ classStat.class }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ classStat.student_count }} Students
                  </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                  <v-chip
                    small
                    :color="getPerformanceColor(classStat.attendance_rate)"
                    dark
                  >
                    {{ classStat.attendance_rate }}%
                  </v-chip>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Recent Activities and Quick Stats -->
    <v-row>
      <!-- Recent Activities -->
      <v-col cols="12" md="8">
        <v-card elevation="4" class="rounded-lg">
          <v-card-title class="d-flex justify-space-between align-center">
            <span class="text-h6">Recent Activities</span>
            <v-btn text color="primary" to="/attendance">View All</v-btn>
          </v-card-title>
          <v-card-text>
            <v-timeline align-top dense>
              <v-timeline-item
                v-for="activity in recentActivities"
                :key="activity.id"
                :color="activity.status_color"
                small
              >
                <div class="d-flex justify-space-between">
                  <div>
                    <strong>{{ activity.student_name }}</strong> (Class {{ activity.student_class }})
                    <div class="text-caption">
                      Marked {{ activity.status }} • {{ activity.recorded_by }}
                    </div>
                  </div>
                  <div class="text-caption text-right">
                    {{ activity.time }}
                  </div>
                </div>
              </v-timeline-item>
            </v-timeline>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Quick Actions -->
      <v-col cols="12" md="4">
        <v-card elevation="4" class="rounded-lg">
          <v-card-title>
            <span class="text-h6">Quick Actions</span>
          </v-card-title>
          <v-card-text>
            <v-list dense>
              <v-list-item
                v-for="action in quickActions"
                :key="action.title"
                @click="action.click"
                class="mb-2 quick-action-item"
              >
                <v-list-item-avatar>
                  <v-icon :color="action.color">{{ action.icon }}</v-icon>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title>{{ action.title }}</v-list-item-title>
                  <v-list-item-subtitle>{{ action.subtitle }}</v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                  <v-icon>mdi-chevron-right</v-icon>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useDashboardStore } from '@/stores/dashboard'
import { useAuthStore } from '@/stores/auth'
import AttendanceChart from '@/components/Charts/AttendanceChart.vue'

const router = useRouter()
const dashboardStore = useDashboardStore()
const authStore = useAuthStore()

const stats = computed(() => dashboardStore.stats)
const recentActivities = computed(() => dashboardStore.recentActivities)
const monthlyChart = computed(() => dashboardStore.monthlyChart)
const classPerformance = computed(() => dashboardStore.classPerformance)
const isLoading = computed(() => dashboardStore.isLoading)

const statistics = computed(() => [
  {
    title: 'Total Students',
    value: stats.value.total_students || 0,
    icon: 'mdi-account-group',
    color: 'blue'
  },
  {
    title: 'Total Teachers',
    value: stats.value.total_teachers || 0,
    icon: 'mdi-teach',
    color: 'green'
  },
  {
    title: 'Present Today',
    value: stats.value.present_today || 0,
    icon: 'mdi-check-circle',
    color: 'success'
  },
  {
    title: 'Absent Today',
    value: stats.value.absent_today || 0,
    icon: 'mdi-close-circle',
    color: 'error'
  },
  {
    title: 'Monthly Rate',
    value: `${stats.value.monthly_attendance_rate || 0}%`,
    icon: 'mdi-chart-line',
    color: 'primary'
  },
  {
    title: 'Not Marked',
    value: stats.value.not_marked_today || 0,
    icon: 'mdi-clock-outline',
    color: 'orange'
  }
])

const quickActions = computed(() => {
  const actions = [
    {
      title: 'Take Attendance',
      subtitle: 'Record today\'s attendance',
      icon: 'mdi-clipboard-check',
      color: 'success',
      click: () => router.push('/attendance')
    },
    {
      title: 'View Students',
      subtitle: 'Browse student directory',
      icon: 'mdi-account-multiple',
      color: 'primary',
      click: () => router.push('/students')
    }
  ]

  if (authStore.isAdmin) {
    actions.push({
      title: 'Manage Users',
      subtitle: 'Admin user management',
      icon: 'mdi-account-cog',
      color: 'warning',
      click: () => router.push('/admin/users')
    })
  }

  return actions
})

const getPerformanceColor = (rate) => {
  if (rate >= 90) return 'success'
  if (rate >= 75) return 'primary'
  if (rate >= 60) return 'warning'
  return 'error'
}

const refreshDashboard = async () => {
  await dashboardStore.refreshDashboard()
}

onMounted(() => {
  dashboardStore.loadDashboardData()
})
</script>

<style scoped>
.stat-card {
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.quick-action-item {
  cursor: pointer;
  transition: background-color 0.3s ease;
  border-radius: 8px;
}

.quick-action-item:hover {
  background-color: #f5f5f5;
}
</style>
