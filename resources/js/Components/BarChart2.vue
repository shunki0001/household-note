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
    year: {
        type: Number,
        default: new Date().getFullYear(),
    }
})

const chartRef = ref(null)
let chartInstance = null

// Chart.jsで描画
const renderChart = (labels, datasets, icons) => {
    if (chartInstance) chartInstance.destroy()

    // カスタムプラグインでアイコンを描画
    const iconPlugin = {
        id: 'categoryIcons',
        afterDraw: chart => {
            const { ctx, chartArea, scales } = chart
            const xAxis = scales.x
            const yButton = chartArea.bottom + 1

            // 画面幅に応じてアイコンサイズを変更
            const isMobile = window.innerWidth < 640 // Tailwindのsmサイズ基準
            const iconSize = isMobile ? 16 : 24

            labels.forEach((label, index) => {
                const x = xAxis.getPixelForTick(index)
                const img = new Image()
                img.src = icons[index]
                img.onload = () => {
                    ctx.drawImage(img, x - 7, yButton, iconSize, iconSize)
                }
            })
        }
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
                    ticks: { display: false },
                    title: {
                        display: true,
                        text: ' ',
                    },
                },
            },
        },
        plugins: [iconPlugin],
    })
}

// データ取得
const fetchChartData = async () => {
    try {
        const response = await axios.get(props.apiUrl, {
            params: {
                month: props.month,
                year: props.year,
            },
        });
        const { labels, datasets, icons } = response.data

        // 各データポイントに個別の色を設定
        const coloredDatasets = datasets.map((d, i) => {
            return {
                ...d,
                backgroundColor: Array.isArray(d.data) ? d.data.map((_, dataIndex) => props.colors[dataIndex % props.colors.length]) : props.colors[i % props.colors.length],
                borderColor: Array.isArray(d.data) ? d.data.map((_, dataIndex) => props.colors[dataIndex % props.colors.length]) : props.colors[i % props.colors.length],
                borderWidth: 1,
            };
        })

        renderChart(labels, coloredDatasets, icons)
    } catch (error) {
        console.error('グラフデータの取得に失敗しました:', error)
    }
}

onMounted(fetchChartData)
watch([() => props.month, () => props.year], fetchChartData)
</script>

<template>
    <div class="w-full h-[400px]">
        <canvas ref="chartRef"></canvas>
    </div>
</template>
