<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    BarElement, CategoryScale, LinearScale
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import { MOBILE_BREAKPOINT, YEAR_END_MONTH, YEAR_START_MONTH } from '@/config/constants'

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
    startMonth: { type: Number, default: YEAR_START_MONTH },
    endMonth: { type: Number, default: YEAR_END_MONTH },
    monthlyData: {           // 任意にする
        type: Array,
        default: () => []
    }
})

const chartData = ref({ labels: [], datasets: [] })
const chartOptions = ref({})
const isMobile = ref(window.innerWidth < MOBILE_BREAKPOINT)
const errorMessage = ref('')

window.addEventListener('resize', () => {
    isMobile.value = window.innerWidth < MOBILE_BREAKPOINT
})

const fetchData = async () => {
    errorMessage.value = '' // エラーメッセージをリセット
    try {
        const response = await fetch(`${props.apiUrl}?year=${props.year}`)

        if (!response.ok) {
            console.error('HTTP error:', response.status, response.statusText);
            throw new Error(`HTTP error: ${response.status}`);
        }

        const json = await response.json()

        console.log('月別グラフデータ:', json);

        // エラーがある場合は処理を停止
        if (json.error) {
            console.error('API error:', json.error, json.message);
            throw new Error(json.message || json.error);
        }

        // labels と totals が存在するか確認
        if (!json.labels || !json.totals) {
            console.error('Missing required data:', json);
            throw new Error('Missing labels or totals in response');
        }

        // 四半期のみ抽出
        const labels = isMobile.value
            ? json.labels.slice(props.startMonth -1, props.endMonth)
            : json.labels;
        const totals = isMobile.value
            ? json.totals.slice(props.startMonth -1, props.endMonth)
            : json.totals;

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
        errorMessage.value = err.message || 'データの取得に失敗しました'
        // エラー時は空のデータを設定
        chartData.value = { labels: [], datasets: [] }
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
        <div v-if="errorMessage" class="text-red-600 text-center p-4">
            <p class="font-bold">エラーが発生しました</p>
            <p class="text-sm">{{ errorMessage }}</p>
        </div>
        <Bar
            v-else
            :data="chartData"
            :options="{...chartOptions, responsive: true, maintainAspectRatio: false}"
            height="400"
        />
    </div>
</template>
