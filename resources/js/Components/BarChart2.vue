<script setup>
import { onMounted, ref, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import axios from 'axios'

Chart.register(...registerables)

const props = defineProps({
    apiUrl: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: 'カテゴリー別支出',
    },
    colors: {
        type: Array,
        default: () => [
            '#fbf8cc', '#fde4cf', '#ffcfd2', '#f1c0e8', '#cfbaf0',
            '#a3c4f3', '#90dbf4', '#8eecf5', '#98f5e1', '#b9fbc0',
        ],
    },
    month: {
        type: Number,
        required: true,
    },
})

const chartRef = ref(null)
let chartInstance = null

// カテゴリー名 → 絵文字
const categoryIcons = {
    '食費': '🍎',
    '日用品費': '🧴',
    '交通費': '🚌',
    '住居費': '🏠',
    '水道・光熱費': '💡',
    '通信費': '📱',
    '医療・保険': '💊',
    '娯楽・交通費': '🎮',
    '教育費': '🎓',
    'その他': '🛍️',

}

// Chart.jsで描画
const renderChart = (labels, datasets) => {
    if (chartInstance) chartInstance.destroy()

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
                    font: { size: 18 },
                },
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return `${context.dataset.label}: ¥${context.parsed.y.toLocaleString()}`
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => `¥${value.toLocaleString()}`,
                    },
                    title: {
                        display: true,
                        text: '金額（円）',
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: 'カテゴリー',
                    },
                },
            },
        },
    })
}

// データ取得
const fetchChartData = async () => {
    try {
        const response = await axios.get(`${props.apiUrl}?month=${props.month}`)
        const { labels, datasets } = response.data

        // 🎨 カテゴリーごとに色と絵文字を付与
        const iconLabels = labels.map(label => categoryIcons[label] || label)

        // 各データポイントに個別の色を設定
        const coloredDatasets = datasets.map((d, i) => {
            return {
                ...d,
                backgroundColor: Array.isArray(d.data) ? d.data.map((_, dataIndex) => props.colors[dataIndex % props.colors.length]) : props.colors[i % props.colors.length],
                borderColor: Array.isArray(d.data) ? d.data.map((_, dataIndex) => props.colors[dataIndex % props.colors.length]) : props.colors[i % props.colors.length],
                borderWidth: 1,
            };
        })

        renderChart(iconLabels, coloredDatasets)
    } catch (error) {
        console.error('グラフデータの取得に失敗しました:', error)
    }
}

onMounted(fetchChartData)
watch(() => props.month, fetchChartData)
</script>

<template>
    <div class="w-full h-[400px]">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
