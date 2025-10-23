<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    BarElement, CategoryScale, LinearScale
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ChartDataLabels)

const props = defineProps({
    label: { type: String, default: '月別支出合計' },
    apiUrl: { type: String, default: '/api/chart-data' },
    colors: {
        type: Array,
        default: () => ['#42b983']
    },
    year: {
        type: Number,
        default: new Date().getFullYear()
    },
    startMonth: { type: Number, default: 1 },
    endMonth: { type: Number, default: 12 },
    monthlyData: {           // ← 🔹追加（任意にする）
        type: Array,
        default: () => []
    }
})

const chartData = ref({ labels: [], datasets: [] })
const chartOptions = ref({})
const isMobile = ref(window.innerWidth < 768)

window.addEventListener('resize', () => {
    isMobile.value = window.innerWidth < 768
})

const fetchData = async () => {
    try {
        const response = await fetch(`${props.apiUrl}?year=${props.year}`)
        const json = await response.json()

        // 四半期のみ抽出
        const labels = isMobile.value
            ? json.labels.slice(props.startMonth -1, props.endMonth)
            : json.labels;
        const totals = isMobile.value
            ? json.totals.slice(props.startMonth -1, props.endMonth)
            : json.totals;
        // const labels = json.labels.slice(props.startMonth - 1, props.endMonth)
        // const totals = json.totals.slice(props.startMonth - 1, props.endMonth)

        const maxValue = Math.max(...(totals.length ? totals : [0]))
        const adjustedMax = Math.ceil((maxValue + 10000) / 1000) * 1000

        chartData.value = {
        labels,
        datasets: [
            {
            label: `${props.year}年 ${props.label}`,
            data: totals,
            backgroundColor: props.colors[0],
            }
        ]
        }

        chartOptions.value = {
        responsive: true,
        maintainAspectRatio: false,
        aspectRatio:1,
        plugins: {
            datalabels: {
            anchor: 'end',
            align: 'end',
            formatter: (value) => `¥${value.toLocaleString()}`,
            color: '#333',
            font: { weight: 'bold', size: isMobile.value ? 10 : 12 }
            },
            legend: { display: true },
            tooltip: {
            callbacks: {
                label: (context) => `¥${context.parsed.y.toLocaleString()}`
            }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            max: adjustedMax,
            ticks: {
                callback: (value) => `¥${value.toLocaleString()}`
            }
            }
        }
        }
    } catch (err) {
        console.error('データ取得エラー:', err)
    }
}

onMounted(fetchData)
watch(() => [props.year, props.startMonth, props.endMonth],
    fetchData,
    { deep: true }
)
</script>

<template>
    <div style="height: 400px;">
        <Bar
            :data="chartData"
            :options="{...chartOptions, responsive: true, maintainAspectRatio: false}"
            height="400"
        />
    </div>
</template>
