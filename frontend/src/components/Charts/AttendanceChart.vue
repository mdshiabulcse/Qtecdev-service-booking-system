<!-- src/components/Charts/AttendanceChart.vue -->
<template>
  <div>
    <canvas ref="chartCanvas" :height="height"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  chartData: {
    type: Array,
    default: () => []
  },
  height: {
    type: Number,
    default: 300
  }
})

const chartCanvas = ref(null)
let chartInstance = null

const createChart = () => {
  if (chartInstance) {
    chartInstance.destroy()
  }

  if (!chartCanvas.value || !props.chartData.length) return

  const ctx = chartCanvas.value.getContext('2d')

  const labels = props.chartData.map(item => {
    const date = new Date(item.date)
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
  })

  const datasets = [
    {
      label: 'Present',
      data: props.chartData.map(item => item.present),
      borderColor: '#4CAF50',
      backgroundColor: 'rgba(76, 175, 80, 0.1)',
      tension: 0.4,
      fill: true
    },
    {
      label: 'Absent',
      data: props.chartData.map(item => item.absent),
      borderColor: '#F44336',
      backgroundColor: 'rgba(244, 67, 54, 0.1)',
      tension: 0.4,
      fill: true
    },
    {
      label: 'Late',
      data: props.chartData.map(item => item.late),
      borderColor: '#FF9800',
      backgroundColor: 'rgba(255, 152, 0, 0.1)',
      tension: 0.4,
      fill: true
    }
  ]

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false
        }
      },
      scales: {
        x: {
          display: true,
          title: {
            display: true,
            text: 'Date'
          }
        },
        y: {
          display: true,
          title: {
            display: true,
            text: 'Number of Students'
          },
          beginAtZero: true
        }
      }
    }
  })
}

watch(() => props.chartData, () => {
  if (props.chartData.length > 0) {
    createChart()
  }
})

onMounted(() => {
  if (props.chartData.length > 0) {
    createChart()
  }
})

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>
