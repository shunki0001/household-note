<script setup>
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title, Tooltip, Legend,
    BarElement, CategoryScale, LinearScale
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'

// Chart.jsのプラグイン登録
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ChartDataLabels)

// propsを定義
const props = defineProps({
    label: { type: String, default: '月別支出合計' },
    apiUrl: { type: String, default: 'api/chart-data' },
    colors: {
        type: Array,
        default: () => ['#42b983', '#42b983', '#42b983', '#42b983']
    }
})

const chartData = ref({ labels: [], datasets: [] })
const chartOptions = ref(createChartOptions())

// Chartオプション生成関数
function createChartOptions(max = undefined) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        layout: { padding: { top: 20 } },
        plugins: {
            datalabels: {
                anchor: 'end',
                align: 'end',
                formatter: (value) => `¥${value.toLocaleString()}`,
                color: '#333',
                font: { weight: 'bold', size: 12 }
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
                max: max,
                ticks: {
                    callback: (value) => `¥${value.toLocaleString()}`
                }
            }
        },
        aspectRatio: 2
    }
}

// APIからデータ取得
onMounted(async () => {
    try {
        const response = await fetch(props.apiUrl)
        const json = await response.json()

        const maxValue = Math.max(...(json.totals || json.datasets?.flatMap(ds => ds.data) || [0]))
        const adjustedMax = Math.ceil((maxValue + 10000) / 1000) * 1000

        if (json.datasets) {
            // 複数カテゴリ
            chartData.value = {
                labels: json.labels,
                datasets: json.datasets.map((dataset, index) => ({
                    ...dataset,
                    backgroundColor: props.colors[index % props.colors.length]
                }))
            }
        } else {
            // 単一データセット
            chartData.value = {
                labels: json.labels,
                datasets: [
                    {
                        label: props.label,
                        data: json.totals,
                        backgroundColor: [props.colors[0]]
                    }
                ]
            }
        }

        chartOptions.value = createChartOptions(adjustedMax)

    } catch (error) {
        console.error('データ取得エラー:', error)
    }
})
</script>

<template>
    <div style="height: 400px;">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
