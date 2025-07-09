<script setup>
import { onMounted, ref, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import axios from 'axios'

Chart.register(...registerables)

// props
const props = defineProps({
  apiUrl: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: 'カテゴリー別支出',
  },
  month: {
    type: Number,
    required: true,
  },
})

// chart element
const chartRef = ref(null)
let chartInstance = null

// グラフ描画関数
const renderChart = (labels, datasets) => {
  if (chartInstance) {
    chartInstance.destroy()
  }

  chartInstance = new Chart(chartRef.value, {
    type: 'bar',
    data: {
      labels,
      datasets,
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: true,
          text: `${props.month}月の${props.label}`,
          font: {
            size: 18,
          },
        },
        legend: {
          display: true,
          position: 'top',
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `${context.dataset.label}: ¥${context.parsed.y.toLocaleString()}`
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return `¥${value.toLocaleString()}`
            },
          },
          title: {
            display: true,
            text: '金額（円）',
          },
        },
        x: {
          title: {
            display: true,
            text: '月',
          },
        },
      },
    },
  })
}

// データ取得と描画
const fetchChartData = async () => {
  try {
    const response = await axios.get(`${props.apiUrl}?month=${props.month}`)
    const { labels, datasets } = response.data
    renderChart(labels, datasets)
  } catch (error) {
    console.error('グラフデータの取得に失敗しました:', error)
  }
}

// 初回と月変更時に再取得
onMounted(fetchChartData)
watch(() => props.month, fetchChartData)
</script>

<template>
  <div class="w-full h-[400px]">
    <canvas ref="chartRef"></canvas>
  </div>
</template>
